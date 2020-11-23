function wpsr_ipopup_show( url, width = '640', height = '480' ){
    $popup = jQuery( '<div id="wpsr_ipopup_wrap"><section style="width:' + width + '; height:' + height + '"><iframe frameborder="0" src="' + url + '"></iframe><a href="#" class="wpsr_ipopup_close" onclick="wpsr_ipopup_close()" title="Close">X</a></section></div>' );
    wpsr_ipopup_close();
    
    jQuery( 'body' ).append( $popup );
}

function wpsr_ipopup_close(){
    jQuery( '#wpsr_ipopup_wrap' ).remove();
}