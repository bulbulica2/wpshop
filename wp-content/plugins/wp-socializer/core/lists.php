<?php
/**
  * List of social media sites for social icons, default values for admin pages and list of external resources
  * 
  */

defined( 'ABSPATH' ) || exit;

class WPSR_Lists{
    
    public static function init(){
        // Nothing to Init
    }
    
    public static function set_defaults( $a, $b ){
        
        $a = (array) $a;
        $b = (array) $b;
        $result = $b;
        
        foreach ( $a as $k => &$v ) {
            if ( is_array( $v ) && isset( $result[ $k ] ) ) {
                $result[ $k ] = self::set_defaults( $v, $result[ $k ] );
            } else {
                $result[ $k ] = $v;
            }
        }
        return $result;
    }
    
    public static function ext_res( $name = 'all' ){
        
        $res = apply_filters( 'wpsr_mod_ext_res', array(
            'font-awesome-adm' => 'https://use.fontawesome.com/releases/v5.12.0/css/all.css',
            'wp-socializer-cl' => 'https://raw.githubusercontent.com/vaakash/vaakash.github.io/master/misc/wp-socializer/changelogs/'
        ));
        
        if( array_key_exists( $name, $res ) ){
            return $res[ $name ];
        }elseif( $name == 'all' ){
            return $res;
        }else{
            return '';
        }
        
    }
    
    public static function font_icons(){
        
        return apply_filters( 'wpsr_mod_font_icons', array(
            'fa4' => array(
                'name' => 'Font awesome 4',
                'type' => 'css',
                'link' => 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
                'deps' => array(),
                'version' => WPSR_VERSION
            ),
            'fa5' => array(
                'name' => 'Font awesome 5',
                'type' => 'css',
                'link' => 'https://use.fontawesome.com/releases/v5.12.0/css/all.css',
                'deps' => array(),
                'version' => WPSR_VERSION
            )
        ));
        
    }
    
    public static function get_font_icon(){
        
        $font_icons = self::font_icons();
        
        $gsettings = self::set_defaults( get_option( 'wpsr_general_settings' ), WPSR_Options::default_values( 'general_settings' ) );
        
        $sel_font_icon = $gsettings['font_icon'];
        $sel_font_icon = ($sel_font_icon == '') ? 'fa4' : $sel_font_icon;
        $sel_font_icon = is_admin() ? 'fa5' : $sel_font_icon;
        
        $icon_props = array(
            'id' => '',
            'prop' => array()
        );
        
        if( array_key_exists( $sel_font_icon, $font_icons ) ){
            $icon_props['id'] = $sel_font_icon;
            $icon_props['prop'] = $font_icons[ $sel_font_icon ];
        }
        
        return $icon_props;
        
    }
    
    public static function social_icons(){
        
        $all_icons = apply_filters( 'wpsr_mod_social_icons_list', array(
            'addtofavorites' => array(
                'name' => 'Add to favorites',
                'title' => 'Add to favorites',
                'icon' => array('fa4' => 'fa fa-star', 'fa5' => 'fa fa-star'),
                'link' => '#',
                'onclick' => 'socializer_addbookmark(event)',
                'options' => array(),
                'features' => array( 'for_share', 'requires_js' ),
                'colors' => array( '#F9A600' ),
            ),
            
            'behance' => array(
                'name' => 'Behance',
                'title' => __('', 'wpsr') . 'Behance',
                'icon' => array('fa4' => 'fa fa-behance', 'fa5' => 'fab fa-behance'),
                'link' => 'https://www.behance.net/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#1769ff' ),
            ),
            
            'bitbucket' => array(
                'name' => 'Bitbucket',
                'title' => __('', 'wpsr') . 'Bitbucket',
                'icon' => array('fa4' => 'fa fa-bitbucket', 'fa5' => 'fab fa-bitbucket'),
                'link' => 'https://bitbucket.org/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#205081' ),
            ),
            
            'blogger' => array(
                'name' => 'Blogger',
                'title' => __('Post this on ', 'wpsr') . 'Blogger',
                'icon' => array('fa4' => 'fa fa-rss', 'fa5' => 'fa fa-rss-square'),
                'link' => 'https://www.blogger.com/blog-this.g?u={url}&n={title}&t={excerpt}',
                'options' => array(),
                'features' => array( 'for_share', 'for_tsb', 'for_profile' ),
                'colors' => array( '#FF6501' ),
            ),
            
            'codepen' => array(
                'name' => 'CodePen',
                'title' => __('', 'wpsr') . 'CodePen',
                'icon' => array('fa4' => 'fa fa-codepen', 'fa5' => 'fab fa-codepen'),
                'link' => 'https://codepen.io/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#000' ),
            ),
            
            'comments' => array(
                'name' => 'Comments',
                'title' => __('', 'wpsr') . 'Comments',
                'icon' => array('fa4'=> 'fa fa-comment', 'fa5' => 'fa fa-comments'),
                'link' => '#{comments-section}',
                'options' => array(),
                'features' => array( 'internal', 'for_tsb' ),
                'colors' => array( '#333' ),
            ),
            
            'delicious' => array(
                'name' => 'Delicious',
                'title' => __('Post this on ', 'wpsr') . 'Delicious',
                'icon' => array('fa4'=> 'fa fa-delicious', 'fa5' => 'fab fa-delicious'),
                'link' => 'https://delicious.com/post?url={url}&title={title}&notes={excerpt}',
                'options' => array(),
                'features' => array( 'for_share' ),
                'colors' => array( '#3274D1' ),
            ),
            
            'deviantart' => array(
                'name' => 'DeviantArt',
                'title' => __('', 'wpsr') . 'DeviantArt',
                'icon' => array('fa4'=> 'fa fa-deviantart', 'fa5' => 'fab fa-deviantart'),
                'link' => 'https://deviantart.com/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#475c4d' ),
            ),
            
            'digg' => array(
                'name' => 'Digg',
                'title' => __('Submit this to ', 'wpsr') . 'Digg',
                'icon' => array('fa4'=> 'fa fa-digg', 'fa5' => 'fab fa-digg'),
                'link' => 'https://digg.com/submit?url={url}&title={title}',
                'options' => array(),
                'features' => array( 'for_share' ),
                'colors' => array( '#000' ),
            ),
            
            'discord' => array(
                'name' => 'Discord',
                'title' => __('', 'wpsr') . 'Discord',
                'icon' => array('fa4'=> 'fa fa-discord', 'fa5' => 'fab fa-discord'),
                'link' => 'https://discord.com/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#7289da' ),
            ),

            'dribbble' => array(
                'name' => 'Dribbble',
                'title' => __('', 'wpsr') . 'Dribble',
                'icon' => array('fa4'=> 'fa fa-dribble', 'fa5' => 'fab fa-dribbble'),
                'link' => 'https://dribbble.com/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#ea4c89' ),
            ),
            
            'email' => array(
                'name' => 'Email',
                'title' => __('Email this ', 'wpsr') . '',
                'icon' => array('fa4'=> 'fa fa-envelope', 'fa5' => 'fa fa-envelope'),
                'link' => 'mailto:?to=&subject={title-plain}&body={excerpt-plain}%20-%20{url}',
                'link_tsb' => 'mailto:?to=&subject={title}&body={excerpt}%20-%20{url}',
                'options' => array(),
                'features' => array( 'for_share', 'for_profile', 'for_tsb' ),
                'colors' => array( '#000' ),
            ),
            
            'etsy' => array(
                'name' => 'Etsy',
                'title' => __('', 'wpsr') . 'Etsy',
                'icon' => array('fa4'=> 'fa fa-etsy', 'fa5' => 'fab fa-etsy'),
                'link' => 'https://www.etsy.com/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#f1641e' ),
            ),
            
            'facebook' => array(
                'name' => 'Facebook',
                'title' => __('Share this on ', 'wpsr') . 'Facebook',
                'icon' => array('fa4'=> 'fa fa-facebook-f', 'fa5' => 'fab fa-facebook-f'),
                'link' => 'https://www.facebook.com/share.php?u={url}',
                'link_tsb' => 'https://www.facebook.com/share.php?u={url}&quote={excerpt}',
                'options' => array( 'count' ),
                'features' => array( 'for_share', 'for_profile', 'for_tsb' ),
                'colors' => array( '#1977f3' ),
            ),
            
            'fbmessenger' => array(
                'name' => 'Facebook messenger',
                'title' => __('', 'wpsr') . 'Facebook messenger',
                'icon' => array('fa4'=> 'fa fa-comment', 'fa5' => 'fab fa-facebook-messenger'),
                'link' => 'https://www.facebook.com/dialog/send?app_id={fb-app-id}&link={url}&redirect_uri={url}',
                'link_mobile' => 'fb-messenger://share?link={url}',
                'options' => array(),
                'features' => array( 'mobile_only', 'for_share', 'for_profile' ),
                'colors' => array( '#2998ff' ),
            ),
            
            'flickr' => array(
                'name' => 'Flickr',
                'title' => __('', 'wpsr') . 'Flickr',
                'icon' => array('fa4'=> 'fa fa-flickr', 'fa5' => 'fab fa-flickr'),
                'link' => 'https://www.flickr.com',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#1c9be9' ),
            ),
            
            'github' => array(
                'name' => 'Github',
                'title' => __('', 'wpsr') . 'Github',
                'icon' => array('fa4'=> 'fa fa-github', 'fa5' => 'fab fa-github'),
                'link' => 'https://www.github.com/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#333' ),
            ),
            
            'google' => array(
                'name' => 'Google',
                'title' => __('Bookmark this on ', 'wpsr') . 'Google','',
                'icon' => array('fa4'=> 'fa fa-google', 'fa5' => 'fab fa-google'),
                'link' => 'https://www.google.com/bookmarks/mark?op=edit&bkmk={url}&title={title}&annotation={excerpt}',
                'options' => array(),
                'features' => array( 'for_share', 'for_tsb' ),
                'colors' => array( '#3A7CEC' ),
            ),
            
            'hackernews' => array(
                'name' => 'Hacker News',
                'title' => __('Share this on ', 'wpsr') . 'HackerNews',
                'icon' => array('fa4'=> 'fa fa-hacker-news', 'fa5' => 'fab fa-hacker-news'),
                'link' => 'https://news.ycombinator.com/submitlink?u={url}&t={title}',
                'options' => array(),
                'features' => array( 'for_share' ),
                'colors' => array( '#FF6500' ),
            ),
            
            'houzz' => array(
                'name' => 'Houzz',
                'title' => __('', 'wpsr') . 'Houzz',
                'icon' => array('fa4'=> 'fa fa-houzz', 'fa5' => 'fab fa-houzz'),
                'link' => 'https://houzz.com',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#4dbc15' ),
            ),

            'html' => array(
                'name' => 'Custom HTML',
                'title' => __('', 'wpsr'),
                'icon' => array('fa4'=> 'fa fa-code', 'fa5' => 'fa fa-code'),
                'link' => 'https://aakashweb.com',
                'options' => array(),
                'features' => array( 'for_share' ),
                'colors' => array( 'red' ),
            ),

            'instagram' => array(
                'name' => 'Instagram',
                'title' => __('', 'wpsr') . 'Instagram',
                'icon' => array('fa4'=> 'fa fa-instagram', 'fa5' => 'fab fa-instagram'),
                'link' => 'https://instagram.com',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#e23367' ),
            ),
            
            'line' => array(
                'name' => 'Line',
                'title' => __('', 'wpsr') . 'Line',
                'icon' => array('fa4'=> 'fa fa-commenting', 'fa5' => 'fab fa-line'),
                'link' => 'https://social-plugins.line.me/lineit/share?url={url}',
                'options' => array(),
                'features' => array( 'for_share', 'mobile_only' ),
                'colors' => array( '#00C300' ),
            ),
            
            'linkedin' => array(
                'name' => 'LinkedIn',
                'title' => __('Add this to ', 'wpsr') . 'LinkedIn',
                'icon' => array('fa4'=> 'fa fa-linkedin', 'fa5' => 'fab fa-linkedin-in'),
                'link' => 'https://www.linkedin.com/sharing/share-offsite/?url={url}',
                'options' => array(),
                'features' => array( 'for_share', 'for_profile', 'for_tsb' ),
                'colors' => array( '#0274B3' ),
            ),
            
            'medium' => array(
                'name' => 'Medium',
                'title' => __('', 'wpsr') . 'Medium',
                'icon' => array('fa4'=> 'fa fa-medium', 'fa5' => 'fab fa-medium-m'),
                'link' => 'https://medium.com',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#02b875' ),
            ),
            
            'mix' => array(
                'name' => 'Mix',
                'title' => __('', 'wpsr') . 'Mix',
                'icon' => array('fa4'=> 'fa fa-mix', 'fa5' => 'fab fa-mix'),
                'link' => 'https://mix.com/mixit?url={url}',
                'options' => array(),
                'features' => array( 'for_share' ),
                'colors' => array( '#ff8226' ),
            ),
            
            'odnoklassniki' => array(
                'name' => 'Odnoklassniki',
                'title' => __('', 'wpsr') . 'Odnoklassniki',
                'icon' => array('fa4'=> 'fa fa-odnoklassniki', 'fa5' => 'fab fa-odnoklassniki'),
                'link' => 'https://connect.ok.ru/dk?st.cmd=OAuth2Login&st.layout=w&st.redirect=%252Fdk%253Fcmd%253DWidgetSharePreview%2526amp%253Bst.cmd%253DWidgetSharePreview%2526amp%253Bst.shareUrl%253D{url}&st._wt=1&st.client_id=-1',
                'options' => array(),
                'features' => array( 'for_share', 'for_profile' ),
                'colors' => array( '#F2720C' ),
            ),
            
            'patreon' => array(
                'name' => 'Patreon',
                'title' => __('', 'wpsr') . 'Patreon',
                'icon' => array('fa4'=> 'fa fa-patreon', 'fa5' => 'fab fa-patreon'),
                'link' => 'https://patreon.com',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#e85b46' ),
            ),

            'paypal' => array(
                'name' => 'PayPal',
                'title' => __('', 'wpsr') . 'PayPal',
                'icon' => array('fa4'=> 'fa fa-paypal', 'fa5' => 'fab fa-paypal'),
                'link' => 'https://paypal.com',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#0070ba' ),
            ),
            
            'pdf' => array(
                'name' => 'PDF',
                'title' => __('Convert to ', 'wpsr') . 'PDF',
                'icon' => array('fa4'=> 'fa fa-file-pdf-o', 'fa5' => 'fa fa-file-pdf'),
                'link' => 'https://www.printfriendly.com/print?url={url}',
                'options' => array(),
                'features' => array( 'for_share' ),
                'colors' => array( '#E61B2E' ),
            ),
            
            'phone' => array(
                'name' => 'Phone',
                'title' => __('', 'wpsr') . 'Phone',
                'icon' => array('fa4' => 'fa fa-phone', 'fa5' => 'fa fa-phone'),
                'link' => '#',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#1A73E8' ),
            ),
            
            'pinterest' => array(
                'name' => 'Pinterest',
                'title' => __('Submit this to ', 'wpsr') . 'Pinterest',
                'icon' => array('fa4'=> 'fa fa-pinterest', 'fa5' => 'fab fa-pinterest'),
                'link' => 'https://www.pinterest.com/pin/create/button/?url={url}&media={image}&description={excerpt}',
                'options' => array( 'count' ),
                'features' => array( 'for_share', 'for_profile', 'for_tsb' ),
                'colors' => array( '#CB2027' ),
            ),
            
            'pocket' => array(
                'name' => 'Pocket',
                'title' => __('Submit this to ', 'wpsr') . 'Pocket',
                'icon' => array('fa4'=> 'fa fa-get-pocket', 'fa5' => 'fab fa-get-pocket'),
                'link' => 'https://getpocket.com/save?url={url}&title={title}',
                'options' => array(),
                'features' => array( 'for_share' ),
                'colors' => array( '#EF4056' ),
            ),
            
            'print' => array(
                'name' => 'Print',
                'title' => __('Print this article ', 'wpsr') . '',
                'icon' => array('fa4'=> 'fa fa-print', 'fa5' => 'fa fa-print'),
                'link' => 'https://www.printfriendly.com/print?url={url}',
                'options' => array(),
                'features' => array( 'for_share' ),
                'colors' => array( '#6D9F00' ),
            ),
            
            'reddit' => array(
                'name' => 'Reddit',
                'title' => __('Submit this to ', 'wpsr') . 'Reddit',
                'icon' => array('fa4'=> 'fa fa-reddit', 'fa5' => 'fab fa-reddit-alien'),
                'link' => 'https://reddit.com/submit?url={url}&title={title}',
                'options' => array(),
                'features' => array( 'for_share' ),
                'colors' => array( '#FF5600' ),
            ),
            
            'renren' => array(
                'name' => 'Renren',
                'title' => __('Submit this to ', 'wpsr') . 'Renren',
                'icon' => array('fa4'=> 'fa fa-renren', 'fa5' => 'fab fa-renren'),
                'link' => 'https://www.connect.renren.com/share/sharer?url={url}&title={title}',
                'options' => array(),
                'features' => array( 'for_share' ),
                'colors' => array( '#005EAC' ),
            ),
            
            'rss' => array(
                'name' => 'RSS',
                'title' => __('Subscribe to ', 'wpsr') . 'RSS',
                'icon' => array('fa4'=> 'fa fa-rss', 'fa5' => 'fa fa-rss'),
                'link' => '{rss-url}',
                'options' => array(),
                'features' => array( 'internal', 'for_profile' ),
                'colors' => array( '#FF7B0A' ),
            ),

            'shortlink' => array(
                'name' => 'Short link',
                'title' => __('', 'wpsr') . 'Short link',
                'icon' => array('fa4'=> 'fa fa-link', 'fa5' => 'fa fa-link'),
                'link' => '{short-url}',
                'onclick' => 'socializer_shortlink( event, this )',
                'options' => array(),
                'features' => array( 'internal', 'requires_js', 'for_share' ),
                'colors' => array( '#333' ),
            ),
            
            'snapchat' => array(
                'name' => 'Snapchat',
                'title' => __('', 'wpsr') . 'Snapchat',
                'icon' => array('fa4'=> 'fa fa-snapchat', 'fa5' => 'fab fa-snapchat'),
                'link' => 'https://snapchat.com',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#FFFC00' ),
            ),
            
            'skype' => array(
                'name' => 'Skype',
                'title' => __('', 'wpsr') . 'Skype',
                'icon' => array('fa4'=> 'fa fa-skype', 'fa5' => 'fab fa-skype'),
                'link' => 'https://web.skype.com/share?url={url}',
                'options' => array(),
                'features' => array( 'for_profile', 'for_share' ),
                'colors' => array( '#00AFF0' ),
            ),
            
            'soundcloud' => array(
                'name' => 'Soundcloud',
                'title' => __('', 'wpsr') . 'Soundcloud',
                'icon' => array('fa4'=> 'fa fa-soundcloud', 'fa5' => 'fab fa-soundcloud'),
                'link' => 'https://soundcloud.com/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#f50' ),
            ),
            
            'stackoverflow' => array(
                'name' => 'StackOverflow',
                'title' => __('', 'wpsr') . 'StackOverflow',
                'icon' => array('fa4'=> 'fa fa-stack-overflow', 'fa5' => 'fab fa-stack-overflow'),
                'link' => 'https://stackoverflow.com/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#F48024' ),
            ),
            
            'quora' => array(
                'name' => 'Quora',
                'title' => __('', 'wpsr') . 'Quora',
                'icon' => array('fa4'=> 'fa fa-quora', 'fa5' => 'fab fa-quora'),
                'link' => 'https://www.quora.com',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#b92b27' ),
            ),
            
            'telegram' => array(
                'name' => 'Telegram',
                'title' => __('', 'wpsr') . 'Telegram',
                'icon' => array('fa4'=> 'fa fa-telegram', 'fa5' => 'fab fa-telegram-plane'),
                'link' => 'https://telegram.me/share/url?url={url}&text={title}',
                'options' => array(),
                'features' => array( 'mobile_only', 'for_share', 'for_profile' ),
                'colors' => array( '#179cde' ),
            ),
            
            'tumblr' => array(
                'name' => 'Tumblr',
                'title' => __('Share this on ', 'wpsr') . 'Tumblr',
                'icon' => array('fa4'=> 'fa fa-tumblr', 'fa5' => 'fab fa-tumblr'),
                'link' => 'https://www.tumblr.com/share?v=3&u={url}&t={title}&s={excerpt}',
                'options' => array(),
                'features' => array( 'for_share', 'for_tsb', 'for_profile' ),
                'colors' => array( '#314358' ),
            ),
            
            'twitch' => array(
                'name' => 'Twitch',
                'title' => __('', 'wpsr') . 'Twitch',
                'icon' => array('fa4'=> 'fa fa-twitch', 'fa5' => 'fab fa-twitch'),
                'link' => 'https://www.twitch.tv/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#4b367c' ),
            ),
            
            'twitter' => array(
                'name' => 'Twitter',
                'title' => __('Tweet this !', 'wpsr') . '',
                'icon' => array('fa4'=> 'fa fa-twitter', 'fa5' => 'fab fa-twitter'),
                'link' => 'https://twitter.com/intent/tweet?text={title}%20-%20{url}%20{twitter-username}',
                'link_tsb' => 'https://twitter.com/intent/tweet?text={excerpt}%20-%20{url}%20{twitter-username}',
                'options' => array(),
                'features' => array( 'for_share', 'for_profile', 'for_tsb' ),
                'colors' => array( '#1da1f2' ),
            ),
            
            'vimeo' => array(
                'name' => 'Vimeo',
                'title' => __('', 'wpsr') . 'Vimeo',
                'icon' => array('fa4'=> 'fa fa-vimeo', 'fa5' => 'fab fa-vimeo-v'),
                'link' => 'https://vimeo.com',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#00ADEF' ),
            ),
            
            'vkontakte' => array(
                'name' => 'VKontakte',
                'title' => __('Share this on ', 'wpsr') . 'VKontakte',
                'icon' => array('fa4'=> 'fa fa-vk', 'fa5' => 'fab fa-vk'),
                'link' => 'https://vk.com/share.php?url={url}&title={title}&description={excerpt}',
                'options' => array(),
                'features' => array( 'for_share', 'for_tsb', 'for_profile' ),
                'colors' => array( '#4C75A3' ),
            ),
            
            'wechat' => array(
                'name' => 'wechat',
                'title' => __('', 'wpsr') . 'WeChat',
                'icon' => array('fa4'=> 'fa fa-wechat', 'fa5' => 'fab fa-weixin'),
                'link' => 'weixin://dl/chat?text={url}',
                'options' => array(),
                'features' => array( 'mobile_only', 'for_share', 'for_profile' ),
                'colors' => array( '#7BB32E' ),
            ),
            
            'weibo' => array(
                'name' => 'Weibo',
                'title' => __('', 'wpsr') . 'Weibo',
                'icon' => array('fa4'=> 'fa fa-weibo', 'fa5' => 'fab fa-weibo'),
                'link' => 'https://service.weibo.com/share/share.php?url={url}&title={title}',
                'link_tsb' => 'https://service.weibo.com/share/share.php?url={url}&title={excerpt}',
                'options' => array(),
                'features' => array( 'for_share', 'for_tsb', 'for_profile' ),
                'colors' => array( '#E6162D' ),
            ),
            
            'whatsapp' => array(
                'name' => 'WhatsApp',
                'title' => __('', 'wpsr') . 'WhatsApp',
                'icon' => array('fa4'=> 'fa fa-whatsapp', 'fa5' => 'fab fa-whatsapp'),
                'link' => 'https://api.whatsapp.com/send?text={title}%20{url}',
                'link_tsb' => 'https://api.whatsapp.com/send?text={excerpt}%20{url}',
                'link_mobile' => 'whatsapp://send?text={title}%20-%20{url}',
                'options' => array(),
                'features' => array( 'for_share', 'for_tsb', 'for_profile' ),
                'colors' => array( '#60b82d' ),
            ),
            
            'xing' => array(
                'name' => 'Xing',
                'title' => __('Share this on ', 'wpsr') . 'Xing',
                'icon' => array('fa4'=> 'fa fa-xing', 'fa5' => 'fab fa-xing'),
                'link' => 'https://www.xing.com/app/user?op=share&url={url}',
                'options' => array(),
                'features' => array( 'for_share' ),
                'colors' => array( '#006567' ),
            ),
            
            'yahoomail' => array(
                'name' => 'Yahoo! Mail',
                'title' => __('Add this to ', 'wpsr') . 'Yahoo! Mail',
                'icon' => array('fa4'=> 'fa fa-yahoo', 'fa5' => 'fab fa-yahoo'),
                'link' => 'https://compose.mail.yahoo.com/?body={excerpt}%20-%20{url}&subject={title}',
                'options' => array(),
                'features' => array( 'for_share', 'for_tsb' ),
                'colors' => array( '#4A00A1' ),
            ),
            
            'youtube' => array(
                'name' => 'Youtube',
                'title' => __('', 'wpsr') . 'Youtube',
                'icon' => array('fa4'=> 'fa fa-youtube-play', 'fa5' => 'fab fa-youtube'),
                'link' => 'https://youtube.com/',
                'options' => array(),
                'features' => array( 'for_profile' ),
                'colors' => array( '#ff0000' ),
            ),
            
        ));
        
        $font_icon = self::get_font_icon()['id'];
        
        foreach( $all_icons as $id => $prop ){
            $icon = $prop['icon'][$font_icon];
            $all_icons[$id]['icon'] = $icon;
        }
        
        return $all_icons;
        
    }
    
    public static function lang_codes( $for = '' ){
        
        if( $for == 'facebook' ){
            return apply_filters( 'wpsr_mod_facebook_lang', array(
                'af_ZA' => 'Afrikaans', 'ak_GH' => 'Akan', 'am_ET' => 'Amharic', 'ar_AR' => 'Arabic', 'as_IN' => 'Assamese', 'ay_BO' => 'Aymara', 'az_AZ' => 'Azerbaijani', 'be_BY' => 'Belarusian', 'bg_BG' => 'Bulgarian', 'bn_IN' => 'Bengali', 'bp_IN' => 'Bhojpuri', 'br_FR' => 'Breton', 'bs_BA' => 'Bosnian', 'ca_ES' => 'Catalan', 'cb_IQ' => 'Sorani Kurdish', 'ck_US' => 'Cherokee', 'co_FR' => 'Corsican', 'cs_CZ' => 'Czech', 'cx_PH' => 'Cebuano', 'cy_GB' => 'Welsh', 'da_DK' => 'Danish', 'de_DE' => 'German', 'el_GR' => 'Greek', 'en_GB' => 'English (UK)', 'en_PI' => 'English (Pirate)', 'en_UD' => 'English (Upside Down)', 'en_US' => 'English (US)', 'eo_EO' => 'Esperanto', 'es_ES' => 'Spanish (Spain)', 'es_LA' => 'Spanish', 'es_MX' => 'Spanish (Mexico)', 'et_EE' => 'Estonian', 'eu_ES' => 'Basque', 'fa_IR' => 'Persian', 'fb_LT' => 'Leet Speak', 'ff_NG' => 'Fula', 'fi_FI' => 'Finnish', 'fo_FO' => 'Faroese', 'fr_CA' => 'French (Canada)', 'fr_FR' => 'French (France)', 'fy_NL' => 'Frisian', 'ga_IE' => 'Irish', 'gl_ES' => 'Galician', 'gn_PY' => 'Guarani', 'gu_IN' => 'Gujarati', 'gx_GR' => 'Classical Greek', 'ha_NG' => 'Hausa', 'he_IL' => 'Hebrew', 'hi_IN' => 'Hindi', 'hr_HR' => 'Croatian', 'ht_HT' => 'Haitian Creole', 'hu_HU' => 'Hungarian', 'hy_AM' => 'Armenian', 'id_ID' => 'Indonesian', 'ig_NG' => 'Igbo', 'is_IS' => 'Icelandic', 'it_IT' => 'Italian', 'ja_JP' => 'Japanese', 'ja_KS' => 'Japanese (Kansai)', 'jv_ID' => 'Javanese', 'ka_GE' => 'Georgian', 'kk_KZ' => 'Kazakh', 'km_KH' => 'Khmer', 'kn_IN' => 'Kannada', 'ko_KR' => 'Korean', 'ks_IN' => 'Kashmiri', 'ku_TR' => 'Kurdish (Kurmanji)', 'ky_KG' => 'Kyrgyz', 'la_VA' => 'Latin', 'lg_UG' => 'Ganda', 'li_NL' => 'Limburgish', 'ln_CD' => 'Lingala', 'lo_LA' => 'Lao', 'lt_LT' => 'Lithuanian', 'lv_LV' => 'Latvian', 'mg_MG' => 'Malagasy', 'mi_NZ' => 'Māori', 'mk_MK' => 'Macedonian', 'ml_IN' => 'Malayalam', 'mn_MN' => 'Mongolian', 'mr_IN' => 'Marathi', 'ms_MY' => 'Malay', 'mt_MT' => 'Maltese', 'my_MM' => 'Burmese', 'nb_NO' => 'Norwegian (bokmal)', 'nd_ZW' => 'Northern Ndebele', 'ne_NP' => 'Nepali', 'nl_BE' => 'Dutch (België)', 'nl_NL' => 'Dutch', 'nn_NO' => 'Norwegian (nynorsk)', 'nr_ZA' => 'Southern Ndebele', 'ns_ZA' => 'Northern Sotho', 'ny_MW' => 'Chewa', 'or_IN' => 'Oriya', 'pa_IN' => 'Punjabi', 'pl_PL' => 'Polish', 'ps_AF' => 'Pashto', 'pt_BR' => 'Portuguese (Brazil)', 'pt_PT' => 'Portuguese (Portugal)', 'qc_GT' => 'Quiché', 'qu_PE' => 'Quechua', 'qz_MM' => 'Burmese (Zawgyi)', 'rm_CH' => 'Romansh', 'ro_RO' => 'Romanian', 'ru_RU' => 'Russian', 'rw_RW' => 'Kinyarwanda', 'sa_IN' => 'Sanskrit', 'sc_IT' => 'Sardinian', 'se_NO' => 'Northern Sámi', 'si_LK' => 'Sinhala', 'sk_SK' => 'Slovak', 'sl_SI' => 'Slovenian', 'sn_ZW' => 'Shona', 'so_SO' => 'Somali', 'sq_AL' => 'Albanian', 'sr_RS' => 'Serbian', 'ss_SZ' => 'Swazi', 'st_ZA' => 'Southern Sotho', 'sv_SE' => 'Swedish', 'sw_KE' => 'Swahili', 'sy_SY' => 'Syriac', 'sz_PL' => 'Silesian', 'ta_IN' => 'Tamil', 'te_IN' => 'Telugu', 'tg_TJ' => 'Tajik', 'th_TH' => 'Thai', 'tk_TM' => 'Turkmen', 'tl_PH' => 'Filipino', 'tl_ST' => 'Klingon', 'tn_BW' => 'Tswana', 'tr_TR' => 'Turkish', 'ts_ZA' => 'Tsonga', 'tt_RU' => 'Tatar', 'tz_MA' => 'Tamazight', 'uk_UA' => 'Ukrainian', 'ur_PK' => 'Urdu', 'uz_UZ' => 'Uzbek', 've_ZA' => 'Venda', 'vi_VN' => 'Vietnamese', 'wo_SN' => 'Wolof', 'xh_ZA' => 'Xhosa', 'yi_DE' => 'Yiddish', 'yo_NG' => 'Yoruba', 'zh_CN' => 'Simplified Chinese (China)', 'zh_HK' => 'Traditional Chinese (Hong Kong)', 'zh_TW' => 'Traditional Chinese (Taiwan)', 'zu_ZA' => 'Zulu', 'zz_TR' => 'Zazaki'
            ));
        }
        
        if( $for == 'linkedin' ){
            return apply_filters( 'wpsr_mod_linkedin_lang', array(
                'en_US' => 'English', 'ar_AE' => 'Arabic', 'zh_CN' => 'Chinese - Simplified', 'zh_TW' => 'Chinese - Traditional ', 'cs_CZ' => 'Czech', 'da_DK' => 'Danish', 'nl_NL' => 'Dutch', 'fr_FR' => 'French', 'de_DE' => 'German', 'in_ID' => 'Indonesian', 'it_IT' => 'Italian', 'ja_JP' => 'Japanese', 'ko_KR' => 'Korean', 'ms_MY' => 'Malay', 'no_NO' => 'Norwegian', 'pl_PL' => 'Polish', 'pt_BR' => 'Portuguese', 'ro_RO' => 'Romanian', 'ru_RU' => 'Russian', 'es_ES' => 'Spanish', 'sv_SE' => 'Swedish', 'tl_PH' => 'Tagalog', 'th_TH' => 'Thai', 'tr_TR' => 'Turkish'
            ));
        }
        
    }
    
    public static function public_icons( $icon ){
        
        $icons = apply_filters( 'wpsr_mod_public_icons', array(
        
            'fb_open' => '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 16 16" class="i-open"><path d="M15,6h-5V1c0-0.55-0.45-1-1-1H7C6.45,0,6,0.45,6,1v5H1C0.45,6,0,6.45,0,7v2c0,0.55,0.45,1,1,1h5v5c0,0.55,0.45,1,1,1h2 c0.55,0,1-0.45,1-1v-5h5c0.55,0,1-0.45,1-1V7C16,6.45,15.55,6,15,6z"/></svg>',
            
            'fb_close' => '<svg class="i-close" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 512 512"><path d="M417.4,224H94.6C77.7,224,64,238.3,64,256c0,17.7,13.7,32,30.6,32h322.8c16.9,0,30.6-14.3,30.6-32 C448,238.3,434.3,224,417.4,224z"/></svg>',
            
            'sb_open' => '<svg class="i-open" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 64 64"><path d="M48,39.26c-2.377,0-4.515,1-6.033,2.596L24.23,33.172c0.061-0.408,0.103-0.821,0.103-1.246c0-0.414-0.04-0.818-0.098-1.215 l17.711-8.589c1.519,1.609,3.667,2.619,6.054,2.619c4.602,0,8.333-3.731,8.333-8.333c0-4.603-3.731-8.333-8.333-8.333 s-8.333,3.73-8.333,8.333c0,0.414,0.04,0.817,0.098,1.215l-17.711,8.589c-1.519-1.609-3.666-2.619-6.054-2.619 c-4.603,0-8.333,3.731-8.333,8.333c0,4.603,3.73,8.333,8.333,8.333c2.377,0,4.515-1,6.033-2.596l17.737,8.684 c-0.061,0.407-0.103,0.821-0.103,1.246c0,4.603,3.731,8.333,8.333,8.333s8.333-3.73,8.333-8.333C56.333,42.99,52.602,39.26,48,39.26 z"/></svg>',
            
            'sb_close' => '<svg class="i-close" xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 512 512"><path d="M417.4,224H94.6C77.7,224,64,238.3,64,256c0,17.7,13.7,32,30.6,32h322.8c16.9,0,30.6-14.3,30.6-32 C448,238.3,434.3,224,417.4,224z"/></svg>'
            
        ));
        
        if( array_key_exists( $icon, $icons ) ){
            return $icons[ $icon ];
        }else{
            return '';
        }
        
    }
    
}

WPSR_Lists::init();

?>