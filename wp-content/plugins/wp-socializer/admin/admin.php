<?php
/**
  * Main entry point class for admin page
  * 
  **/

defined( 'ABSPATH' ) || exit;

class WPSR_Admin{
    
    public static $pages = array();
    public static $pagehook = 'toplevel_page_wp_socializer';
    public static $current_page = 'home';
    
    public static function init(){
        
        // Register the admin pages
        add_action( 'init', array( __CLASS__, 'register_pages' ) );

        // Register the admin menu
        add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );

        // Enqueue the scripts and styles
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

        // Register action to include admin scripts
        add_action( 'admin_print_scripts', array( __CLASS__, 'inline_scripts' ) );

        // Register the action for admin ajax features
        add_action( 'wp_ajax_wpsr_admin_ajax', array( __CLASS__, 'admin_ajax' ) );

        // Register the action links in plugin list page
        add_filter( 'plugin_action_links_' . WPSR_BASE_NAME, array( __CLASS__, 'action_links' ) );

        // Register the admin notice to inform new version features
        add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );

        add_action( 'plugins_loaded', array( __CLASS__, 'on_activate' ) );

        add_filter( 'admin_footer_text', array( __class__, 'footer_text' ) );

        register_activation_hook( WPSR_BASE_NAME, array( __CLASS__, 'on_activate' ) );

    }
    
    public static function register_pages(){
        
        $init_pages = apply_filters( 'wpsr_register_admin_page', array() );
        $defaults = array(
            'name' => '',
            'banner' => '',
            'description' => '',
            'category' => '',
            'type' => '',
            'form_name' => '',
            'callbacks' => array(
                'page' => false,
                'form' => false,
                'validation' => false,
            )
        );
        
        foreach( $init_pages as $id => $config ){

            $config = WPSR_Lists::set_defaults( $config, $defaults );
            self::$pages[ $id ] = $config;

            // Register the validation filter for the form
            if( $config[ 'callbacks' ][ 'validation' ] ){
                add_filter( 'wpsr_form_validation_' . $config[ 'form_name' ], $config[ 'callbacks' ][ 'validation' ] );
            }
            
        }
        
    }

    public static function get_pages(){
        return apply_filters( 'wpsr_mod_admin_pages', self::$pages );
    }

    public static function admin_menu(){
        
        $pages = self::get_pages();
        $icon = WPSR_ADMIN_URL . 'images/icons/wp-socializer-sm.png';
        
        add_menu_page( 'WP Socializer - Admin page', 'WP Socializer', 'manage_options', 'wp_socializer', array( __CLASS__, 'admin_page' ), $icon );
        
        add_submenu_page( 'wp_socializer', 'WP Socializer - Admin page', 'Home', 'manage_options', 'wp_socializer', array( __CLASS__, 'admin_page' ) );
        
        foreach( $pages as $id => $config ){
            if( empty( $config[ 'link' ] ) ){
                add_submenu_page( 'wp_socializer', 'WP Socializer - ' . $config[ 'name' ], $config[ 'name' ], 'manage_options', 'wp_socializer&tab="' . $id . '"', array( __CLASS__, 'admin_page' ) );
            }
        }
    }

    public static function admin_page(){

        if( !current_user_can( 'manage_options' ) ){
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        $pages = self::get_pages();
        self::$current_page = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

        // Set default page
        if( empty( self::$current_page ) || !array_key_exists( self::$current_page, $pages ) ){
            self::$current_page = 'home';
        }

        echo '<div class="wrap page_' . self::$current_page . '">';

            self::admin_header();

            echo '<div id="main">';
                self::admin_content();
                self::admin_sidebar();
            echo '</div>';

        echo '</div>';

        self::intro_popups();

    }

    public static function admin_header(){
        
        echo '<header id="wpsr_header">';
        echo '<hgroup>';
        echo '<h1 class="wpsr_title">WP Socializer 
        <span class="title-count">' . WPSR_VERSION . '</span><a href="admin.php?page=wp_socializer" class="back_btn"><i class="fa fa-chevron-left"></i> Back</a></h1>';
        self::admin_links();
        echo '</hgroup>';
        echo '</header>';
        
    }
    
    public static function admin_content(){

        $pages = self::get_pages();

        echo '<div id="content">';

        if(self::$current_page == 'home'){

            echo '<h1><i class="fa fa-star-of-life"></i>Features</h1>';
            self::admin_pages_list( 'feature' );

            echo '<h1><i class="fas fa-columns"></i>Widgets</h1>';
            self::admin_pages_list( 'widget' );

            echo '<h1><i class="fa fa-wrench"></i>Settings</h1>';
            self::admin_pages_list( 'other' );

        }else{

            $page = $pages[ self::$current_page ];
            echo '<div class="page_head">';
            echo '<h1><i class="fa fa-star-of-life"></i>' . $page[ 'name' ] . '</h1>';
            if( !empty( $page[ 'description' ] ) ) echo '<p>' . $page[ 'description' ] . '</p>';
            echo '</div>';

            call_user_func( $pages[ self::$current_page ][ 'callbacks' ][ 'page' ] );
        }

        self::coffee_box();

        echo '</div>';

    }

    public static function admin_pages_list( $category = false ){

        $pages = self::get_pages();

        echo '<div class="admin_pages_list">';

        foreach( $pages as $id => $config ){
            
            if( $config[ 'category' ] != $category ){
                continue;
            }

            $is_feature_active = false;
            $action_text = __( 'Open', 'wpsr' );
            $link = empty( $config['link'] ) ? ( 'admin.php?page=wp_socializer&tab=' . $id ) : $config[ 'link' ];

            if( $config[ 'type' ] == 'feature' ){
                $feat_settings = get_option( 'wpsr_' . $config['form_name'], array() );
                $feat_settings = WPSR_Lists::set_defaults( $feat_settings, WPSR_Options::default_values( $id ) );
                $is_feature_active = ( isset( $feat_settings[ 'ft_status' ] ) && $feat_settings[ 'ft_status' ] == 'enable' ) ? true : false;
                $action_text = $is_feature_active ? '<i class="fa fa-check"></i>' . __( 'Active', 'wpsr' ) : __( 'Inactive', 'wpsr' );
            }

            if( $config[ 'type' ] == 'widget' ){
                $action_text = __( 'Add widget', 'wpsr' );
            }

            $card_class = array(
                'page_card',
                'card_' . $id,
                ( $is_feature_active ? 'active' : '' )
            );

            echo '<a class="' . implode( ' ', $card_class ) . '" href="' . $link . '">';
            echo '<div class="card_banner" style="background-image: url(' . $config[ 'banner' ] . '?v=' . WPSR_VERSION . ')"></div>';
            echo '<div class="card_info">';
            echo '<h3>' . $config[ 'name' ] . '</h3>';
            if( !empty( $config[ 'description' ] ) ) echo '<p>' . $config[ 'description' ] . '</p>';
            echo '</div>';
            echo '<div class="page_feat_status">' . $action_text . '</div>';
            echo '</a>';
            
        }
        echo '</div>';
        
    }
    
    public static function settings_form( $id = '' ){

        if( empty( $id ) )
            return;

        $pages = self::get_pages();
        $page = $pages[ $id ];

        $form_name = $page[ 'form_name' ];
        $form_callback = $page[ 'callbacks' ][ 'form' ];
        
        $option = 'wpsr_' . $form_name;
        $nonce = 'wpsr_nonce_' . $form_name . '_submit';
        $form_fields = 'wpsr_form_' . $form_name;
        $validation_filter = 'wpsr_form_validation_' . $form_name;
        
        // Form post
        if( $_POST && check_admin_referer( $nonce ) ){
            
            $post = self::clean_post();
            $post_value = apply_filters( $validation_filter, $post );
            
            update_option( $option, $post_value );
            
            echo '<div class="notice notice-success inline is-dismissible save_notice">';
            echo '<p>' . __( 'Settings saved successfully ! ', 'wpsr' );
            echo '<a href="' . get_site_url() . '" target="_blank">' . __( 'Visit site', 'wpsr' ) . ' <i class="fas fa-arrow-right"></i></a></p>';
            echo '<hr />';
            echo '<p>📢 <a href="https://twitter.com/intent/tweet?hashtags=wordpress,plugin,facebook,twitter,addtoany,addthis&ref_src=twsrc%5Etfw&related=aakashweb&text=Check%20out%20WP%20Socializer%20⚡%20a%20free%20all%20in%20one%20plugin%20to%20add%20social%20sharing%20buttons%2C%20profile%20links%2C%20sticky%20share%20bar%20and%20widgets%20to%20your%20WordPress%20site&tw_p=tweetbutton&url=https%3A%2F%2Fwww.aakashweb.com%2Fwordpress-plugins%2Fwp-socializer%2F&via=aakashweb" target="_blank">Tweet, share and help others try this plugin <i class="fas fa-arrow-right"></i></a></p>';
            echo '</div>';
        }
        
        // Get saved details
        $saved_settings = get_option( $option );

        echo '<form method="post" id="' . $form_name . '" class="main_form">';
            
            // Execute all hooked form fields from services
            if( is_callable( $form_callback ) ){
                call_user_func( $form_callback, $saved_settings );
            }
            
            do_action( 'wpsr_form_' . $form_name, $saved_settings );
            
            wp_nonce_field( $nonce );
        
        echo '<div class="main_form_footer postbox"><input type="submit" value="' . __( 'Save settings', 'wpsr' ) . '" class="button button-primary" /></div>';
        
        echo '</form>';
        
    }
    
    public static function enqueue_scripts( $hook ){
        
        if( self::$pagehook == $hook ){
            wp_enqueue_style( 'wpsr_css', WPSR_ADMIN_URL . 'css/style.css', array(), WPSR_VERSION );
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'wpsr_ipopup', WPSR_ADMIN_URL . 'css/ipopup.css', array(), WPSR_VERSION );
            wp_enqueue_style( 'wpsr_fa', WPSR_Lists::ext_res( 'font-awesome-adm' ), array(), WPSR_VERSION );
            
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-conditioner', WPSR_ADMIN_URL . 'js/jquery.conditioner.js', array( 'jquery' ) );
            wp_enqueue_script( 'wp-color-picker' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            wp_enqueue_script( 'jquery-ui-draggable' );
            wp_enqueue_script( 'wpsr_ipopup', WPSR_ADMIN_URL . 'js/ipopup.js', array(), WPSR_VERSION );
            wp_enqueue_script( 'wpsr_js', WPSR_ADMIN_URL . 'js/script.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-conditioner', 'wp-color-picker', 'wpsr_ipopup' ), WPSR_VERSION );
            
        }
        
    }
    
    public static function inline_scripts(){
        
        $screen = get_current_screen();
        
        if( self::$pagehook == $screen->id ){
            
            $loc_rules = WPSR_Location_Rules::rules_list();
            
            $js_texts = array(
                'sel_btn' => __( 'Please select a service to create button for !', 'wpsr' ),
                'del_btn' => __( 'Are you sure want to delete this button ?', 'wpsr' ),
                'close' => __( 'Close', 'wpsr' ),
                'fb_empty' => __( 'No buttons are added. Open the editor to add buttons.', 'wpsr' )
            );
            
            echo '<script>
            var wpsr = {
                ajaxurl: "' . get_admin_url() . 'admin-ajax.php",
                loc_rules: ' . wp_json_encode( $loc_rules ) . ',
                js_texts: ' . wp_json_encode( $js_texts ) . ',
                ext_res: ' . wp_json_encode( WPSR_Lists::ext_res() ) . ',
            };
            </script>';
            
            echo '<script>
            var wpsr_show = {
                changelog: "' . ( ( get_option( 'wpsr_last_changelog' ) != WPSR_VERSION ) ? WPSR_VERSION : 'false'  ) . '"
            }
            </script>';
        }
        
    }
    
    public static function on_activate(){
        
        $prev_version = get_option( 'wpsr_version' );

        if( WPSR_VERSION != $prev_version ){
            update_option( 'wpsr_version', WPSR_VERSION );
        }
        
        if( !get_option( 'wpsr_since' ) ){
            add_option( 'wpsr_since', time() );
        }

    }

    public static function admin_ajax(){
        
        $get = self::clean_get();
        $do = $get[ 'do' ];
        
        if( $do == 'close_changelog' ){
            update_option( 'wpsr_last_changelog', WPSR_VERSION );
            echo 'done';
        }

        die( 0 );
        
    }
    
    public static function clean_post(){
        
        return stripslashes_deep( $_POST );
        
    }
    
    public static function clean_attr( $a ){
        
        foreach( $a as $k=>$v ){
            if( is_array( $v ) ){
                $a[ $k ] = self::clean_attr( $v );
            }else{
                
                if( in_array( $k, array( 'custom', 'tip', 'helper' ) ) )
                    continue;
                
                $a[ $k ] = esc_attr( $v );
            }
        }
        
        return $a;
    }
    
    public static function clean_get(){
        
        foreach( $_GET as $k=>$v ){
            $_GET[$k] = sanitize_text_field( $v );
        }

        return $_GET;
    }
    
    public static function action_links( $links ){
        array_unshift( $links, '<a href="'. esc_url( admin_url( 'admin.php?page=wp_socializer') ) .'">Settings</a>' );
        return $links;
    }
    
    public static function admin_notices(){
        
        $pages_display = array( 'plugins', 'update-core', 'dashboard' );
        
        if( in_array( get_current_screen()->id, $pages_display ) ){
            if( version_compare( WPSR_VERSION, get_option( 'wpsr_last_changelog' ), '>' ) ){
                echo '<div class="notice notice-success is-dismissible">
                    <p>✨ ' . __( '<b>WP Socializer</b> plugin is updated to the latest version', 'wpsr') . ' <code>' . WPSR_VERSION . '</code></p>
                    <p>' . __( 'Please read the changelog in the settings page.', 'wpsr') . '</p>
                    <p><a href="' . esc_url( admin_url( 'admin.php?page=wp_socializer') ) . '" class="button button-primary">' . __( 'View change log', 'wpsr' ) . '</a></p>
                </div>';
            }
        }
    }
    
    public static function admin_links(){
        echo '<ul class="admin_links">';
            echo '<li class="slogan">⚡ Your all in one social sharing plugin</li>';
            echo '<li><a href="#"><i class="fas fa-bars"></i> Features</a>';
                echo '<ul class="sub_menu">';
                $pages = self::get_pages();
                foreach( $pages as $id => $config ){
                    $link = empty( $config['link'] ) ? ( 'admin.php?page=wp_socializer&tab=' . $id ) : $config[ 'link' ];
                    echo '<li><a href="' . $link . '">' . $config[ 'name' ] . '</a></li>';
                }
                echo '</ul>';
            echo '</li>';
            echo '<li><a href="https://www.paypal.me/vaakash/6" target="_blank">Buy me a coffee</a></li>';
            echo '<li><a href="https://wordpress.org/support/plugin/wp-socializer/reviews/?rate=5#new-post" target="_blank">Rate this plugin</a></li>';
        echo '</ul>';
    }
    
    public static function footer_text( $text ){

        $screen = get_current_screen();

        if( self::$pagehook == $screen->id ){
            return '<img src="' . WPSR_ADMIN_URL . '/images/icons/aakash-web.png" alt="Aakash Web" /> Thank you for using WP Socializer. Created by <a href="https://www.aakashweb.com" target="_blank">Aakash Chakravarthy</a>. More <a href="https://www.aakashweb.com/wordpress-plugins/" target="_blank">WordPress plugins</a>';
        }

        return $text;

    }

    public static function admin_sidebar(){

        echo '<div id="sidebar">';

        echo '<div class="side_card">';
        echo '<h2><i class="fas fa-lightbulb"></i> Floating/sticky widgets</h2>';
        echo '<a class="side_banner" href="https://www.aakashweb.com/wordpress-plugins/ultimate-floating-widgets/?utm_source=wp-socializer&utm_medium=sidebar&utm_campaign=ufw" target="_blank"><img src="' . WPSR_ADMIN_URL . '/images/banners/ultimate-floating-widgets.png" /></a>';
        echo '<p>If your website does not have a sidebar, but still want to use widgets then you can try Ultimate floating widgets plugin. It creates a sticky popup where you can place your widgets.</p>';
        echo '<a class="cta_link" href="https://www.aakashweb.com/wordpress-plugins/ultimate-floating-widgets/?utm_source=wp-socializer&utm_medium=sidebar&utm_campaign=ufw" target="_blank">Learn more <i class="fas fa-arrow-right"></i></a>';
        echo '</div>';

        echo '<div class="side_card">';
        echo '<h2><i class="fas fa-info-circle"></i> Get updates</h2>';
        echo '<p>Get updates on the WordPress plugins, tips and tricks to enhance your WordPress experience. No spam.</p>';

    echo '<form class="subscribe_form" action="https://aakashweb.us19.list-manage.com/subscribe/post?u=b7023581458d048107298247e&amp;id=ef5ab3c5c4" method="post" name="mc-embedded-subscribe-form" target="_blank" novalidate>
        <input type="email" value="" name="EMAIL" class="required subscribe_email_box" id="mce-EMAIL" placeholder="Your email address">
        <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_b7023581458d048107298247e_ef5ab3c5c4" tabindex="-1" value=""></div>
        <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button subscribe_btn">
    </form>';

        echo '<a href="https://www.facebook.com/aakashweb" target="_blank" class="cta_link">Follow me on Facebook <i class="fas fa-arrow-right"></i></a>';
        echo '<a href="https://www.twitter.com/aakashweb" target="_blank" class="cta_link">Follow me on Twitter <i class="fas fa-arrow-right"></i></a>';
        echo '</div>';

        echo '<div class="side_card">';
        echo '<h2><i class="fas fa-life-ring"></i> Help &amp; Support</h2>';
        echo '<p>Got any issue or not sure how to achieve what you are looking for with the plugin or have any idea or missing feature ? Let me know. Please post a topic in the forum for an answer.</p>';
        echo '<a class="cta_link" href="https://www.aakashweb.com/forum/discuss/wordpress-plugins/wp-socializer/" target="_blank">Visit the support forum <i class="fas fa-arrow-right"></i></a>';
        echo '</div>';

        echo '</div>';

    }

    public static function intro_popups(){

        echo '<div class="welcome_wrap intro_popup style_ele">
        <section></section>
        <footer><button class="button button-primary close_changelog_btn">' . __( 'Start using WP Socializer', 'wpsr' ) . '</button> <a href="https://twitter.com/intent/tweet?hashtags=wordpress,plugin,facebook,twitter,addtoany,addthis&ref_src=twsrc%5Etfw&related=aakashweb&text=Check%20out%20WP%20Socializer%20⚡%20a%20free%20all%20in%20one%20plugin%20to%20add%20social%20sharing%20buttons%2C%20profile%20links%2C%20sticky%20share%20bar%20and%20widgets%20to%20your%20WordPress%20site&tw_p=tweetbutton&url=https%3A%2F%2Fwww.aakashweb.com%2Fwordpress-plugins%2Fwp-socializer%2F&via=aakashweb" class="button" target="_blank">Share on Twitter</a></footer>
        </div>';

    }
    
    public static function coffee_box(){

        echo '<div class="happy_box">';
        
        echo '<h2>Happy with the plugin ?</h2>';

        echo '<div class="cfe_bottom">';
        echo '<div class="icon">☕</div>';
        echo '<h3>Buy me a Coffee !</h3><p>If you like this plugin, buy me a coffee and help support this plugin !</p>';
        echo '<div class="cfe_form">';
        echo '<select class="cfe_amt">';
        for($i = 5; $i <= 15; $i++){
            echo '<option value="' . $i . '" ' . ($i == 6 ? 'selected="selected"' : '') . '>$' . $i . '</option>';
        }
        echo '<option value="">Custom</option>';
        echo '</select>';
        echo '<a class="button button-primary cfe_btn" href="https://www.paypal.me/vaakash/6" data-link="https://www.paypal.me/vaakash/" target="_blank">' . __( 'Buy me a coffee !', 'wpsr' ) . '</a>';
        echo '</div>';
        echo '</div>';

        echo '<div class="rate_review">';
        echo '<div class="icon">⭐</div>';
        echo '<h3>Rate &amp; Review</h3><p>If you have a minute, please rate this plugin and let others know and try this plugin.</p>';
        echo '<a href="https://wordpress.org/support/plugin/wp-socializer/reviews/?rate=5#new-post" class="button button-primary" target="_blank">Rate &amp; Review <i class="fas fa-arrow-right"></i></a>';
        echo '</div>';

        echo '</div>';

    }

}

WPSR_Admin::init();

?>