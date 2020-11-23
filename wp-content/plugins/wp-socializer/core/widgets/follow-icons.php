<?php
/**
 * Follow icons widget
 */

defined( 'ABSPATH' ) || exit;

class WPSR_Follow_Icons_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'wpsr_follow_icons_widget',
            'Follow icons widget',
            array( 'description' => __( 'Add icons with links to social media profiles using this widget.', 'text_domain' ), ),
            array('width' => 500, 'height' => 500)
        );
    }

    public static function init(){
        add_filter( 'wpsr_register_widget', array( __class__, 'register' ) );
        add_filter( 'wpsr_register_admin_page', array( __class__, 'register_admin_page' ) );
    }

    public static function register( $widgets ){

        $widgets[ 'follow_icons' ] = array(
            'name' => __( 'Follow icons', 'wpsr' ),
            'widget_class' => __class__
        );

        return $widgets;

    }

    public static function register_admin_page( $pages ){
        
        $pages[ 'follow-icons-widget' ] = array(
            'name' => __( 'Follow icons widget', 'wpsr' ),
            'banner' => WPSR_ADMIN_URL . '/images/banners/follow-icons-widget.svg',
            'link' => admin_url('widgets.php#wp-socializer:follow_icons'),
            'category' => 'widget',
            'type' => 'widget',
            'description' => __( 'Add links of your social media profiles to sidebar/footer using this widget.', 'wpsr' )
        );

        return $pages;

    }

    public static function defaults(){

        return array(
            'title' => '',
            'template' => '',
            'orientation' => 'horizontal',
            'shape' => '',
            'size' => '32px',
            'bg_color' => '',
            'icon_color' => '#ffffff',
            'hover' => 'opacity',
            'pad' => 'pad',
            'profile_text' => ''
        );

    }

    function widget( $args, $instance ){

        $instance = WPSR_Lists::set_defaults( $instance, self::defaults() );

        WPSR_Widgets::before_widget( $args, $instance );

        echo WPSR_Template_Follow_Icons::html( $instance, False );

        WPSR_Widgets::after_widget( $args, $instance );

    }

    function form( $instance ){

        $instance = WPSR_Lists::set_defaults( $instance, self::defaults() );
        $fields = new WPSR_Widget_Form_Fields( $this, $instance );
        
        echo '<div class="wpsr_widget_wrap">';

        $fields->text( 'title', 'Title' );

        $tmpl_val = esc_attr( $instance[ 'template' ] );
        $tmpl_cnt_id = $this->get_field_id( 'template' );
        $tmpl_prev_id = $this->get_field_id( 'fbw_prev' );
        
        echo '<div class="hidden">';
        $fields->text( 'template', '' );
        $fields->text( 'orientation', '' );
        echo '</div>';
        
        $fields->heading( __( 'Selected icons', 'wpsr' ) );
        echo '<div class="clearfix" id="' . $tmpl_prev_id . '">';
        $tmpl = WPSR_Admin_Follow_Icons::read_template( $tmpl_val );
        echo $tmpl[ 'prev' ];
        echo '</div>';
        
        echo '<p align="center"><button class="button button-primary wpsr_ppe_fb_open" data-wtmpl-cnt-id="' . $tmpl_cnt_id . '" data-wtmpl-prev-id="' . $tmpl_prev_id . '"><i class="fa fa-pencil"></i> ' . __( 'Open editor', 'wpsr' ) . '</button></p>';
        
        $fields->heading( __( 'Settings', 'wpsr' ) );
        $fields->select( 'shape', 'Icon shape', array(
            '' => 'Square',
            'circle' => 'Circle',
            'squircle' => 'Squircle',
            'squircle-2' => 'Squircle 2',
            'diamond' => 'Diamond',
            'ribbon' => 'Ribbon',
            'drop' => 'Drop',
        ), array( 'class' => 'smallfat' ));
        
        $fields->select( 'size', 'Icon size', array(
            '32px' => '32px',
            '40px' => '40px',
            '48px' => '48px',
            '64px' => '64px',
        ), array( 'class' => 'smallfat' ));
        
        $fields->text( 'bg_color', 'Icon background color', array( 'class' => 'smallfat wpsr-color-picker' ));
        
        $fields->text( 'icon_color', 'Icon color', array( 'class' => 'smallfat wpsr-color-picker' ));
        
        $fields->select( 'hover', 'Hover effect', array(
            '' => __( 'None', 'wpsr' ),
            'opacity' => 'Fade',
            'rotate' => 'Rotate',
            'zoom' => 'Zoom',
            'shrink' => 'Shrink',
            'float' => 'Float',
            'sink' => 'Sink'
        ), array( 'class' => 'smallfat' ));
        
        $fields->select( 'pad', 'Add space between icons', array(
            '' => 'No',
            'pad' => 'Yes'
        ), array( 'class' => 'smallfat' ));

        $fields->textarea( 'profile_text', 'Text above follow icons' );
        echo '<span class="description">' . __( 'Add any text you like to see above the icons. HTML is allowed.', 'wpsr' ) . '</span>';

        $fields->footer();

        echo '</div>';

    }

    function update( $new_instance, $old_instance ){
        return $new_instance;
    }

}

WPSR_Follow_Icons_Widget::init();

?>