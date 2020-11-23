<?php
/**
  * Share counter class
  * 
  */

defined( 'ABSPATH' ) || exit;

class WPSR_Share_Counter{
    
    public static function init(){
        
        add_action( 'wp_ajax_wpsr_share_count', array( __CLASS__, 'ajax_count' ) );
        add_action( 'wp_ajax_nopriv_wpsr_share_count', array( __CLASS__, 'ajax_count' ) );
        
    }
    
    public static function counter_services(){
        
        return apply_filters( 'wpsr_mod_counter_services', array(
            'facebook' => array(
                'name' => 'Facebook',
                'callback' => array( __CLASS__, 'facebook_count' )
            ),
            'pinterest' => array(
                'name' => 'Pinterest',
                'callback' => array( __CLASS__, 'pinterest_count' )
            )
        ));
        
    }
    
    public static function remote_request_json( $api_url, $method = 'get', $args = array() ){
        
        if( $method == 'get' ){
            $response = wp_remote_get( $api_url );
        }elseif( $method == 'post' ){
            $response = wp_remote_post( $api_url, $args );
        }else{
            return 0;
        }
        
        if( is_wp_error( $response ) ){
            return false;
        }else{
            if( $response[ 'response' ][ 'code' ] == 200 ){
                $data = json_decode( wp_remote_retrieve_body( $response ) );
                return $data;
            }else{
                return false;
            }
        }
    }
    
    public static function service_count( $id, $url ){
        
        $counter_services = self::counter_services();
        $gs = WPSR_Lists::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );
        
        if( !array_key_exists( $id, $counter_services ) ){
            return 0;
        }
        
        $link_md5 = md5( $url );
        $transient_name = 'wpsr_count_' . $link_md5;
        $callback = $counter_services[ $id ][ 'callback' ];
        $expiration = $gs[ 'counter_expiration' ];
        
        $link_counts = get_transient( $transient_name );
        
        if( empty( $link_counts ) || !array_key_exists ( $id, $link_counts ) ){
            $count = call_user_func( $callback, $url );
            
            // Some error while getting the count
            if( $count == false ){
                return 0;
            }

            if( is_array( $link_counts ) ){
                $link_counts[ $id ] = $count;
            }else{
                $link_counts = array(
                    $id => $count
                );
            }
            
            set_transient( $transient_name, $link_counts, $expiration );
            return $count;
            
        }else{
            return $link_counts[ $id ];
        }
        
    }
    
    public static function get_count( $id, $url ){
        
        $gs = WPSR_Lists::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );
        $count = 0;
        
        if( $gs[ 'counter_both_protocols' ] == 'yes' ){ //TODO add general settings
            
            $url_https = str_replace('http://', 'https://', $url);
            $url_http = str_replace('https://', 'http://', $url_https);
            
            $count_http = self::service_count( $id, $url_http );
            $count_https = self::service_count( $id, $url_https );
            $count = $count_http + $count_https;
            
        }else{
            
            $count = self::service_count( $id, $url );
            
        }
        
        $formatted = self::format_count( $count );
        
        return array(
            'full' => $count,
            'formatted' => $formatted
        );
        
    }
    
    public static function total_count( $url, $services = array() ){
        
        $counter_services = self::counter_services();
        $count = 0;
        
        foreach( $services as $id ){
            if( array_key_exists( $id, $counter_services ) ){
                $service_count = self::get_count( $id, $url );
                $count += $service_count[ 'full' ];
            }
        }
        
        $formatted = self::format_count( $count );
        
        return array(
            'full' => $count,
            'formatted' => $formatted
        );
        
    }
    
    public static function ajax_count(){
        
        if( $_POST && isset( $_POST[ 'data' ] ) ){
            
            $p = WPSR_Admin::clean_post();
            $data = json_decode( $p[ 'data' ], true );
            
            if( $data !== false && isset( $data[ 'url' ] ) && isset( $data[ 'services' ] ) ){
                
                if(ob_get_length() > 0) {
                    ob_clean();
                }
                
                $out = self::ajax_request( $data[ 'url' ], $data[ 'services' ] );
                
                header( 'Access-Control-Allow-Origin: ' . esc_url_raw( site_url() ) );
                wp_send_json( $out );
                
            }else{
                wp_send_json( array() );
            }
            
        }
        
        die(0);
        
    }
    
    public static function ajax_request( $url, $services ){
        
        $ret = array();
        
        if( count( $services ) == 0 ){
            return $ret;
        }
        
        $url_parse = parse_url( $url );
        $site_parse = parse_url( get_site_url() );
        
        /*
        if( $url_parse[ 'host' ] != $site_parse[ 'host' ] ){
            return $ret;
        }*/
        
        foreach( $services as $sid ){
            $count = self::get_count( $sid, $url );
            $ret[ $sid ] = $count[ 'full' ];
        }
        
        return $ret;
        
    }
    
    public static function placeholder( $url, $services ){
        
        $services = (array) $services;
        
        // Always get share count from ajax in if caching plugin is enabled
        if( ( defined( 'WP_CACHE' ) && WP_CACHE ) || !self::is_cached( $url, $services ) ){
            return '<span data-wpsrs="' . esc_attr( $url ) . '" data-wpsrs-svcs="' . join( ',', $services ) . '">0</span>';
        }else{
            $count = self::total_count( $url, $services );
            return '<span data-wpsrs-cached="true">' . $count[ 'formatted' ] . '</span>';
        }
        
    }
    
    public static function is_cached( $url, $services ){
        
        $link_md5 = md5( $url );
        $transient_name = 'wpsr_count_' . $link_md5;
        $link_counts = get_transient( $transient_name );
        $counter_services = self::counter_services();
        
        if( $link_counts === false ){
            return false;
        }
        
        if( !is_array( $services ) ){
            $services = explode( ',', $services );
        }
        
        foreach( $services as $sid ){
            if( !array_key_exists( $sid, $counter_services ) ){
                continue;
            }

            if( !array_key_exists( $sid, $link_counts ) ){
                return false;
            }
        }
        
        return true;

    }

    public static function total_count_html( $settings, $page_info ){

        $default_values = array(
            'text' => 'Shares',
            'counter_color' => '#000',
            'add_services' => array(),
            'size' => '32px'
        );

        $settings = WPSR_Lists::set_defaults( $settings, $default_values );
        $html = '';

        $total_holder = self::placeholder( $page_info[ 'url' ], $settings[ 'add_services' ] );

        $classes = array( 'wpsr-counter', 'wpsrc-sz-' . $settings[ 'size' ] );

        $style = '';
        if( $settings[ 'counter_color' ] != '' ){
            $style = 'style="color:' . $settings['counter_color'] . '"';
        }

        $html .= '<div class="' . implode( ' ', $classes ) . '" ' . $style . '>';
        $html .= '<span class="scount">' . $total_holder . '</span>';
        $html .= '<small class="stext">' . $settings[ 'text' ] . '</small>';
        $html .= '</div>';

        return $html;

    }
    
    public static function facebook_count( $url ){
        
        $gs = WPSR_Lists::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );
        $fb_app_id = $gs[ 'facebook_app_id' ];
        $fb_app_secret = $gs[ 'facebook_app_secret' ];
        $access_token = '';
        
        if( !empty( $fb_app_id ) && !empty( $fb_app_secret ) ){
            $access_token = $fb_app_id . '|' . $fb_app_secret;
        }else{
            return false;
        }
        
        $api = 'https://graph.facebook.com/?id=' . $url . '&fields=engagement&access_token=' . $access_token;
        $data = self::remote_request_json( $api );
        
        if( $data == false ){
            return false;
        }else{
            $reaction_count = isset( $data->engagement->reaction_count ) ? intval( $data->engagement->reaction_count ) : 0;
            $comment_count = isset( $data->engagement->comment_count ) ? intval( $data->engagement->comment_count ) : 0;
            $share_count = isset( $data->engagement->share_count ) ? intval( $data->engagement->share_count ) : 0;
            $sum = $reaction_count + $comment_count + $share_count;
            return $sum;
        }
        
    }
    
    public static function pinterest_count( $url ){
        
        $api = 'https://api.pinterest.com/v1/urls/count.json?callback=wpsr&url=' . $url;
        $response = wp_remote_get( $api );
        
        if( is_wp_error( $response ) ){
            return false;
        }else{
            if( $response[ 'response' ][ 'code' ] == 200 ){
                $data = self::jsonp_decode( wp_remote_retrieve_body( $response ) );
                
                if( isset( $data->count ) ){
                    return $data->count;
                }else{
                    return 0;
                }
                
            }else{
                return 0;
            }
        }
        
    }
    
    public static function format_count( $num ){
        
        if( $num < 1000 )
            return $num;
        
        $suffixes = array( 'k', 'm', 'b', 't' );
        $final = $num;

        for( $i=0; $i<sizeof($suffixes); $i++ ){
            $num = $num/1000;
            
            if( $num > 1000 ){
                continue;
            }else{
                $final = round( $num, 2 ) . $suffixes[$i];
                break;
            }
        }
        
        return $final;
    }
    
    public static function jsonp_decode( $jsonp ) { // PHP 5.3 adds depth as third parameter to json_decode
        if($jsonp[0] !== '[' && $jsonp[0] !== '{') { // we have JSONP
           $jsonp = substr($jsonp, strpos($jsonp, '('));
        }
        return json_decode( trim($jsonp,'();') );
    }

    public static function admin_note(){

        $counter_services = self::counter_services();
        $services = array_keys( $counter_services );
        $services_text = implode( ', ', $services );

        $gs = WPSR_Lists::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );
        echo '<div class="note">';
        echo '<ul>';
        echo '<li>' . __( 'Share count is supported only for sites: ', 'wpsr' ) . '<b>' . $services_text . '</b>' . '</li>';
        echo '<li>' . __( 'Share counts are cached for every page. You can review the cache configuration in the settings page if needed.', 'wpsr' ) . '</li>';
        if( empty( $gs[ 'facebook_app_id' ] ) || empty( $gs[ 'facebook_app_secret' ] ) ){
            echo '<li>' . __( '<b>Facebook API details are not set</b>: To display facebook like count, please fill in the facebook API information in the settings page.', 'wpsr' ) . '</li>';
        }
        echo '</ul>';
        echo '<p><a href="' . admin_url( 'admin.php?page=wp_socializer&tab=general_settings' ) . '" target="_blank" class="button button-primary">' . __( 'Open settings', 'wpsr' ) . '</a></p>';
        echo '</div>';

    }

}

WPSR_Share_Counter::init();

?>