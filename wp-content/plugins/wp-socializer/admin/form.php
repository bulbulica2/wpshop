<?php
/**
  * Form builder class
  * 
  **/

defined( 'ABSPATH' ) || exit;

class WPSR_Form{

    public $fields = array();
    public $current_field = array();
    public $values = array();
    public $options = array();

    function __construct( $values = array(), $options = array() ){
        $this->values = $values;
        $this->options = $options;
    }

    function start( $class = '', $attr = '', $custom = '' ){
        $this->current_field[ 'wrap_class' ] = $class;
        $this->current_field[ 'wrap_attr' ] = $attr;
    }

    function label( $name ){
        $this->current_field[ 'label' ] = $name;
    }

    function field( $type, $prop = array() ){
        $new_field = [
            'field_type' => $type,
            'field_prop' => $prop
        ];

        if( !isset( $this->current_field[ 'fields' ] ) ){
            $this->current_field[ 'fields' ] = [];
        }

        array_push( $this->current_field[ 'fields' ], $new_field );
    }

    function description( $desc ){
        $this->current_field[ 'description' ] = $desc;
    }

    function end(){
        if( !empty( $this->current_field ) ){
            array_push( $this->fields, $this->current_field );
            $this->current_field = [];
        }
    }

    function build(){

        $this->end();

        foreach( $this->fields as $field_wrap ){
            $class = !isset( $field_wrap[ 'wrap_class' ] ) ? 'class="field_wrap"' : ' class="field_wrap ' . $field_wrap[ 'wrap_class' ] . '" ';
            $attr = !isset( $field_wrap[ 'wrap_attr' ] ) ? '' : $field_wrap[ 'wrap_attr' ];

            echo '<div ' . $class . ' ' . $attr . '>';
            echo '<label class="field_label">' . $field_wrap[ 'label' ] . '</label>';
            echo '<div>';
            echo '<div class="field_val_wrap">';
            if( isset($field_wrap[ 'fields' ] ) ){
                foreach( $field_wrap[ 'fields' ] as $field ){
                    echo '<div class="field_val">';
                    echo self::field_html( $field[ 'field_type' ], $field[ 'field_prop' ] );
                    echo '</div>';
                }
            }
            echo '</div>';

            if( isset( $field_wrap[ 'description' ] ) ){
                echo '<p class="description">' . $field_wrap[ 'description' ] . '</p>';
            }

            echo '</div>';
            echo '</div>';
        }

        $this->fields = [];
    }

    function section_start( $heading = false, $step = false ){

        if( $heading ) echo '<h3 class="section_head" data-step="' . $step . '">' . $heading . '</h3>';
        echo '<section class="form_section">';

    }

    function section_end(){
        echo '</section>';
    }

    function section_description( $description = false ){
        if( $description ){
            echo '<p class="section_desc">' . $description . '</p>';
        }
    }

    function tab_list( $tabs ){

        echo '<ul class="tab_list">';
        foreach( $tabs as $id => $label ){
            echo '<li><a href="#' . $id . '">' . $label . '</a></li>';
        }
        echo '</ul>';

    }

    public static function field_html( $field_type, $field_props = array() ){

        $fields = array( 'text', 'select', 'image_select', 'radio', 'textarea' );

        $default_props = array(
            'id' => '',
            'name' => '',
            'class' => '',
            'value' => '',
            'list' => array(),
            'type' => '',
            'required' => '',
            'placeholder' => '',
            'rows' => '',
            'cols' => '',
            'helper' => '',
            'tooltip' => '',
            'before_text' => '',
            'after_text' => '',
            'custom' => ''
        );

        if( !in_array( $field_type, $fields ) ){
            return '';
        }

        $props = WPSR_Lists::set_defaults( $field_props, $default_props );
        $field_html = '';

        $props = WPSR_Admin::clean_attr( $props );
        extract( $props, EXTR_SKIP );

        $id_attr = empty( $id ) ? '' : 'id="' . $id . '"';
        $class_attr = empty( $class ) ? '' : 'class="' . $class . '"';

        if( !empty( $before_text ) ){
            $field_html .= "<span class='field_before_text'>$before_text</span>";
        }

        if( $field_type == 'text' ){
            $type = empty( $type ) ? 'text' : $type;
            $field_html .= "<input type='$type' $class_attr $id_attr name='$name' value='$value' placeholder='$placeholder' " . ( $required ? "required='$required'" : "" ) . " $custom />";
        }

        if( $field_type == 'select' ){
            $field_html .= "<select name='$name' $class_attr $id_attr $custom>";
            foreach( $list as $k => $v ){
                $field_html .= "<option value='$k' " . selected( $value, $k, false ) . ">$v</option>";
            }
            $field_html .= "</select>";
        }

        if( $field_type == 'image_select' ){
            $field_html .= "<select name='$name' class='$class img_select' $id_attr $custom>";
            foreach( $list as $k => $v ){
                $opt_name = ( count( $v ) >= 2 ) ? $v[0] : $v;
                $field_html .= "<option value='$k' " . selected( $value, $k, false ) . ">$opt_name</option>";
            }
            $field_html .= "</select>";
            $field_html .= "<ul class='img_select_list clearfix'>";
            foreach( $list as $k => $v ){
                $is_selected = ( $value == $k ) ? 'img_opt_selected' : '';
                $img = 'default_image.png';
                $opt_name = '';
                if( count( $v ) >= 2 ){
                    $opt_name = $v[0];
                    $img = $v[1];
                }else{
                    $opt_name = $v;
                }
                $img = ( substr( $img, 0, 4 ) !== 'http' ) ? ( WPSR_ADMIN_URL . 'images/select_images/' . $img ) : $img;
                $width = ( is_array( $v ) && isset( $v[2] ) ) ? "style='width:" . $v[2] . "'" : "";
                $field_html .= "<li data-value='$k' data-init='false' class='" . $is_selected . "' $width><img src='" . $img . "' /><span>" . $opt_name . "</span></li>";
            }
            $field_html .= "</ul>";
        }

        if( $field_type == 'radio' ){
            $field_html .= '<div class="radios_wrap">';
            foreach( $list as $k => $v ){
                $field_html .= "<label class='lbl_margin' $custom><input type='radio' name='$name' $class_attr value='$k' $id_attr " . checked( $value, $k, false ) . " />&nbsp;$v </label>";
            }
            $field_html .= '</div>';
        }

        if( $field_type == 'textarea' ){
            $field_html .= "<textarea $id_attr name='$name' $class_attr placeholder='$placeholder' rows='$rows' cols='$cols' $custom>$value</textarea>";
        }

        if( !empty( $after_text ) ){
            $field_html .= "<span class='field_after_text'>$after_text</span>";
        }

        if( !empty( $helper ) )
            $field_html .= "<p class='description'>$helper</p>";

        return $field_html;

    }

}

?>