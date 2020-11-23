<?php
/**
 * Plugin Name:       WP Socializer
 * Plugin URI:        https://www.aakashweb.com/wordpress-plugins/wp-socializer/
 * Description:       Add various social media sharing features to posts/pages/widgets like social media share icons, floating/sticky share bar, follow my profile icons and more.
 * Version:           6.1
 * Author:            Aakash Chakravarthy
 * Author URI:        https://www.aakashweb.com
 * Text Domain:       wpsr
 * Domain Path:       /languages
 */

define( 'WPSR_VERSION', '6.1' );
define( 'WPSR_PATH', plugin_dir_path( __FILE__ ) ); // All have trailing slash
define( 'WPSR_URL', plugin_dir_url( __FILE__ ) );
define( 'WPSR_ADMIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) . 'admin' ) );
define( 'WPSR_BASE_NAME', plugin_basename( __FILE__ ) );

//error_reporting(E_ALL);

final class WP_Socializer{
    
    function __construct(){
        
        add_action( 'plugins_loaded', array( $this, 'load_text_domain' ) );
        
        $this->includes();
        
    }

    function includes(){
        
        // Core
        include_once( WPSR_PATH . 'core/lists.php' );
        include_once( WPSR_PATH . 'core/options.php' );
        include_once( WPSR_PATH . 'core/metadata.php' );
        include_once( WPSR_PATH . 'core/location-rules.php' );
        include_once( WPSR_PATH . 'core/includes.php' );
        include_once( WPSR_PATH . 'core/share-counter.php' );
        include_once( WPSR_PATH . 'core/shortcodes.php' );
        include_once( WPSR_PATH . 'core/widgets.php' );
        include_once( WPSR_PATH . 'core/import-export.php' );

        // Templates
        include_once( WPSR_PATH . 'core/templates/share-icons.php' );
        include_once( WPSR_PATH . 'core/templates/floating-sharebar.php' );
        include_once( WPSR_PATH . 'core/templates/follow-icons.php' );
        include_once( WPSR_PATH . 'core/templates/text-sharebar.php' );

        // Widgets
        include_once( WPSR_PATH . 'core/widgets/follow-icons.php' );
        include_once( WPSR_PATH . 'core/widgets/facebook.php' );
        include_once( WPSR_PATH . 'core/widgets/twitter.php' );

        // Admin
        include_once( WPSR_PATH . 'admin/admin.php' );
        include_once( WPSR_PATH . 'admin/form.php' );
        include_once( WPSR_PATH . 'admin/icons-editor.php' );
        include_once( WPSR_PATH . 'admin/widgets.php' );
        include_once( WPSR_PATH . 'admin/tools.php' );

        // Admin pages
        include_once( WPSR_PATH . 'admin/pages/share-icons.php' );
        include_once( WPSR_PATH . 'admin/pages/floating-sharebar.php' );
        include_once( WPSR_PATH . 'admin/pages/follow-icons.php' );
        include_once( WPSR_PATH . 'admin/pages/text-sharebar.php' );
        include_once( WPSR_PATH . 'admin/pages/shortcodes.php' );
        include_once( WPSR_PATH . 'admin/pages/import-export.php' );
        include_once( WPSR_PATH . 'admin/pages/general-settings.php' );

    }
    
    function load_text_domain(){
        load_plugin_textdomain( 'wpsr', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    }
    
}

$wpsr = new WP_Socializer();

?>