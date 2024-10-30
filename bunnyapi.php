<?php
/* 
 Plugin Name: Bunny.net+BunnyAPI
 Plugin URI: http://wordpress.org/extend/plugins/bunnyapi/
 Description: Upload your files to Bunny.net instead of WordPress Media Library for faster content delivery.
 Version: 2.0.7
 Author: NoteToServices
 Author URI: https://notetoservices.com/
 License: GPL2
 Text Domain: BunnyAPI
 Notes:
  $path
	[path] - base directory and sub directory or full path to upload directory.
	[url] - base url and sub directory or absolute URL to upload directory.
	[subdir] - sub directory if uploads use year/month folders option is on.
	[basedir] - path without subdir.
	[baseurl] - URL path without subdir.
	[error] - set to false.
	[path] => path/to/wordpress/wp-content/uploads/2019/05
	[url] => http://example.com/wp</content/uploads/2019/05
	[subdir] => /2019/05
	[basedir] => /path/to/wordpress/wp-content/uploads
	[baseurl] => http://example.com/wp-content/uploads
	[error] =>

*/

 //no direct access
if ( ! defined( 'ABSPATH' ) ) { die(); }


//WP default upload paths

$uploads = wp_upload_dir();
$upload_base = $uploads['baseurl'];
$upload_path = $uploads['path'];
$upload_url = $uploads['url'];

define('BunnyAPI_UPLOAD_BASE', $upload_base);
define('BunnyAPI_UPLOAD_PATH', $upload_path);
define('BunnyAPI_UPLOAD_URL', $upload_url);

// Set the paths
define('BunnyAPI_SITE_URL', get_home_url());
define('BunnyAPI_PLUGIN_FILE', __FILE__);
define('BunnyAPI_PLUGIN_DIR', dirname(__FILE__));
define('BunnyAPI_PLUGIN_URLDIR', plugin_dir_url(__DIR__));
define('BunnyAPI_PLUGIN_BASE', plugin_basename(__FILE__));
define('BunnyAPI_PULLZONEDOMAIN', "b-cdn.net");
define('BunnyAPI_PLUGIN_NAME', plugin_basename(BunnyAPI_PLUGIN_DIR));
define('BunnyAPI_DEFAULT_FOLDER', "uploads");

//load the css
add_action('wp_enqueue_scripts', 'bunnyapi_callback_css');
add_action( 'admin_init','bunnyapi_callback_css');
function bunnyapi_callback_css() {
    if( is_admin() ) { // only load this css in backend
	    wp_register_style( 'popup', BunnyAPI_PLUGIN_URLDIR.BunnyAPI_PLUGIN_NAME.'/assets/css/popup.css' );
    	    wp_enqueue_style( 'popup' );
    }
}

// Load the plugin
spl_autoload_register('BunnyAPILoad');
function BunnyAPILoad($class) 
{
	require_once(BunnyAPI_PLUGIN_DIR.'/inc/bunnyapiFunctions.php');
	require_once(BunnyAPI_PLUGIN_DIR.'/inc/bunnyapiSettings.php');
	require_once(BunnyAPI_PLUGIN_DIR.'/inc/bunnyapiMedia.php');
}

// Register the settings page and menu
add_action("admin_menu", array("BunnyAPISettings", "initialize"), 30);
add_action("wp_head", "bunnyapi_dnsprefetch", 0);


/* bunnyapi_curl_extend_timeout */
/* uncomment this code if you experience timeout or nothing is displaying
/* should you need more time for your code to load, increase the 30 to more time 
/*
add_action('http_api_curl', 'bunnyapi_curl_extend_timeout', 9999, 1);
function bunnyapi_curl_extend_timeout( $handle ){
	curl_setopt( $handle, CURLOPT_CONNECTTIMEOUT, 30 ); // 30 seconds. 
        curl_setopt( $handle, CURLOPT_TIMEOUT, 30 ); // 30 seconds. 
}
*/

/**
	 * bunnyapi_img_shortcode
	 *
	 * generate and activate image shortcode
	 *
	 * @since 1.0.0
	 * @param $atts
         * @usage src=img, link=yes/no, href=link, alt=description, title=title, align=left|center|right href=url height=# width=# target=_blank|_self|_top|_parent display=block|inline data-src=none|href
	 */

add_shortcode('img', 'bunnyapi_img_shortcode');

function bunnyapi_img_shortcode($atts) 
{
    // Attributes
    $atts = shortcode_atts(
        [
        'src' => '',
        'link' => '',
        'href' => '',
        'alt' => '',
        'align' => 'center',
        'height' => '',
        'target' => '_blank',
        'width' => '',
        'title' => '',
        'display' => 'block', 
        'data-src' => ''
        ], $atts, 'img'
    );

    //ensure all attribs are escaped
    $ATTRdisplay = trim(esc_attr($atts['display']));
    $ATTRalign = trim(esc_attr($atts['align']));
    $ATTRalt = trim(esc_attr($atts['alt']));
    $ATTRhref = trim(esc_url_raw($atts['href']));
    $ATTRtitle = trim(esc_attr($atts['title']));
    $ATTRtarget = trim(esc_attr($atts['target']));
    $ATTRheight = trim(esc_attr($atts['height']));
    $ATTRwidth = trim(esc_attr($atts['width']));
    $ATTRlink = trim(esc_attr($atts['link'])); 
    $ATTRsrc = trim(esc_url_raw($atts['src']));
    $ATTRdatasrc = trim(esc_url_raw($atts['data-src']));

    $return = '';
     
    //set div attributes
    $divattr = '';
    $divattr .= empty($ATTRalign) ? '' : ' align="'.$ATTRalign.'"';
    $divattr .= empty($ATTRdisplay) ? '' : ' style="display:'.$ATTRdisplay.'"';
    
    //set a attributes
    $aattr = '';
    $ATTRlink = !empty($ATTRhref) && !preg_match('/no/i', $ATTRlink) ? 'yes' : 'no'; 
    if(preg_match('/yes/i', $ATTRlink)) { 
        $aattr = empty($ATTRhref) ? ' href="' . $ATTRsrc . '" ' : ' href="' . $ATTRhref . '" '; 
    } 

    $aattr .= empty($ATTRtarget) ? '' : $aattr = ' target="'.$ATTRtarget.'"';
    $aattr .= empty($ATTRtitle) ? '' : $aattr = ' title="'.$ATTRtitle.'"';

    //set img attributes
    $imgattr = empty($ATTRsrc) ? '' : $imgattr = ' src="'.$ATTRsrc.'"';
    $imgattr .= preg_match('/none/i', $ATTRdatasrc) ? '' : ' data-src="'.$ATTRsrc.'"';
    $imgattr .= empty($ATTRalign) ? '' : ' align="' . $ATTRalign . '" ';
    $imgattr .= empty($ATTRalt) ? '' : ' alt="' . $ATTRalt . '" ';
    $imgattr .= empty($ATTRtitle) ? '' : ' title="' . $ATTRtitle . '" ';


    //if the display attribute is NOT empty then it gets the style attribute
    if(!empty($ATTRdisplay)) { 
        $imgattr .= ' style="display:'.$ATTRdisplay.';';
        //both width & height attribs are not empty
        if(!empty($ATTRwidth) && !empty($ATTRheight)) { 
            $imgattr .= 'width:' . $ATTRwidth . 'px;height:' . $ATTRheight . 'px;"';
        } else {
           //width only
            if(!empty($ATTRwidth) && empty($ATTRheight)) { 
                $imgattr .= 'width:' . $ATTRwidth . 'px;"';
            }
            //height only
            if(empty($ATTRwidth) && !empty($ATTRheight)) { 
                $imgattr .= 'height:' . $ATTRheight . 'px;"';
            }
        }
    } else { 
       //there is no display, assign width the style attribute
        //both width & height attribs are not empty
        if(!empty($ATTRwidth) && !empty($ATTRheight)) { 
            $imgattr .= 'width:' . $ATTRwidth . 'px;height:' . $ATTRheight . 'px;"';
        } else {
           //width only
            if(!empty($ATTRwidth) && empty($ATTRheight)) { 
                $imgattr .= 'width:' . $ATTRwidth . 'px;"';
            }
            //height only
            if(empty($ATTRwidth) && !empty($ATTRheight)) { 
                $imgattr .= 'height:' . $ATTRheight . 'px;"';
            }
       }
    }

    //if ATTR has a link, add div and the a link else just add the image
    if (strtolower($ATTRlink) == 'yes')
    {
       $return = '<div'.$divattr.'><a'.$aattr.'><img '.$imgattr.'/></a></div>';
    } else {
       $return = '<img'.$imgattr.'/>';
    }
    // Return HTML code
    return $return;
}

/**
	 * bunnyapi_dnsprefetch
	 *
	 * preload dns-prefetch field
	 *
	 * @since 1.0.0
	 * @param $atts
         * @usage 
	 */


function bunnyapi_dnsprefetch() 
{
	$options = BunnyAPI::getOptions();
        echo '<link rel="dns-prefetch" href="//';
	echo strlen($options["hostname"] > 0) ? $options["hostname"] : BunnyAPI_PULLZONEDOMAIN;
        echo '">';
}
