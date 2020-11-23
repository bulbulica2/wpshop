(function($){
jQuery(document).ready(function(){
    
    var init = function(){

        if( $.fn.sortable ){

            $( '.fb_selected' ).sortable({
                axis: 'y',
                handle: 'h4',
                placeholder: 'ui-sortable-placeholder'
            });
            
            $( '.ssb_selected_list' ).sortable({
                stop: process_tsb_editor
            });
            
        }
        
        loc_sub_criteria();
        
        loc_generate_rules();
        
        feature_toggle();
        
        feature_toggle_btn();
        
        $( '.loc_page' ).each( function(){
            loc_update_rule_helper( $(this) );
        });
        
        if( $.fn.conditioner ){
            $('[data-conditioner]').conditioner();
        }
        
        if( $.fn.wpColorPicker ){
            $('.color_picker').wpColorPicker();
        }
        
        $('.template_wrap').hide().first().show();
        $('.template_tab li').first().addClass( 'templ_tab_active' );
        
        if( window.wpsr_show ){
            if( wpsr_show[ 'changelog' ] != 'false' ){
                changelog_show( window.wpsr_show[ 'changelog' ] );
            }
        }

        tabs();

        wpsr_init_image_selects();

    }
    
    var loc_generate_rules = function(){
    
        $( '.loc_rules_wrap' ).each(function(){
            
            tval = new Array();
            $wrap = $(this);
            $tinfo = $wrap.find( '.loc_rule_info' );
            $gadd = $wrap.find( '.loc_group_add' );
            $rule_box = $wrap.find( '.loc_rule_value' );
            $tgrp = $wrap.find( '.loc_rules_box .loc_group_wrap' );
            
            i = 0;
            $( $tgrp ).each(function(){
                $trle = $(this).find( '.loc_rule_wrap' );
                j = 0;
                tval[i] = new Array();
                $( $trle ).each(function(){
                    
                    tval[i][j] = [
                        $(this).find( '.loc_page' ).val(),
                        $(this).find( '.loc_operator' ).val(),
                        $(this).find( '.loc_value' ).val()
                    ];
                    
                    j++;
                });
                i++;
            });
            
            $rule_box.val( btoa( JSON.stringify( tval ) ) );
            
            if( $tgrp.length == 0 ){
                $tinfo.show();
                $gadd.text( 'Add new rules' );
            }else{
                $tinfo.hide();
                $gadd.text( ' Add another page ' );
            }
            
        });
    }
    
    var loc_sub_criteria = function(){
        $('.loc_group_wrap').each(function(){
            $master_rule = $(this).find( '.loc_rule_wrap:first-child' );
            master = $master_rule.find( '.loc_page' ).val();
            
            $( this ).find( '.loc_rule_wrap' ).each(function(){
                if( $(this).index() == 0 )
                    return true;
                $(this).find( '.loc_page option' ).each(function(){
                    if( $.inArray( $(this).val(), wpsr.loc_rules[ master ][ 'children' ] ) == -1 ){
                        $(this).remove();
                    }
                });
            });
            
            if( 'children' in wpsr.loc_rules[ master ] && wpsr.loc_rules[ master ][ 'children' ].length > 0 ){
                $master_rule.find( '.loc_rule_add' ).show()
            }else{
                $master_rule.find( '.loc_rule_add' ).hide()
            }
            
        });
        
        $( '.loc_page' ).each(function(){
            loc_update_rule_helper( $(this) );
        });
    }
    
    var loc_add_rule = function( group, btn ){
        grp_temp = $( '.loc_rules_temp' ).html();
        rule_temp = $( '.loc_rules_temp .loc_group_wrap').html();
        
        if( group ){
            btn.closest( '.loc_rules_wrap' ).find('.loc_rules_box').append( grp_temp );
        }else{
            btn.closest( '.loc_group_wrap' ).append( rule_temp );
        }
        
        loc_sub_criteria();
        loc_generate_rules();
    }

    var loc_remove_rule = function( btn ){
        $rule = btn.parent();
        $grp = $rule.parent();

        if( $rule.index() == 0 ){
            $grp.empty();
        }
        
        $rule.remove();
        
        if( $grp.children().length == 0 ){
            $grp.remove();
        }
        
        loc_generate_rules();
    }

    var loc_update_rule_helper = function( pageBtn ){
        
        helper = pageBtn.find( 'option:selected' ).attr( 'data-helper' );
        
        if( helper == 0 ){
            pageBtn.siblings( '.loc_operator, .loc_value' ).hide();
        }else{
            pageBtn.siblings( '.loc_operator, .loc_value' ).show();
        }
        
        placeholder = pageBtn.find( 'option:selected' ).attr( 'data-placeholder' );
        if( placeholder ){
            pageBtn.siblings( '.loc_value' ).attr( 'placeholder', placeholder );
        }
        
    }

    var feature_toggle = function(){
        var $ft_wrap = $( '.feature_wrap' );
        
        if( $( '[name="ft_status"]' ).val() == 'enable' ){
            $ft_wrap.removeClass( 'ft_disable' );
        }else{
            $ft_wrap.addClass( 'ft_disable' );
        }
    }
    
    var feature_toggle_btn = function(){
        $( '[name="ft_status"]' ).each(function(){
            $btn = $( '<i class="fa ft_toggle_btn" title="Toggle feature"></i>' );
            if( $(this).val() == 'enable' ){
                $btn.addClass( 'fa-toggle-on' );
            }else{
                $btn.addClass( 'fa-toggle-off' );
            }
            $(this).after( $btn );
        });
    }
    
    var tabs = function(){

        $('.tab_list li:first-child').addClass('active');
        $('.tab_wrap > div:first-child').addClass('active');

        $('.tab_list a').click(function(e){

            e.preventDefault();

            var id = $(this).attr('href').substr(1);

            var $tab_list = $(this).closest('.tab_list');
            var $tab_wrap = $tab_list.next('.tab_wrap');

            $tab_wrap.children('div').removeClass('active');
            $tab = $tab_wrap.find('[data-tab="' + id + '"]');
            $tab.addClass('active');

            $tab_list.find('li').removeClass('active');
            $(this).parent().addClass('active');

        });

    }

    var changelog_show = function( version ){
        vFile = wpsr.ext_res[ 'wp-socializer-cl' ] + version + '.html';
        $wcPopup = $( '.welcome_wrap' );
        $.get( vFile, function(data){
            $wcPopup.find( 'section' ).html( data );
            $wcPopup.fadeIn( 'fast' );
        });
        window.changelog_on = true;
    }
    
    var changelog_hide = function(){
        var url = wpsr.ajaxurl + '?action=wpsr_admin_ajax&do=close_changelog';

        $('.close_changelog_btn').append( '<i class="fas fa-spinner fa-spin changelog_icon"></i>' );

        $.get( url, function( data ){
            if( data.search( /done/g ) == -1 ){
                $( '.welcome_wrap section' ).html( 'Failed to close window. <a href="' + url + '" target="_blank">Please click here to close</a>' );
            }else{
                $( '.welcome_wrap' ).fadeOut();
                $(' .changelog_icon' ).remove();
            }
        });
        
        window.changelog_on = false;

    }

    // Attach the events
    
    $(document).on('submit', function(e){
        loc_generate_rules();
    });

    $(document).on( 'change', '.loc_rule_select', function(e){
        
        $parent = $(this).parent();
        $parent.find( '.loc_rule_selector, .loc_btn_menu' ).remove();
        
        $.get( wpsr.ajaxurl, {
        
            action: 'wpsr_location_rules',
            rule_id: $(this).val()
            
        }).done(function( data ){
            
            $parent.append( '<span class="loc_rule_selector">' + data + '</span>' );
            
        });
        
    });
    
    $(document).on( 'click', '.loc_rules_menu', function(){
        $(this).siblings('.loc_rule_selector').fadeToggle('fast');
    });
    
    $(document).on( 'click', '.loc_rules_remove', function(){
        $(this).parent().remove();
    });
    
    $(document).on( 'click', '.add_loc_rule', function(e){
        e.preventDefault();
        
        rule = $('.loc_rules_temp').html();
        rule = rule.replace( '%rule_id%', $(this).attr( 'data-id' ) );
        $(this).siblings( '.loc_rules_list' ).append( '<li>' + rule + '</li>' );
        
    });
    
    $(document).on( 'click', '.loc_group_add', function(e){
        e.preventDefault();
        loc_add_rule( true, $(this) );
    });
    
    $(document).on( 'click', '.loc_rule_add', function(e){
        e.preventDefault();
        loc_add_rule( false, $(this) );
    });
    
    $(document).on( 'click', '.loc_rule_remove', function(e){
        e.preventDefault();
        loc_remove_rule( $(this) );
    });

    $(document).on( 'click', '.loc_value', function(e){
        $list = $(this).siblings( '.loc_page' )
        val = $list.val();
        helper = $list.find( 'option:selected' ).attr( 'data-helper' );
        
        if( helper == "1" ){
            wpsr_admin_tooltip({
                parent: $(this),
                class: 'loc_rules_tt',
                height: '200px',
                content: {
                    url: wpsr.ajaxurl,
                    data: {
                        action: 'wpsr_location_rules',
                        rule_id: val,
                        selected: $(this).val()
                    }
                }
            });
        }
    });
    
    $(document).on( 'click', '.loc_rules_tt input[type="checkbox"]', function(e){
        temp = [];
        $(this).closest( '.loc_rules_tt' ).find( 'input[type="checkbox"]' ).each(function(){
            if( $(this).is(':checked') )
                temp.push( $(this).val() );
        });
        document.wpsr_tt_parent.val( temp );
    });
    
    $(document).on( 'change', '.loc_page', function(e){
        loc_update_rule_helper( $(this) );
        wpsr_admin_tooltip_close();
        $(this).siblings( '.loc_value' ).val( '' );
        
        if( $(this).closest( '.loc_rule_wrap' ).index() == 0 ){
            $(this).closest( '.loc_group_wrap' ).children().not(':first-child' ).remove();
            loc_sub_criteria();
        }
        
    });

    $( document ).on( 'click', '.fb_add', function(){
        $sel_list = $( '.fb_selected' );
        sel_val = $( '.fb_list' ).val();
        props = social_icons[ sel_val ];
        li_tmpl = window.li_template;
        
        li_tmpl = li_tmpl.replace( /%id%/g, sel_val );
        li_tmpl = li_tmpl.replace( /%color%/g, props[ 'colors' ][0] );
        li_tmpl = li_tmpl.replace( /%name%/g, props[ 'name' ] );
        li_tmpl = li_tmpl.replace( /%icon%/g, props[ 'icon' ] );
        li_tmpl = li_tmpl.replace( /%url%/g, '' );
        li_tmpl = li_tmpl.replace( /%iurl%/g, '' );
        li_tmpl = li_tmpl.replace( /%text%/g, '' );
        $li_tmpl = $( li_tmpl );

        $sel_list.append( $li_tmpl );
        
        $sel_list.find( 'div' ).slideUp();
        $li_tmpl.find( 'div' ).slideDown();

    });
    
    $( document ).on( 'click', '.fb_selected h4', function(e){
        e.preventDefault();
        var $to_open = $(this).closest( 'li' ).find( 'div' );
        $( '.fb_selected li > div' ).not($to_open).hide();
        $to_open.slideToggle();
    });
    
    var process_fb_editor = function(){
        
        cnt = [];
        prev = '';
        
        $( '.fb_selected li' ).each(function(){
           sid = $(this).data( 'id' );
           burl = $(this).find( '.fb_item_url' ).val();
           iurl = $(this).find( '.fb_icon_url' ).val();
           text = $(this).find( '.fb_btn_text' ).val();
           btn = {};
           
           btn[ sid ] = {
               'url': burl,
               'icon': iurl,
               'text': encodeURIComponent( text )
           };
           
           cnt.push( btn );
           
           // For preview
           pcolor = social_icons[ sid ][ 'colors' ][0];
           pname = social_icons[ sid ][ 'name' ];
           picon = social_icons[ sid ][ 'icon' ];
           
           prev += '<li style="background-color:' + pcolor + '" title="' + pname + '"><i class="' + picon + '"></i></li>';
           
        });
        
        template = btoa( JSON.stringify( cnt ) );
        $( '.fb_template' ).val( template );
        
        if( prev == '' && window.wpsr ){
            prev = '<span>' + window.wpsr.js_texts.fb_empty + '</span>';
        }
        
        return '<ul class="fb_preview">' + prev + '</ul>';
    }
    
    $( document ).on( 'click', '.fb_item_remove', function(e){
        e.preventDefault();
        $(this).closest( 'li' ).remove();
    });
    
    var process_tsb_editor = function(){
        selected = [];
        
        $( '.ssb_selected_list li' ).each(function(){
            selected.push( $(this).data( 'id' ) );
        });
        
        $( '.ssb_template' ).val( btoa( JSON.stringify( selected ) ) );
        
    }
    
    $( document ).on( 'click', '.ssb_add', function(e){
        e.preventDefault();
        $slist = $( '.ssb_selected_list' );
        $list = $( '.ssb_list' );
        var sel_val = $list.val();
        var color = $list.find('option:selected').data('color');
        
        $slist.find( '.ssb_empty' ).remove();
        $slist.append( '<li title="' + sb_sites[ sel_val ][ 'name' ] + '" data-id="' + sel_val + '" style="background-color:' + color + '"><i class="' + sb_sites[ sel_val ][ 'icon' ] + '"></i><span class="ssb_remove">x</span></li>' );
        
        process_tsb_editor();
        
    });
    
    $( document ).on( 'click', '.ssb_remove', function(){
        $(this).parent().remove();
        process_tsb_editor();
    });
    
    $( document ).on( 'click', '.fb_preview li', function(){
        alert( 'Please click "open editor" to rearrange the buttons' );
    });
    
    // Import data
    $( document ).on( 'submit', '#import_form', function( e ){
        e.preventDefault();
        
        var import_val = $(this).find( '[name="import_data"]' ).val();
        
        $.ajax({
            url: wpsr.ajaxurl,
            method: 'POST',
            data: {
                action: 'wpsr_import_ajax',
                import_data: import_val,
                _wpnonce: $(this).find( '[name="_wpnonce"]' ).val(),
            }
            
        }).done(function(d){
            if( d.search( /import_success/g ) != -1 ){
                $( '.notice-success' ).fadeIn();
            }
            if( d.search( /import_failed|auth_error/g ) != -1 ){
                $( '.notice-error' ).fadeIn();
            }
        });
    });
    
    $( document ).on( 'click', '.template_tab li', function(){
        id = $(this).index() + 1;
        $('.template_tab li').removeClass( 'templ_tab_active' );
        $('.template_wrap').hide();
        $('.template_wrap[data-id="' + id + '"]').fadeIn( 'slow' );
        $(this).addClass( 'templ_tab_active' );
    });

    // Popup editor on click events
    $( document ).on( 'click', '.wpsr_ppe_save', function(){
        mode = $(this).data( 'mode' );
        
        if( self != top ){
            
            close_popup = true;
            
            cnt_id = $(this).data( 'cnt-id' );
            prev_id = $(this).data( 'prev-id' );
            
            cnt_val = '';
            prev_val = '';
            
            if( mode == 'widget' ){
                process_vedit();
                
                cnt_val = $( '#wpsr_pp_editor .veditor_content' ).val();
                prev_val = $( '#wpsr_pp_editor .veditor' )[0].outerHTML;
                
            }
            
            if( mode == 'followbar' ){
                
                $( '.fb_selected li' ).each(function(){
                   sid = $(this).data( 'id' );
                   burl = $(this).find( '.fb_item_url' ).val();
                   if( burl == '' ){
                       var uprompt = confirm( sid + ' does not have any URL set to follow. Please enter an URL by clicking edit.' );
                       close_popup = !uprompt;
                       $(this).addClass( 'not_set' );
                   }else{
                       $(this).removeClass( 'not_set' );
                   }
                });
                
                prev_val = process_fb_editor();
                cnt_val = $( '.fb_template' ).val();
            }
            
            window.parent.document.getElementById( cnt_id ).value = cnt_val;
            window.parent.document.getElementById( prev_id ).innerHTML = prev_val;
            
            if( window.parent.wpsr_ipopup_close && close_popup ){
                window.parent.wpsr_ipopup_close();
            }
            
        }
    });

    $( document ).on( 'click', '.wpsr_ppe_cancel', function(){
        if( window.parent.wpsr_ipopup_close ){
            window.parent.wpsr_ipopup_close();
        }
    });
    
    $( document ).on( 'click', '.wpsr_ppe_fb_open', function(e){
        e.preventDefault();
        
        if( wpsr.ajaxurl ){
            
            cnt_id = $( this ).attr( 'data-cnt-id' );
            cnt = $( '#' + cnt_id ).val();
            prev_id = $( this ).attr( 'data-prev-id' );
            qstring = 'action=wpsr_follow_icons_editor&template=' + cnt + '&cnt_id=' + cnt_id + '&prev_id=' + prev_id;
            
            wpsr_ipopup_show( wpsr.ajaxurl + '?' + qstring, '80%', '80%' );
            
        }
    });
    
    $( document ).on( 'click', '.close_changelog_btn', function(e){
        e.preventDefault();
        changelog_hide();
    });
    
    $( document ).on( 'click', '.tblr_btn', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $('[data-tglr="' + id + '"]').toggle();
    });
    
    $( document ).on( 'click', '.ft_toggle_btn', function(){
        var on = 'fa-toggle-on';
        var off = 'fa-toggle-off';
        var $btn = $(this);
        var $sel = $(this).prev();
        
        if( $btn.hasClass( on ) ){
            $btn.removeClass( on );
            $btn.addClass( off );
            $sel.val( 'disable' );
        }else{
            $btn.removeClass( off );
            $btn.addClass( on );
            $sel.val( 'enable' );
        }
        feature_toggle();
    });
    
    $( document ).on( 'change', '.cfe_amt', function(){
        var $btn = $(this).closest('.cfe_form').find('.cfe_btn');
        $btn.attr('href', $btn.data('link') + $(this).val());
    });

    // V5
    $( document ).on( 'keyup', '.sip_filter', function(){
        $list = $( '.sip_selector' );
        if( $list.length > 0 ){
            val = $( this ).val();
            
            $list.children().each(function(){
                $item = $(this);
                text = $item.text().toLowerCase();
                if( text.search( val.toLowerCase() ) == -1 ){
                    $item.hide();
                }else{
                    $item.show();
                }
            });
        }
    });

    $( document ).on( 'click', '.sip_selector li', function(){
        var $sie_list = window.sie_active_editor.find( '.sie_selected' );
        $(this).clone().appendTo( $sie_list );
        sie_update_selection();
        sic_close_popup();
    });

    $( document ).on( 'click', '.sic_close_btn', function(){
        sic_close_popup();
    });

    $( document ).on( 'click', '.sie_open_picker_btn', function( e ){
        e.preventDefault();
        window.sie_active_editor = $(this).closest( '.sie_wrap' );
        $('.sip_picker').fadeIn();
    });

    $( document ).on( 'click', '.sie_delete_btn', function(){
        $(this).parent().remove();
        sie_update_selection();
    });

    $( document ).on( 'click', '.sie_settings_btn', function(){
        $icon = $(this).parent();
        $data = $icon.data();
        window.sie_current_icon = $icon;

        $popup = $( '.sie_icon_settings' ).show();
        $popup.find( 'h3' ).text( sip_icons[ $icon.data( 'id' ) ][ 'name' ] + ' icon (Advanced settings)' );
        $cnt = $( '.sie_icon_settings section' ).empty().append( '<table class="form-table"></table>' );
        $tbl = $cnt.find( 'table' );
        
        for( opt in $data ){
            
            if( opt.search( 'icns' ) != -1 ){
                
                opt_val = $data[ opt ];
                the_opt = opt.replace( 'icns_', '' );
                
                if( typeof sip_icon_settings[ the_opt ] === 'undefined' )
                    continue;
                
                $wrap = $( '<tr><th></th><td></td></tr>' );
                $checkbox = $( '<input type="checkbox" value="1" class="sie_is_input" data-type="checkbox" />' );
                $text = $( '<input type="text" class="widefat sie_is_input" data-type="text" />' );
                $textarea = $( '<textarea class="widefat sie_is_input" data-type="textarea"></textarea>' );
                
                helper = sip_icon_settings[ the_opt ][ 'helper' ];
                type = sip_icon_settings[ the_opt ][ 'type' ];
                placeholder = ( 'placeholder' in sip_icon_settings[ the_opt ] ) ? sip_icon_settings[ the_opt ][ 'placeholder' ] : '';
                
                $the_input = $( '<i/>' );
                
                if( type == 'checkbox' ){
                    if( opt_val == '1' || opt_val == 'true' )
                        $checkbox.attr('checked', 'checked');
                    
                    $the_input = $checkbox.attr( 'data-id', the_opt );
                }
                
                if( type == 'text' ){
                    $the_input = $text.val( opt_val ).attr( 'data-id', the_opt );
                }
                
                if( type == 'textarea' ){
                    $the_input = $textarea.val( opt_val ).attr( 'data-id', the_opt );
                }

                $wrap.find( 'th' ).append( helper );
                $wrap.find( 'td' ).append( $the_input );
                
                if( placeholder != '' )
                    $wrap.find( 'td' ).append( '<small>' + placeholder + '</small>' );
                
                $tbl.append( $wrap );
                
            }
        }
    });

    $( document ).on( 'click', '.sie_save_settings_btn', function( e ){
        e.preventDefault();

        $icon = window.sie_current_icon;

        if( $icon.length ){
            $popup = $( this ).closest( '.sie_icon_settings' );
            $inputs = $popup.find( '.sie_is_input' );

            $inputs.each( function(){

                $i = $( this );
                id = $i.data( 'id' );
                type = $i.data( 'type' );
                value = '';

                if( type == 'checkbox' && $i.is( ':checked' ) )
                    value = '1';

                if( type == 'text' )
                    value = $i.val();

                if( type == 'textarea' )
                    value = $i.val();

                $icon.data( 'icns_' + id, value );

            });

        }

        sic_close_popup();
        sie_update_selection();

    });

    $( '.sie_selected' ).sortable({
        stop: function(){
            sie_update_selection();
        }
    }).disableSelection();

    var sic_close_popup = function(){
        $( '.sic_backdrop' ).fadeOut();
    }

    var sie_update_selection = function(){

        $('.sie_selected').each(function(){

            var selected_icons = [];
            var $selected_icons_input = $(this).closest('.sie_wrap').find('.sie_selected_icons');

            $(this).find('li').each(function(){
                var id = $(this).data( 'id' );
                var datas = $(this).data();
                var icon = {};
                icon[ id ] = {};
    
                for( d in datas ){
                    if( d.search( 'icns_' ) != -1 ){
                        setting = d.replace( 'icns_', '' );
                        val = datas[ d ];
    
                        icon[ id ][ setting ] = val;
                    }
                }
    
                selected_icons.push( icon );
            });

            console.log( selected_icons );
            $selected_icons_input.val( btoa( JSON.stringify( selected_icons ) ) );

        });

    }

    // Initinitinitinitinit
    init();
    
});
})( jQuery );

function wpsr_admin_tooltip( o ){
    
    if( !o.parent.is( document.wpsr_tt_parent ) ){
        wpsr_admin_tooltip_close();
    }else{
        return false;
    }
    
    $tt = jQuery('<div class="wpsr_tooltip_wrap"><i class="fa fa-times wpsr_tooltip_close" title="' + wpsr.js_texts.close + '"></i><div class="wpsr_tooltip_cnt"></div></div>');
    
    $parent = o.parent;
    document.wpsr_tt_parent = $parent;
    
    if( o.class ) $tt.addClass( o.class );
    if( o.width ) $tt.width( o.width );
    if( o.height ) $tt.height( o.height );
    if( o.name ) $tt.attr( 'data-name', o.name );
    
    $tt.css({
        position: 'absolute',
        top: $parent.offset().top + $parent.outerHeight(),
        left: $parent.offset().left
    });
    
    $tt.appendTo( 'body' );
    
    if( typeof o.content == 'object' ){
        
        $tt.addClass( 'loading' );
        
        jQuery.ajax(o.content).done(function(data){
            $tt.removeClass( 'loading' );
            $tt.find('.wpsr_tooltip_cnt').html( data );
            
            $footer = $tt.find( '.btn_settings_footer' );
            if( $footer.length > 0 ){
                $footer.appendTo( '.wpsr_tooltip_wrap' );
                $tt.find('.wpsr_tooltip_cnt').addClass( 'tt_has_footer' );
            }
            
            if( jQuery.fn.wpColorPicker ){
                jQuery( '.wp-color' ).wpColorPicker();
            }
            
            wpsr_init_image_selects();
            
        });
        
    }else{
        
        $tt.find('.wpsr_tooltip_cnt').html( o.content );
        
    }
    
    if( o.class && o.class.search( 'wpsr_tooltip_popup' ) != -1 )
        jQuery( 'body' ).addClass( 'hide_scrollbar' );
    
    // Positioning adjust
    winwid = jQuery(window).width();
    ttwid = $tt.offset().left + $tt.outerWidth();
    
    if( winwid < ttwid  ){
        $tt.css( 'margin-left', -(ttwid+70-winwid));
    }
    
    jQuery('.wpsr_tooltip_close').click(function(){
        wpsr_admin_tooltip_close();
    });
    
}

function wpsr_admin_tooltip_close(){
    jQuery('.wpsr_tooltip_close').off( 'click' );
    jQuery('.wpsr_tooltip_wrap').remove();
    jQuery( 'body' ).removeClass( 'hide_scrollbar' );
    document.wpsr_tt_parent = false;
}

function wpsr_init_image_selects(){
    jQuery( '.img_select_list li' ).each(function(){
        $li = jQuery(this);
        if( $li.attr( 'data-init' ) == 'false' ){
            $li.on( 'click', function(){
                $the_li = jQuery(this);
                $parent = $the_li.parent();
                $org = $parent.prev();
                $parent.find( 'li' ).removeClass( 'img_opt_selected' );
                $the_li.addClass( 'img_opt_selected' );
                $org.val( $the_li.attr( 'data-value' ) );
                $org.trigger( 'change' );
            });
            $li.attr( 'data-init', 'true' );
        }
    });
}