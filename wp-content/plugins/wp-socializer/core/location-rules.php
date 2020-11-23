<?php
/**
  * Location rules module
  * 
  */

defined( 'ABSPATH' ) || exit;

class WPSR_Location_Rules{
    
    public static function init(){
        
        // Register location rules action
        add_action( 'wp_ajax_wpsr_location_rules', array( __CLASS__, 'selectors_ajax' ) );
        
    }
    
    public static function rules_list(){
        
        //Default rules
        $rules = array(
            'single' => array( 
                'name' => 'Single post',
                'callback' => array( __CLASS__, 'rule_is_single' ),
                'placeholder' => __( 'Select specific posts', 'wpsr' ),
                'helper' => 1,
                'children' => array( 'has-category', 'has-tag', 'post-type' )
            ),
            
            'page' => array(
                'name' => 'Page',
                'callback' => array( __CLASS__, 'rule_is_page' ),
                'placeholder' => __( 'Select specific pages', 'wpsr' ),
                'helper' => 1,
                'children' => array( 'has-category', 'has-tag', 'post-type' )
            ),
            
            'home' => array(
                'name' => 'Home page',
                'callback' => array( __CLASS__, 'rule_is_home' ),
                'helper' => 0,
                'children' => array( 'in-excerpt', 'post-type', 'has-category', 'has-tag' )
            ),
            
            'front-page' => array(
                'name' => 'Front page',
                'callback' => array( __CLASS__, 'rule_is_frontpage' ),
                'helper' => 0,
                'children' => array( 'in-excerpt', 'post-type', 'has-category', 'has-tag' )
            ),
            
            'sticky' => array(
                'name' => 'Sticky posts',
                'callback' => array( __CLASS__, 'rule_is_sticky' ),
                'helper' => 0
            ),
            
            'post-type' => array(
                'name' => 'Post type',
                'callback' => array( __CLASS__, 'rule_post_type' ),
                'placeholder' => 'Select available post type',
                'helper' => 1,
                'children' => array( 'single', 'page', 'has-category', 'has-tag' )
            ),
            
            'post-format' => array(
                'name' => 'Post format',
                'callback' => array( __CLASS__, 'rule_is_post_format' ),
                'placeholder' => 'Select post formats',
                'helper' => 1,
            ),
            
            'archive' => array(
                'name' => 'Archive pages',
                'callback' => array( __CLASS__, 'rule_is_archive' ),
                'helper' => 0,
                'children' => array( 'category', 'tag', 'date' )
            ),
            
            'category' => array(
                'name' => 'Category archive page',
                'callback' => array( __CLASS__, 'rule_is_category' ),
                'placeholder' => __( 'Select available category archive pages', 'wpsr' ),
                'helper' => 1
            ),
            
            'tag' => array(
                'name' => 'Tags archive page',
                'callback' => array( __CLASS__, 'rule_is_tag' ),
                'placeholder' => __( 'Select available tag archive pages', 'wpsr' ),
                'helper' => 1
            ),
            
            'date' => array(
                'name' => 'Date archive page',
                'callback' => array( __CLASS__, 'rule_is_date' ),
                'helper' => 0
            ),
            
            'not-found' => array(
                'name' => '404 page',
                'callback' => array( __CLASS__, 'rule_is_404' ),
                'helper' => 0
            ),
            
            'has-category' => array(
                'name' => 'Categories of post',
                'callback' => array( __CLASS__, 'rule_has_category' ),
                'placeholder' => __( 'Select available categories', 'wpsr' ),
                'helper' => 1
            ),
            
            'has-tag' => array(
                'name' => 'Tags of post',
                'callback' => array( __CLASS__, 'rule_has_tag' ),
                'placeholder' => __( 'Select available tags', 'wpsr' ),
                'helper' => 1
            ),
            
        );
        
        return apply_filters( 'wpsr_mod_location_rules_list', $rules );
        
    }
    
    public static function check_rule( $rule_wrap ){
        
        $rule_wrap = WPSR_Lists::set_defaults( $rule_wrap, array(
            'type' => 'show_all',
            'rule' => 'W10='
        ));
        
        $type = $rule_wrap[ 'type' ];
        $group = json_decode( base64_decode( $rule_wrap[ 'rule' ] ) );
                
        if( $type == 'show_all' ){
            return 1;
        }else if( $type == 'hide_all' ){
            return 0;
        }
        
        if( empty( $group ) ){
            return 0;
        }
        
        $or_flag = 0;
        foreach( $group as $rules ){
            
            $and_flag = 1;
            foreach( $rules as $rule ){
                $rule_answer = self::exe_rule( $rule );
                
                if( $rule_answer && $and_flag ){
                    $and_flag = 1;
                }else{
                    $and_flag = 0;
                }
            }
            
            if( $and_flag || $or_flag ){
                $or_flag = 1; // can display;
            }else{
                $or_flag = 0; // cannot display;
            }
            
        }
        
        if( $type == 'show_selected' ){
            return $or_flag;
        }else{
            return !$or_flag;
        }
        
    }
    
    public static function exe_rule( $rule ){
        
        $loc_rules = self::rules_list();
        
        $answer = 0;
        if( is_callable( $loc_rules[ $rule[0] ][ 'callback' ] ) ){
            $answer = call_user_func_array( $loc_rules[ $rule[ 0 ] ][ 'callback' ], array( 2, $rule[ 2 ] ) ); # Mode 2
        }
        
        if( $loc_rules[ $rule[ 0 ] ][ 'helper' ] != 0 ){
            if( $rule[ 1 ] == 'equal' ){
                return $answer;
            }else{
                return !$answer;
            }
        }else{
            return $answer;
        }
        
    }
    
    public static function selectors_ajax(){
        
        $rules = self::rules_list();
        $rule_id = $_GET[ 'rule_id' ];
        $selected = $_GET[ 'selected' ];
        
        if( !array_key_exists( $rule_id, $rules ) ){
            die( __( 'Invalid rule id !', 'wpsr' ) );
        }
        
        // Mode 1: Get selectors list
        // Mode 2: Check rule
        if( isset( $rules[ $rule_id ][ 'callback' ] ) && is_callable( $rules[ $rule_id ][ 'callback' ] ) ){
            $list = call_user_func_array( $rules[ $rule_id ][ 'callback' ], array( 1, '' ) ); # Mode 1
        }else{
            die( __( 'No selections supported for this page !', 'wpsr' ) );
        }
        
        if( empty( $list ) )
            die();
        
        $selSplit = array_filter( array_map( 'trim', explode( ',', $selected ) ) );

        if( is_array( $selSplit ) ){
            foreach ( $list as $k => $v ){
                $isCheck = in_array( $k, $selSplit ) ? 'checked="selected"' : '';
                echo '<label><input type="checkbox" ' . $isCheck . ' value="' . $k . '"> ' . $v . '</label><br/>';
            }
        }
        
        die();
        
    }
    
    public static function display_rules( $id, $values = array() ){
        
        $types = array(
            'show_all' => array( __( 'Show in all pages', 'wpsr' ), 'lr-show-all.svg', '80px' ),
            'hide_all' => array( __( 'Hide in all pages', 'wpsr' ), 'lr-hide-all.svg', '80px' ),
            'show_selected' => array( __( 'Show in selected pages', 'wpsr' ), 'lr-show-sel.svg', '80px' ),
            'hide_selected' => array( __( 'Hide in selected pages', 'wpsr' ), 'lr-hide-sel.svg', '80px' )
        );
        
        echo '<div class="loc_rules_wrap">';

        echo '<div class="loc_rules_type">';
        echo WPSR_Form::field_html( 'image_select', array(
            'name' => $id . '[type]',
            'list' => $types,
            'value' => $values['type'],
            'default' => 'show_all',
        ));
        echo '</div>';
        
        // Set default pages to rule
        if( !isset( $values['rule'] ) ){
            $values['rule'] = 'W10='; // [] - Default base64 value for no rule
        }
        
        $values['rule'] = json_decode( base64_decode( $values['rule'] ) );
        
        echo '<div class="loc_rules_inner" data-conditioner data-condr-input="(prev::)(find::select)" data-condr-value="selected" data-condr-action="pattern?fadeIn:hide" data-condr-events="change">';
        echo '<p class="loc_rule_info">' . __( 'No page rules are added. Template will be hidden everywhere', 'wpsr' ) . '</p>';
        
        echo '<div class="loc_rules_box">';
        if( is_array( $values['rule'] ) ){
            foreach( $values['rule'] as $grp ){
                echo '<div class="loc_group_wrap">';
                foreach( $grp as $rle ){
                    echo self::rules_template( $rle, 0 );
                }
                echo '</div>';
            }
        }
        echo '</div>';
        
        echo '<a href="#" class="button-primary loc_group_add" title="' . __( 'Add another page', 'wpsr' ) . '">  AND  </a>';
        echo '</div>';
        
        echo '<div class="hidden">';
            echo '<input type="hidden" name="' . $id . '[rule]" class="loc_rule_value" />';
            echo '<div class="loc_rules_temp">' . self::rules_template( array( '', '', '' ), 1 ) . '</div>';
        echo '</div>';
        
        echo '</div>';
        
    }
    
    public static function rules_template( $val, $grp ){

        $rules = self::rules_list();
        
        $operators = array(
            array( 'equal', 'is' ),
            array( 'not-equal', 'is not' )
        );
        
        $loc_pages = '';
        $loc_operators = '';
        
        foreach( $rules as $k => $v ){
            $s = selected( $k, $val[ 0 ], false );
            
            if( isset( $v[ 'helper' ] ) ){
                $h = 'data-helper="' . $v[ 'helper' ] . '"';
            }
            
            $p = isset( $v[ 'placeholder' ] ) ? ' data-placeholder="' . $v[ 'placeholder' ] . '"' : '';
            $loc_pages .= '<option value="' . $k . '" ' . $s . $h . $p . '>' . $v[ 'name' ] . '</option>';
        }
        
        foreach( $operators as $k => $v ){
            $s = selected( $v[0], $val[1], false );
            $loc_operators .= '<option value="' . $v[0] . '" ' . $s . '>' . $v[1] . '</option>';
        }
        
        $rule = '<div class="loc_rule_wrap"><select class="loc_page">' . $loc_pages . '</select><select class="loc_operator">' . $loc_operators . '</select><input type="text" class="loc_value" value="' . $val[2] . '" placeholder="" title="' . __( 'Leave empty to show in all', 'wpsr' ) . '"/><a href="#" class="button loc_rule_add" title="' . __( 'Add another criteria to match', 'wpsr' ) . '">and</a><a href="#" class="button loc_rule_remove" title="' . __( 'Remove criteria', 'wpsr' ) . '"><i class="fas fa-minus"></i></a></div>';
        
        if( $grp ) return '<div class="loc_group_wrap">' . $rule . '</div>';
        else return $rule;
    }
    
    public static function array_it( $ids ){
        
        return array_filter( array_map( 'trim', explode( ',', $ids ) ) );
        
    }
    
    public static function rule_is_single( $mode, $ids = '' ){
        
        if( $mode == 1 ){ // Rule selectors
            $list = array();

            $posts = get_posts( 'posts_per_page=-1&post_type=post' );
            if ( !empty( $posts ) ){
                foreach ( $posts as $post ){
                    $list[ $post->ID ] = $post->post_title;
                }
                return $list;
            }else{
                die( __( 'No posts !', 'wpsr' ) );
            }
            
        }elseif( $mode == 2 ){ // Rule check
            return is_single( self::array_it( $ids ) );
        }
        
    }
    
    
    public static function rule_is_page( $mode, $ids = '' ){
        
        if( $mode == 1 ){ // Rule selectors
            $list = array();

            $pages = get_posts( 'posts_per_page=-1&post_type=page' );
            if ( !empty( $pages ) ){
                foreach ( $pages as $page ){
                    $list[ $page->ID ] = $page->post_title;
                }
                return $list;
            }else{
                die( __( 'No pages !', 'wpsr' ) );
            }
            
            return $list;
            
        }elseif( $mode == 2 ){ // Rule check
            return is_page( self::array_it( $ids ) );
        }
        
    }
    
    public static function rule_post_type( $mode, $ids = '' ){
        
        if( $mode == 1 ){ // Rule selectors
        
            return get_post_types( array( 'public' => true ) );
            
        }elseif( $mode == 2 ){ // Rule check
            
            $post_types = self::array_it( $ids );
            return in_array( get_post_type(), $post_types );
            
        }
        
    }
    
    public static function rule_is_post_format( $mode, $ids = '' ){
        
        if( $mode == 1 ){ // Rule selectors
        
            return array(
                'standard' => 'Standard/No post format set',
                'aside' => 'Aside',
                'chat' => 'Chat',
                'gallery' => 'Gallery',
                'link' => 'Link',
                'image' => 'Image',
                'quote' => 'Quote',
                'status' => 'Status',
                'video' => 'Video',
                'audio' => 'Audio'
            );
            
        }elseif( $mode == 2 ){ // Rule check
            
            $selected_post_formats = self::array_it( $ids );
            $format = get_post_format() ? get_post_format() : 'standard';
            return in_array( $format, $selected_post_formats );
            
        }
        
    }
    
    public static function rule_is_archive( $mode, $ids = '' ){
        
        if( $mode == 1 ){ // Rule selectors
        }elseif( $mode == 2 ){ // Rule check
            return is_archive();
        }
        
    }
    
    public static function rule_is_category( $mode, $ids = '' ){
        
        if( $mode == 1 ){ // Rule selectors
            
            $list = array();
            $cats = get_categories();
            
            if( !empty( $cats ) ){
                foreach( $cats as $cat ){
                    $list[ $cat->slug ] = $cat->name;
                }
                return $list;
            }else{
                die( __( 'No categories !', 'wpsr' ) );
            }
            
        }elseif( $mode == 2 ){ // Rule check
            return is_category( self::array_it( $ids ) );
        }
        
    }
    
    
    public static function rule_is_tag( $mode, $ids = '' ){
        
        if( $mode == 1 ){ // Rule selectors
            
            $list = array();
            $tags = get_tags();
            
            if( !empty( $tags ) ){
                foreach( $tags as $tag ){
                    $list[ $tag->slug ] = $tag->name;
                }
                return $list;
            }else{
                die( __( 'No tags !', 'wpsr' ) );
            }
            
        }elseif( $mode == 2 ){ // Rule check
            return is_tag( self::array_it( $ids ) );
        }
        
    }
    
    public static function rule_is_date( $mode, $ids = '' ){
        
        if( $mode == 1 ){ // Rule selectors
        }elseif( $mode == 2 ){ // Rule check
            return is_date();
        }
        
    }
    
    public static function rule_has_category( $mode, $ids = '' ){
        
        if( $mode == 1 ){ // Rule selectors
            
            $list = array();
            $cats = get_categories();
            
            if( !empty( $cats ) ){
                foreach( $cats as $cat ){
                    $list[ $cat->slug ] = $cat->name;
                }
                return $list;
            }else{
                die( __( 'No categories !', 'wpsr' ) );
            }
            
        }elseif( $mode == 2 ){ // Rule check
            return has_category( self::array_it( $ids ) );
        }
        
    }
    
    public static function rule_has_tag( $mode, $ids = '' ){
        
        if( $mode == 1 ){ // Rule selectors
            
            $list = array();
            $tags = get_tags();
            
            if( !empty( $tags ) ){
                foreach( $tags as $tag ){
                    $list[ $tag->slug ] = $tag->name;
                }
                return $list;
            }else{
                die( __( 'No tags !', 'wpsr' ) );
            }
            
        }elseif( $mode == 2 ){ // Rule check
            return has_tag( self::array_it( $ids ) );
        }
        
    }
    
    public static function rule_is_home( $mode, $ids = '' ){
        
        if( $mode == 1 ){
        }elseif( $mode == 2 ){
            return is_home();
        }
        
    }
    
    public static function rule_is_frontpage( $mode, $ids = '' ){
        
        if( $mode == 1 ){
        }elseif( $mode == 2 ){
            return is_front_page();
        }
        
    }
    
    public static function rule_is_sticky( $mode, $ids = '' ){
        
        if( $mode == 1 ){
        }elseif( $mode == 2 ){
            return is_sticky();
        }
        
    }
    
    public static function rule_is_404( $mode, $ids = '' ){
        
        if( $mode == 1 ){ // Rule selectors
        }elseif( $mode == 2 ){ // Rule check
            return is_404();
        }
        
    }
    
}

WPSR_Location_Rules::init();

?>