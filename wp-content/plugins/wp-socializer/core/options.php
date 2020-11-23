<?php
/**
  * Options of all the features
  * 
  */

defined( 'ABSPATH' ) || exit;

class WPSR_Options{

    public static function common_options( $field ){

        $fields = array(
            'icon_size' => array(
                '32px' => array( '32px', 'size.svg', '32px' ),
                '40px' => array( '40px', 'size.svg', '40px' ),
                '48px' => array( '48px', 'size.svg', '48px' ),
                '64px' => array( '64px', 'size.svg', '64px' )
            ),
            'icon_shape' => array(
                '' => array( 'Square', 'shape-square.svg', '32px' ),
                'circle' => array( 'Circle', 'shape-circle.svg', '32px' ),
                'squircle' => array( 'Squircle', 'shape-squircle.svg', '32px' ),
                'squircle-2' => array( 'Squircle 2', 'shape-squircle-2.svg', '32px' ),
                'drop' => array( 'Drop', 'shape-drop.svg', '32px' ),
                'diamond' => array( 'Diamond*', 'shape-diamond.svg', '32px' ),
                'ribbon' => array( 'Ribbon*', 'shape-ribbon.svg', '32px' )
            ),
            'hover_effect' => array(
                '' => __( 'None', 'wpsr' ),
                'opacity' => 'Fade',
                'rotate' => 'Rotate',
                'zoom' => 'Zoom',
                'shrink' => 'Shrink',
                'float' => 'Float',
                'sink' => 'Sink'
            ),
            'share_counter' => array(
                '' => 'No share count',
                'individual' => 'Individual count',
                'total' => 'Total count only',
                'total-individual' => 'Both individual and total counts',
            ),
            'sc_style' => array(
                'count-1' => array( 'Style 1', 'counter-1.svg', '60px' ),
                'count-2' => array( 'Style 2', 'counter-2.svg', '70px' ),
                'count-3' => array( 'Style 3', 'counter-3.svg', '70px' ),
            ),
            'more_icons' => array(
                '0' => 'No grouping',
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
                '5' => '5',
                '6' => '6',
                '7' => '7',
                '8' => '8',
                '9' => '9',
                '10' => '10',
            )
        );

        return $fields[ $field ];

    }

    public static function filter_options( $feature, $prop ){

        $fields = call_user_func( array( __class__, $feature ) );
        $output = array();

        foreach( $fields as $key => $val ){
            $filter_val = array_key_exists( $prop, $val ) ? $val[ $prop ] : '';
            $output[ $key ] = $filter_val;
        }

        return $output;

    }

    public static function default_values( $feature ){
        return self::filter_options( $feature, 'default' );
    }

    public static function options( $feature ){
        return self::filter_options( $feature, 'options' );
    }

    public static function share_icons(){

        return array(
            'selected_icons' => array(
                'default' => 'W3siZmFjZWJvb2siOnsiaG92ZXJfdGV4dCI6IiIsInRleHQiOiIiLCJpY29uIjoiIn19LHsidHdpdHRlciI6eyJpY29uIjoiIiwidGV4dCI6IiIsImhvdmVyX3RleHQiOiIifX0seyJsaW5rZWRpbiI6eyJpY29uIjoiIiwidGV4dCI6IiIsImhvdmVyX3RleHQiOiIifX0seyJwaW50ZXJlc3QiOnsiaWNvbiI6IiIsInRleHQiOiIiLCJob3Zlcl90ZXh0IjoiIn19LHsicHJpbnQiOnsiaWNvbiI6IiIsInRleHQiOiIiLCJob3Zlcl90ZXh0IjoiIn19LHsicGRmIjp7Imljb24iOiIiLCJ0ZXh0IjoiIiwiaG92ZXJfdGV4dCI6IiJ9fV0=',
                'options' => false,
                'description' => __( 'The social media icons selected for sharing', 'wpsr' )
            ),
            'layout' => array(
                'default' => '',
                'options' => array(
                    '' => array( 'Normal', 'layout-horizontal.svg', '64px' ),
                    'fluid' => array( 'Full width', 'layout-fluid.svg', '64px' ),
                ),
                'description' => __( 'The layout of the social icons. It decides whether the icons should be of normal width or full width. Select fluid for full width.', 'wpsr' )
            ),
            'icon_size' => array(
                'default' => '32px',
                'options' => self::common_options( 'icon_size' ),
                'description' => __( 'The size of the icons.', 'wpsr' )
            ),
            'icon_shape' => array(
                'default' => 'circle',
                'options' => self::common_options( 'icon_shape' ),
                'description' => __( 'The shape of the icons.', 'wpsr' )
            ),
            'hover_effect' => array(
                'default' => 'opacity',
                'options' => self::common_options( 'hover_effect' ),
                'description' => __( 'The behavior of the icons when mouse is hovered over them.', 'wpsr' )
            ),
            'icon_color' => array(
                'default' => '#ffffff',
                'options' => false,
                'description' => __( 'The color of the icons.', 'wpsr' )
            ),
            'icon_bg_color' => array(
                'default' => '',
                'options' => false,
                'description' => __( 'The background color of the icons. Leave empty to take the social media site\'s own color.', 'wpsr' )
            ),
            'padding' => array(
                'default' => 'pad',
                'options' => array(
                    '' => 'No',
                    'pad' => 'Yes'
                ),
                'description' => __( 'Decides whether to add a space between the icons.', 'wpsr' )
            ),
            'share_counter' => array(
                'default' => 'total-individual',
                'options' => self::common_options( 'share_counter' ),
                'description' => __( 'The type of share counters to display in the share icons bar.', 'wpsr' )
            ),
            'sc_style' => array(
                'default' => 'count-1',
                'options' => self::common_options( 'sc_style' ),
                'description' => __( 'The design style of the share count numbers and how they are displayed.', 'wpsr' )
            ),
            'sc_total_position' => array(
                'default' => 'left',
                'options' => array(
                    'left' => 'Left to the icons',
                    'right' => 'Right to the icons'
                ),
                'description' => __( 'The position of the total count. This is effective only when share_counter includes total count.', 'wpsr' )
            ),
            'more_icons' => array(
                'default' => '0',
                'options' => self::common_options( 'more_icons' ),
                'description' => __( 'The number of icons from the last to group into a single icon.', 'wpsr' )
            ),
            'center_icons' => array(
                'default' => '',
                'options' => array(
                    '' => 'No',
                    'yes' => 'Yes'
                ),
                'description' => __( 'Centers the icon in the content.', 'wpsr' )
            ),
            'heading' => array(
                'default' => '<h3>Share and Enjoy !</h3>',
                'options' => false,
                'description' => __( 'The heading to display above the icons. HTML is allowed.', 'wpsr' )
            ),
            'custom_html_above' => array(
                'default' => '',
                'options' => false
            ),
            'custom_html_below' => array(
                'default' => '',
                'options' => false
            ),

            'sm_screen_width' => array(
                'default' => '768',
                'options' => false,
                'description' => __( 'The screen width below which the icons will act in mobile/small screen mode. In pixels.', 'wpsr' )
            ),
            'lg_screen_action' => array(
                'default' => 'show',
                'options' => array(
                    'show' => __( 'Show', 'wpsr' ),
                    'hide' => __( 'Hide', 'wpsr' )
                ),
                'description' => __( 'The behavior of the icons in desktop/large screens.', 'wpsr' )
            ),
            'sm_screen_action' => array(
                'default' => 'show',
                'options' => array(
                    'show' => __( 'Show', 'wpsr' ),
                    'hide' => __( 'Hide', 'wpsr' )
                ),
                'description' => __( 'The behavior of the icons in mobile/small screens.', 'wpsr' )
            ),
            
            'loc_rules' => array(
                'default' => array(
                    'type' => 'show_all',
                    'rule' => 'W10='
                ),
                'options' => false
            ),

            'position' => array(
                'default' => 'below_posts',
                'options' => array(
                    'above_posts' => __( 'Above posts', 'wpsr' ),
                    'below_posts' => __( 'Below posts', 'wpsr' ),
                    'above_below_posts' => __( 'Both above and below posts', 'wpsr' )
                ),
                'description' => __( 'The position of the social icons in a post.', 'wpsr' )
            ),
            'in_excerpt' => array(
                'default' => 'hide',
                'options' => array(
                    'show' => __( 'Show in excerpt', 'wpsr' ),
                    'hide' => __( 'Hide in excerpt', 'wpsr' )
                ),
                'description' => __( 'Decides whether to show the icons in the excerpts.', 'wpsr' )
            )
        );

    }

    public static function floating_sharebar(){

        return array(
            'ft_status' => array(
                'default' => 'disable',
                'options' => array(
                    'enable' => __( 'Enable floating sharebar', 'wpsr' ),
                    'disable' => __( 'Disable floating sharebar', 'wpsr' )
                )
            ),
            'selected_icons' => array(
                'default' => 'W3siZmFjZWJvb2siOnsiaG92ZXJfdGV4dCI6IiIsImljb24iOiIifX0seyJ0d2l0dGVyIjp7ImhvdmVyX3RleHQiOiIiLCJpY29uIjoiIn19LHsibGlua2VkaW4iOnsiaG92ZXJfdGV4dCI6IiIsImljb24iOiIifX0seyJlbWFpbCI6eyJob3Zlcl90ZXh0IjoiIiwiaWNvbiI6IiJ9fSx7InBkZiI6eyJob3Zlcl90ZXh0IjoiIiwiaWNvbiI6IiJ9fSx7IndoYXRzYXBwIjp7ImhvdmVyX3RleHQiOiIiLCJpY29uIjoiIn19XQ==',
                'options' => false
            ),
            'icon_size' => array(
                'default' => '40px',
                'options' => self::common_options( 'icon_size' )
            ),
            'icon_shape' => array(
                'default' => '',
                'options' => self::common_options( 'icon_shape' )
            ),
            'hover_effect' => array(
                'default' => 'opacity',
                'options' => self::common_options( 'hover_effect' )
            ),
            'icon_color' => array(
                'default' => '#ffffff',
                'options' => false
            ),
            'icon_bg_color' => array(
                'default' => '',
                'options' => false
            ),
            'padding' => array(
                'default' => '',
                'options' => array(
                    '' => 'No',
                    'pad' => 'Yes'
                )
            ),
            'style' => array(
                'default' => '',
                'options' => array(
                    '' => array( 'Simple', 'layout-vertical.svg', '64px' ),
                    'enclosed' => array( 'Enclosed', 'fsb-enclosed.svg', '64px' ),
                )
            ),
            'sb_bg_color' => array(
                'default' => '#ffffff',
                'options' => false
            ),
            'sb_position' => array(
                'default' => 'wleft',
                'options' => array(
                    'wleft' => 'Left of the page',
                    'wright' => 'Right of the page',
                    'scontent' => 'Stick to the content'
                )
            ),
            'stick_element' => array(
                'default' => '.entry',
                'options' => false
            ),
            'offset' => array(
                'default' => '10px',
                'options' => false
            ),
            'movement' => array(
                'default' => 'move',
                'options' => array(
                    'move' => __( 'Sticky, move when page is scrolled', 'wpsr' ),
                    'static' => __( 'Static, no movement', 'wpsr' )
                )
            ),
            'share_counter' => array(
                'default' => 'total-individual',
                'options' => self::common_options( 'share_counter' )
            ),
            'sc_style' => array(
                'default' => 'count-1',
                'options' => self::common_options( 'sc_style' )
            ),
            'sc_total_position' => array(
                'default' => 'top',
                'options' => array(
                    'top' => 'Above the icons',
                    'bottom' => 'Below the icons'
                )
            ),
            'sc_total_color' => array(
                'default' => '#000000',
                'options' => false
            ),
        
            'sm_screen_width' => array(
                'default' => '768',
                'options' => false
            ),
            'lg_screen_action' => array(
                'default' => 'show',
                'options' => array(
                    'show' => __( 'Show', 'wpsr' ),
                    'hide' => __( 'Hide', 'wpsr' ),
                    'close' => __( 'Close', 'wpsr' )
                )
            ),
            'sm_screen_action' => array(
                'default' => 'bottom',
                'options' => array(
                    'bottom' => __( 'Show to bottom of the page', 'wpsr' ),
                    'hide' => __( 'Hide', 'wpsr' )
                )
            ),
        
            'more_icons' => array(
                'default' => '0',
                'options' => self::common_options( 'more_icons' )
            ),
            'loc_rules' => array(
                'default' => array(
                    'type' => 'show_all',
                    'rule' => 'W10='
                ),
                'options' => false
            )
        );

    }

    public static function follow_icons(){

        return array(
            'ft_status' => array(
                'default' => 'disable',
                'options' => array(
                    'enable' => __( 'Enable follow icons', 'wpsr' ),
                    'disable' => __( 'Disable follow icons', 'wpsr' )
                )
            ),
            'template' => array(
                'default' => 'W10=',
                'options' => false
            ),
            'shape' => array(
                'default' => '',
                'options' => self::common_options( 'icon_shape' ),
                'description' => __( 'The shape of the icons.', 'wpsr' )
            ),
            'size' => array(
                'default' => '32px',
                'options' => self::common_options( 'icon_size' ),
                'description' => __( 'The size of the icons.', 'wpsr' )
            ),
            'bg_color' => array(
                'default' => '',
                'options' => false,
                'description' => __( 'The background color of the icons. Leave empty to take the default social media site\'s brand color', 'wpsr' )
            ),
            'icon_color' => array(
                'default' => '#ffffff',
                'options' => false,
                'description' => __( 'The color of the icon.', 'wpsr' )
            ),
            'orientation' => array(
                'default' => 'vertical',
                'options' => array(
                    'vertical' => array( 'Vertical', 'layout-vertical.svg', '75px' ),
                    'horizontal' => array( 'Horizontal', 'layout-horizontal.svg', '75px' ),
                ),
                'description' => __( 'The orientation of the icon bar.', 'wpsr' )
            ),
            'position' => array(
                'default' => 'rm',
                'options' => array(
                    'tl' => array( 'Top left', 'pos-tl.svg', '60px' ),
                    'tm' => array( 'Top middle', 'pos-tm.svg', '60px' ),
                    'tr' => array( 'Top right', 'pos-tr.svg', '60px' ),
                    'rm' => array( 'Right middle', 'pos-rm.svg', '60px' ),
                    'br' => array( 'Bottom right', 'pos-br.svg', '60px' ),
                    'bm' => array( 'Bottom middle', 'pos-bm.svg', '60px' ),
                    'bl' => array( 'Bottom left', 'pos-bl.svg', '60px' ),
                    'lm' => array( 'Left middle', 'pos-lm.svg', '60px' ),
                )
            ),
            'hover' => array(
                'default' => 'zoom',
                'options' => self::common_options( 'hover_effect' ),
                'description' => __( 'The behavior of the icons when mouse is hovered over them.', 'wpsr' )
            ),
            'pad' => array(
                'default' => 'pad',
                'options' => array(
                    '' => __( 'No', 'wpsr' ),
                    'pad' => __( 'Yes', 'wpsr' )
                ),
                'description' => __( 'Decides whether to add a space between the icons.', 'wpsr' )
            ),
            'title' => array(
                'default' => '',
                'options' => false
            ),
            'open_popup' => array(
                'default' => 'no',
                'options' => array(
                    'no' => 'No',
                    '' => 'Yes',
                ),
                'description' => __( 'Decides whether to open the links in a popup or in a new tab.', 'wpsr' )
            ),
            'sm_screen_width' => array(
                'default' => '768',
                'options' => false,
                'description' => __( 'The screen width below which the icons will act in mobile/small screen mode. In pixels.', 'wpsr' )
            ),
            'lg_screen_action' => array(
                'default' => 'show',
                'options' => array(
                    'show' => __( 'Show', 'wpsr' ),
                    'hide' => __( 'Hide', 'wpsr' ),
                    'close' => __( 'Close', 'wpsr' )
                ),
                'description' => __( 'The behavior of the icons in desktop/large screens.', 'wpsr' )
            ),
            'sm_screen_action' => array(
                'default' => 'show',
                'options' => array(
                    'show' => __( 'Show', 'wpsr' ),
                    'hide' => __( 'Hide', 'wpsr' ),
                    'close' => __( 'Close', 'wpsr' )
                ),
                'description' => __( 'The behavior of the icons in mobile/small screens.', 'wpsr' )
            ),
            'loc_rules' => array(
                'default' => array(
                    'type' => 'show_all',
                    'rule' => 'W10='
                ),
                'options' => false
            )
        );

    }

    public static function text_sharebar(){

        return array(
            'ft_status' => array(
                'options' => array(
                    'enable' => __( 'Enable text sharebar', 'wpsr' ),
                    'disable' => __( 'Disable text sharebar', 'wpsr' )
                ),
                'default' => 'disable'
            ),
            'template' => array(
                'options' => false,
                'default' => 'W10=',
            ),
            'content' => array(
                'options' => false,
                'default' => '.entry-content',
            ),
            'size' => array(
                'options' => self::common_options( 'icon_size' ),
                'default' => '32px',
            ),
            'bg_color' => array(
                'options' => false,
                'default' => '#333',
            ),
            'icon_color' => array(
                'options' => false,
                'default' => '#fff',
            ),
            'text_count' => array(
                'options' => false,
                'default' => '20',
            ),
            'loc_rules' => array(
                'options' => false,
                'default' => array(
                    'type' => 'show_selected',
                    'rule' => 'W1tbInNpbmdsZSIsImVxdWFsIiwiIl1dLFtbInBhZ2UiLCJlcXVhbCIsIiJdXV0='
                )
            )
        );

    }

    public static function general_settings(){

        return array(

            // Share icons
            'facebook_app_id' => array(
                'default' => '',
                'options' => false
            ),
            'facebook_app_secret' => array(
                'default' => '',
                'options' => false
            ),
            'facebook_lang' => array(
                'default' => 'en_US',
                'options' => false
            ),
            'twitter_username' => array(
                'default' => '',
                'options' => false
            ),

            // Share counter
            'counter_expiration' => array(
                'default' => '43200',
                'options' => false
            ),
            'counter_both_protocols' => array(
                'default' => 'no',
                'options' => array(
                    'no' => __( 'No', 'wpsr' ),
                    'yes' => __( 'Yes', 'wpsr' )
                )
            ),

            // Misc settings
            'font_icon' => array(
                'default' => 'fa5',
                'options' => false
            ),
            'misc_additional_css' => array(
                'default' => '',
                'options' => false
            ),
            'skip_res_load' => array(
                'default' => '',
                'options' => false
            )

        );

    }

}

?>