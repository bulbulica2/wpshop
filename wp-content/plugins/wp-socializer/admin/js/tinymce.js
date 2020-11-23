/*
 * WP Socializer TinyMCE plugin
*/

(function() {

    tinymce.create( 'tinymce.plugins.WPSRButton',{
    
        init : function(ed, url){
            console.log(url);
            var url = url.replace('/js', '');
            ed.addButton( 'wp-socializer', {
                title : 'Open WP Socializer shortcode reference',
                image : url + '/images/icons/wp-socializer.png',
                onclick : function(){
                    window.open('admin.php?page=wp_socializer&tab=shortcodes');
                }
            });
        },

        getInfo : function(){
            return {
                longname : 'WP Socializer',
                author : 'Aakash Chakravarthy',
                authorurl : 'https://www.aakashweb.com/',
                infourl : 'https://www.aakashweb.com/',
                version : '1.3'
            };
        }

    });

    tinymce.PluginManager.add( 'wp-socializer', tinymce.plugins.WPSRButton );

})();