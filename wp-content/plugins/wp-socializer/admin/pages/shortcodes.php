<?php
/**
 * Shortcodes admin page
 *
 **/

defined( 'ABSPATH' ) || exit;

class WPSR_Admin_Shortcodes{
    
    function __construct(){
        
        add_filter( 'wpsr_register_admin_page', array( $this, 'register' ) );
        
    }
    
    function register( $pages ){

        $pages[ 'shortcodes' ] = array(
            'name' => __( 'Shortcodes', 'wpsr' ),
            'banner' => WPSR_ADMIN_URL . '/images/banners/shortcodes.svg',
            'description' => __( 'Create shortcodes for social sharing icons and follow icons to use them in any custom location.', 'wpsr' ),
            'category' => 'feature',
            'type' => 'shortcodes',
            'callbacks' => array(
                'page' => array( $this, 'page' )
            )
        );
        
        return $pages;
        
    }
    
    function page(){
        
        $form = new WPSR_Form();

        $form->section_start();

        $form->tab_list(array(
            'share_icons' => '<i class="fas fa-share-alt"></i>' . __( 'Share Icons', 'wpsr' ),
            'follow_icons' => '<i class="fas fa-user-plus"></i>' . __( 'Follow Icons', 'wpsr' ),
            'share_link' => '<i class="fas fa-link"></i></i>' . __( 'Share link', 'wpsr' )
        ));

        echo '<div class="tab_wrap">';
        $this->tab_share_icons();
        $this->tab_follow_icons();
        $this->tab_share_link();
        echo '</div>';

        echo '<h3>' . __( 'Save your shortcodes', 'wpsr' ) . '</h3>';
        echo '<p>' . __( 'Save the shortcodes you created with a shortcode creation and management plugin like "Shortcoder" and insert them easily in posts whenever needed.' ) . '</p>';
        if( class_exists( 'Shortcoder' ) && is_plugin_active( 'shortcoder/shortcoder.php' ) ){
            echo '<p><a href="' . admin_url( 'post-new.php?post_type=shortcoder' ) . '" target="_blank" class="button button-primary">' . __( 'Open shortcoder', 'wpsr' ) . '</a></p>';
        }else{
            if( function_exists( 'add_thickbox' ) ){
                add_thickbox();
            }
            echo '<p><a href="' . admin_url( 'plugin-install.php?tab=plugin-information&plugin=shortcoder&TB_iframe=true&width=700&height=550' ) . '" class="button button-primary thickbox">' . __( 'Learn more', 'wpsr' ) . '</a></p>';
        }

        echo '<h3>' . __( 'Using in theme', 'wpsr' ) . '</h3>';
        echo '<p>' . __( 'To use the shortcode anywhere in your theme, use the below PHP snippet and replace the shortcode with the plugin shortcode.', 'wpsr' ) . '</p>';
        echo '<pre>&lt;?php echo do_shortcode( \'THE_SHORTCODE\' ); ?&gt;</pre>';

        $form->section_end();

    }

    function tab_share_icons(){

        echo '<div data-tab="share_icons">';

        echo '<h3>' . __( 'Syntax', 'wpsr' ) . '</h3>';
        echo '<pre>[wpsr_share_icons parameter1="value" parameter2="value" ...]</pre>';

        echo '<h3>' . __( 'Example', 'wpsr' ) . '</h3>';
        echo '<pre>[wpsr_share_icons icons="facebook,twitter,pinterest,email" icon_size="40px" icon_bg_color="red" icon_shape="drop"]</pre>';

        echo '<h3>' . __( 'Parameter reference', 'wpsr' ) . '</h3>';
        $options = WPSR_Options::share_icons();

        unset( $options[ 'selected_icons' ] );
        unset( $options[ 'loc_rules' ] );
        unset( $options[ 'position' ] );
        unset( $options[ 'in_excerpt' ] );
        unset( $options[ 'heading' ] );
        unset( $options[ 'custom_html_above' ] );
        unset( $options[ 'custom_html_below' ] );

        echo '<table class="widefat">
            <thead>
                <tr>
                    <th>' . __( 'Parameter', 'wpsr' ) . '</th>
                    <th>' . __( 'Default value', 'wpsr' ) . '</th>
                    <th>' . __( 'Description', 'wpsr' ) . '</th>
                    <th>' . __( 'Supported values', 'wpsr' ) . '</th>
                </tr>
            </thead>
            <tbody>
        ';

        // Adding icons to the param list
        $icons_param = array(
            'icons' => array(
                'default' => 'facebook,twitter,linkedin,pinterest,email',
                'options' => false,
                'description' => __( 'The ID share icons to display separated by comma. See list below for icon IDs.', 'wpsr' )
            ),
            'template' => array(
                'default' => '<empty>',
                'options' => array( '1' => '1', '2' => '2' ),
                'description' => __( 'The ID of the template which is configured in the share icons feature settings page. When this is provided, other configurations are NOT considered. Use this parameter only to use the saved configuration in a custom location.', 'wpsr' )
            )
        );
        $options = $icons_param + $options;

        $options[ 'page_url' ] = array(
            'default' => __( 'The current post URL', 'wpsr' ),
            'description' => __( 'The URL to share', 'wpsr' ),
            'options' => false
        );
        $options[ 'page_title' ] = array(
            'default' => __( 'The current post URL', 'wpsr' ),
            'description' => __( 'The title of the URL', 'wpsr' ),
            'options' => false
        );
        $options[ 'page_excerpt' ] = array(
            'default' => __( 'The current post\'s excerpt', 'wpsr' ),
            'description' => __( 'A short description of the page. Honored by some social sharing sites.', 'wpsr' ),
            'options' => false
        );

        foreach( $options as $key => $val ){
            $default = empty( $val[ 'default' ] ) ? '<empty>' : $val[ 'default' ];
            $description = isset( $val[ 'description' ] ) ? $val[ 'description' ] : '';

            $supported_values = '';
            if( $val[ 'options' ] && is_array( $val[ 'options' ] ) ){
                $supported_values = array_keys( $val[ 'options' ] );
                $supported_values = array_map(function( $value ){
                    return empty( $value ) ? htmlspecialchars( '<empty>' ) : $value;
                }, $supported_values );
                $supported_values = implode( ', ', $supported_values );
            }

            echo '<tr>';
            echo '<td><code>' . $key . '</code></td>';
            echo '<td><code>' . htmlspecialchars( $default ) . '</code></td>';
            echo '<td>' . $description . '</td>';
            echo '<td>' . $supported_values . '</td>';
            echo '</tr>';
        }

        echo '</tbody>
        </table>';

        echo '<h3>' . __( 'Supported share icons', 'wpsr' ) . '</h3>';
        $social_icons = WPSR_Lists::social_icons();
        $social_icons = array_filter( $social_icons, function( $props ){
            if( in_array( 'for_share', $props[ 'features' ] ) ){
                return true;
            }else{
                return false;
            }
        });
        echo '<p>' . implode( ', ', array_keys( $social_icons ) ) . '</p>';

        echo '</div>';

    }

    function tab_follow_icons(){

        echo '<div data-tab="follow_icons">';

        echo '<h3>' . __( 'Syntax', 'wpsr' ) . '</h3>';
        echo '<pre>[wpsr_follow_icons parameter1="value" parameter2="value" ...]</pre>';

        echo '<h3>' . __( 'Example', 'wpsr' ) . '</h3>';
        echo '<pre>[wpsr_follow_icons facebook="https://facebook.com/aakashweb" twitter="https://twitter.com/aakashweb" instagram="https://instagram.com/aakashweb" bg_color="green" shape="circle"]</pre>';

        echo '<h3>' . __( 'Parameter reference', 'wpsr' ) . '</h3>';
        $options = WPSR_Options::follow_icons();

        unset( $options[ 'ft_status' ] );
        unset( $options[ 'template' ] );
        unset( $options[ 'orientation' ] );
        unset( $options[ 'position' ] );
        unset( $options[ 'title' ] );
        unset( $options[ 'loc_rules' ] );

        echo '<table class="widefat">
            <thead>
                <tr>
                    <th>' . __( 'Parameter', 'wpsr' ) . '</th>
                    <th>' . __( 'Default value', 'wpsr' ) . '</th>
                    <th>' . __( 'Description', 'wpsr' ) . '</th>
                    <th>' . __( 'Supported values', 'wpsr' ) . '</th>
                </tr>
            </thead>
            <tbody>
        ';

        // Adding icons to the param list
        $icons_param = array( '&lt;icon_id&gt;' => array(
            'default' => '',
            'options' => false,
            'description' => __( 'The profile URL of the site. See list below for follow icons ID.', 'wpsr' )
        ));
        $options = $icons_param + $options;

        foreach( $options as $key => $val ){
            $default = empty( $val[ 'default' ] ) ? '<empty>' : $val[ 'default' ];
            $description = isset( $val[ 'description' ] ) ? $val[ 'description' ] : '';

            $supported_values = '';
            if( $val[ 'options' ] ){
                $supported_values = array_keys( $val[ 'options' ] );
                $supported_values = array_map(function( $value ){
                    return empty( $value ) ? htmlspecialchars( '<empty>' ) : $value;
                }, $supported_values );
                $supported_values = implode( ', ', $supported_values );
            }

            echo '<tr>';
            echo '<td><code>' . $key . '</code></td>';
            echo '<td><code>' . htmlspecialchars( $default ) . '</code></td>';
            echo '<td>' . $description . '</td>';
            echo '<td>' . $supported_values . '</td>';
            echo '</tr>';
        }

        echo '</tbody>
        </table>';

        echo '<h3>' . __( 'Supported icons', 'wpsr' ) . '</h3>';
        $social_icons = WPSR_Lists::social_icons();
        $social_icons = array_filter( $social_icons, function( $props ){
            if( in_array( 'for_profile', $props[ 'features' ] ) ){
                return true;
            }else{
                return false;
            }
        });
        echo '<p>' . implode( ', ', array_keys( $social_icons ) ) . '</p>';

        echo '</div>';

    }

    function tab_share_link(){

        echo '<div data-tab="share_link">';

        echo '<h3>' . __( 'Syntax', 'wpsr' ) . '</h3>';
        echo '<pre>[wpsr_share_link parameter1="value" parameter2="value" ...]</pre>';

        echo '<h3>' . __( 'Example', 'wpsr' ) . '</h3>';
        echo '<pre>[wpsr_share_link for="twitter"]Tweet about this page[/wpsr_share_link]</pre>';

        echo '<h3>' . __( 'Output', 'wpsr' ) . '</h3>';
        echo '<pre><a href="https://twitter.com/intent/tweet?text=Post+by+author%20-%20http://example.com/post-by-author/%20@vaakash" target="_blank" rel="nofollow">Tweet about this page</a></pre>';

        echo '<h3>' . __( 'Parameter reference', 'wpsr' ) . '</h3>';

        echo '<table class="widefat">
            <thead>
                <tr>
                    <th>' . __( 'Parameter', 'wpsr' ) . '</th>
                    <th>' . __( 'Default value', 'wpsr' ) . '</th>
                    <th>' . __( 'Description', 'wpsr' ) . '</th>
                    <th>' . __( 'Supported values', 'wpsr' ) . '</th>
                </tr>
            </thead>
            <tbody>
        ';

        $rows = array(
            array(
                'parameter' => 'for',
                'default_value' => '&lt;empty&gt;',
                'description' => __( 'The ID of the social media service to generate share link for.', 'wpsr' ),
                'supported_values' => __( 'Refer list below for the supported IDs', 'wpsr' )
            ),
            array(
                'parameter' => 'class',
                'default_value' => '&lt;empty&gt;',
                'description' => __( 'Sets the CSS class value for the a tag.', 'wpsr' ),
                'supported_values' => ''
            ),
            array(
                'parameter' => 'target',
                'default_value' => '_blank',
                'description' => __( 'Sets the target attribute for the link.', 'wpsr' ),
                'supported_values' => ''
            ),
            array(
                'parameter' => 'page_url',
                'default_value' => 'The URL of the current post/page where the shortcode is used.',
                'description' => __( 'Sets the URL to share.', 'wpsr' ),
                'supported_values' => ''
            ),
            array(
                'parameter' => 'page_title',
                'default_value' => 'The title of the current post/page where the shortcode is used.',
                'description' => __( 'The title of the page to share', 'wpsr' ),
                'supported_values' => ''
            ),
            array(
                'parameter' => 'page_excerpt',
                'default_value' => 'The description of the current post/page where the shortcode is used.',
                'description' => __( 'The description of the page to share', 'wpsr' ),
                'supported_values' => ''
            )
        );

        foreach( $rows as $row ){
            echo '<tr>';
            echo '<td>' . $row['parameter'] . '</td>';
            echo '<td>' . $row['default_value'] . '</td>';
            echo '<td>' . $row['description'] . '</td>';
            echo '<td>' . $row['supported_values'] . '</td>';
            echo '</tr>';
        }

        echo '</tbody>
        </table>';

        echo '<h3>' . __( 'Supported icons', 'wpsr' ) . '</h3>';
        $social_icons = WPSR_Lists::social_icons();
        $social_icons = array_filter( $social_icons, function( $props ){
            if( in_array( 'for_share', $props[ 'features' ] ) ){
                return true;
            }else{
                return false;
            }
        });
        echo '<p>' . implode( ', ', array_keys( $social_icons ) ) . '</p>';

        echo '</div>';

    }

    public static function note( $feature = '', $shortcode = '' ){
        echo '<div class="note">';
        echo '<h4><i class="fas fa-code"></i>' . __( 'Shortcode', 'wpsr' ) . '</h4>';
        echo '<p>' . sprintf( __( 'If you want to use %s anywhere in a custom position then you can use the shortcode <code>[%s]</code>. Please refer shortcodes page on how to customize this shortcode.', 'wpsr' ), $feature, $shortcode ) . '</p>';
        echo '<p><a href="' . admin_url( 'admin.php?page=wp_socializer&tab=shortcodes' ) . '" target="_blank" class="button button-primary">' . __( 'Create shortcode', 'wpsr' ) . '</a></p>';
        echo '</div>';
    }

}

new WPSR_Admin_Shortcodes();

?>