<?php
	/**
	 * bunnyapi_CustomMediaUI
	 *
	 * add a custom media library for bunnycdn
	 * @since 1.0.0
	 * @param
	 */
add_action( 'media_buttons', function($editor_id){


//set prefix with http or https
$prefix = bunnyapi_Functions::bunnyapi_check_https();

//fill in the folder var
$folder = !empty(BunnyAPI::getOption('folder')) ? sanitize_file_name (BunnyAPI::getOption('folder')) : BunnyAPI_DEFAULT_FOLDER;

//save bandwidth? 
$savebandwidth = BunnyAPI::getOption('savebandwidth');

//grab media list from bunnyapi bunnycdn media
$getmedia = bunnyapi_Functions::bunnyapi_api_call('bunnylist', $folder);

//set a preset bunnyurl with the prefix and host
$hostname = $prefix.BunnyAPI::getOption('hostname'); 

//get selected hostname
$bunnyurl = $hostname.'/'.$folder; 

//open a modal window when the button is clicked
echo '<a href="#openModal" class="button">Bunny.net Media</a><div id="openModal" class="modalDialog"><div><a href="#close" title="Close" class="close">X</a><h2>Bunny.net Media</h2><p>';
//make sure the bunnyapi key is valid
if(empty(bunnyapi_Functions::bunnyapi_full_validation_apikey())) { $getmedia = NULL; }
    //if there is bunnycdn media, loop through the data
  if((!is_array($getmedia) && preg_match('/empty/i', $getmedia)) || empty($getmedia)) { 
	if(empty(bunnyapi_Functions::bunnyapi_full_validation_apikey())) { 
		echo 'You must supply a valid BunnyAPI key to use this feature.'; 
       	} else { 
        	echo 'No Bunny.net Media.';
       	}
   } else {
        echo '<div style="overflow-y:scroll; overflow-x:hidden; height:400px;"><table style="tr:nth-of-type(odd){background-color:#ccc;}" border="1" width="100%"><thead><th width="20%">Image</th><th width="35%">URL</th><th width="35%">IMG URL</th></thead><tbody>';
    	foreach ( $getmedia["data"] as $getfile ) {  
       	 	$filename = basename($getfile["name"]);
        	$finalurl = $bunnyurl.'/'.$filename;
        	echo '<tr>';
        	echo '<td align="center">';
        	if($savebandwidth == 'no') { 
           		echo '<a href="'.$finalurl.'" target="_blank"><img style="width:100%;" src="'.$finalurl.'"></a>';
        	} else {
            		echo '<a href="'.$finalurl.'" target="_blank"><strong>'.basename($filename).'</strong></a>';
        	}
        	echo '</td>';
        	echo '<td align="center">';
        	echo '<input style="width:100%;" onclick="this.select();" type="text" value="'.$finalurl.'" readonly>';
        	echo '</td>';
        	echo '<td align="center">';
        	echo '<input style="width:100%;" onclick="this.select();" type="text" value="[img src='.$finalurl.']" readonly>';
        	echo '</td>';
        	echo '</tr>';
    	}
    }
    echo '</tbody></table></div></div></div>';
} );

class bunnyapi_CustomMediaUI {


	/**
	 * bunnyapi_media_urls
	 *
	 * populate the Bunny.net Media screen
	 * @since 1.0.0
	 * @param
	 */

public static function bunnyapi_media_urls( ) {

    //make sure the bunnyapi key is valid
    if(empty(bunnyapi_Functions::bunnyapi_full_validation_apikey())) { echo 'You must supply a valid BunnyAPI key to use this feature.'; die(); }
    
    //clear cache
    bunnyapi_Functions::bunnyapi_clear_cache();

   // if bunnycdn parent is installed, navigate to bunnycdn url
    if(is_plugin_active('bunnycdn/bunnycdn.php')) { 
        sprintf( __('<a href="'.BunnyAPI_SITE_URL.'/wp-admin/admin.php?page='.BunnyAPI_PLUGIN_NAME.'/inc/bunnyapiSettings.php">Enable BunnyAPI to connect Bunny.net Media</a>', 'bunnyapi'));  
    } else { 
        sprintf( __('<a href="'.BunnyAPI_SITE_URL.'/wp-admin/tools.php?page=bunnycdn_extended">Enable BunnyAPI to connect Bunny.net Media</a>', 'bunnyapi'));  
    }

  //check for multiurl
 if(isset($_REQUEST["multiurl"])) { $multiurl = explode(PHP_EOL, $_REQUEST["multiurl"]); }
 
    if(!empty($multiurl)) { 
          // upload any manually input urls
          foreach($multiurl as $mu) {              
               //url was uploaded so it should be safe, however, doesnt hurt to escape!      
               bunnyapi_Functions::bunnyapi_api_call('upload', esc_url_raw($mu));
          }
    }
  
    // upload by url button clicked
    if(isset($_GET["url"])) { 
         echo '<h3>Multiple Upload by URL</h3><form action="" method="post"><textarea style="width:100%;height:100px;" name="multiurl" required/></textarea><br><small>Separate each URL by new line</small><p><input type="submit" class="button"> <a href="'.str_replace('&url=uploadbyurl', '', bunnyapi_Functions::bunnyapi_currenturl()).'" class="button">Cancel</a></p></form>'; 
    }

    //check if files were uploaded to Bunny.net via upload button
    if(!empty($_FILES)) { bunnyapi_Functions::bunnyapi_upload_files($_FILES); }

    //fill the prefix with http or https
    $prefix = bunnyapi_Functions::bunnyapi_check_https();

    //fill the folder variable
    $folder = !empty(BunnyAPI::getOption('folder')) ? sanitize_file_name(BunnyAPI::getOption('folder')): BunnyAPI_DEFAULT_FOLDER;

    //save bandwidth? 
    $savebandwidth = BunnyAPI::getOption('savebandwidth');

    //default bunny hostname url with the site prefix
   $bunnyurl = $prefix.BunnyAPI::getOption('hostname').'/'.$folder; 

    //grab the list of media
    $getmedia = bunnyapi_Functions::bunnyapi_api_call('bunnylist', $folder);

    $outcount = NULL;
    //count media
    if(!empty($getmedia)) {
	if(is_array($getmedia)) { 
	        if(is_countable($getmedia["data"])) { $getcount = count($getmedia["data"]); } else { $getcount = 0; }
       	 		$getcount == 1 ? $outcount = $getcount.' file' : $outcount = $getcount.' files';
	     	} else { 
        		$getcount = 'N/A'; 
     		}
	} 

    //check to see if we should filter media for a search query
    if(isset($_REQUEST["search"])) { $getsearch = sanitize_text_field($_REQUEST["search"]); }

    //uid is generated as a means to let us know if check all files was selected
    //check all triggers bunnyapi_delete_all to delete the entire folder on Bunny.net rather than deleting each file individually which is faster than bunnyapi_delete_files($files)
    if(isset($_REQUEST["uid"])) { $getuid = $_REQUEST["uid"]; }

     //on the page load we are checking to see if there was a check all and then calling the proper function
    if(!empty($getuid)) { 
              echo __( '<div style="color:green;">Bunny.net Media deleted.</a></div>' );
              foreach($_REQUEST as $data) {
                 //if multiple files are selected -- but not all
                 //then delete those files individually
                 //sanitize file name just in case user uploaded files on Bunny.net 
                 $filetype = wp_check_filetype(sanitize_file_name($data));
                 $fileext = $filetype["ext"];
                 if(!empty($fileext)) { echo __( basename($data)).'<br>'; }
                 bunnyapi_Functions::bunnyapi_delete_files($_REQUEST);
              }
             bunnyapi_Functions::bunnyapi_clear_cache();

        //although we have called the clear cache function, it does not always trigger immediately, but a new page load seems to clear the cache and reload the media
        echo __( '<div style="color:blue;"><a href="'.BunnyAPI_SITE_URL.'/wp-admin/admin.php?page=bunnycdn-media-library">Continue to Bunny.net Media</a></div>' ); exit(); 
    }
 
     //keeping count of images
    $imgcount = 0;
    $searchcount = 0;
    ?><table border="1" width="100%"><thead><span style="float:right;"><form action="<?php echo BunnyAPI_SITE_URL.'/wp-admin/admin.php?page=bunnycdn-media-library&uid='.rand(); ?>" method="post"><?php if(empty($getsearch)) { ?><input onclick="toggle(this);" type="checkbox" name="checkall"> Check All <?php } ?><input onclick="return confirm('Delete selected files?')" type="submit" value="Remove"></span><th width="25%">Image</th><th width="35%">URL</th><th width="35%">IMG URL</th><th width="5%">Delete</th></thead><tbody> 
<?php
    
     //loop through each file
//print_r($getmedia);die();
    if(is_array($getmedia) && !empty($getmedia)) {
    foreach ( $getmedia["data"] as $getfile ) {  
        $filename = sanitize_file_name(basename($getfile["name"])); 
        if(!empty($getsearch)) {
            //if our search query isn't matched ~ skip
            if(!preg_match('/'.$getsearch.'/', $filename)) { continue; }
            $searchcount++;
         }
        
        //sanitize final url
        $finalurl = esc_url_raw($bunnyurl.'/'.$filename);

        //image counter
        $imgcount++;
        echo '<tr>';
        echo '<td align="center">';
        if($savebandwidth == 'no') {
            //download the image and display it
            echo '<a href="'.$finalurl.'" target="_blank"><img style="width:100%;" src="'.$finalurl.'"></a>';
        } else {
            //only display the text
            echo '<a href="'.$finalurl.'" target="_blank"><strong>'.basename($finalurl).'</strong></a>';
        }
        echo '</td>';
        echo '<td align="center">';
        //select all for the raw url
        echo '<input style="width:100%;" onclick="this.select();" type="text" value="'.$finalurl.'" readonly>';
        echo '</td>';
        echo '<td align="center">';
        //select all for the img shortcode
        echo '<input style="width:100%;" onclick="this.select();" type="text" value="[img src='.$finalurl.']" readonly>';
        echo '</td>';
        echo '<td align="center">';
        // add a option to delete this image
        echo '<input type="checkbox" name="'.$imgcount.'" value="'.$finalurl.'">';
        echo '</td>';
        echo '</tr>';
    } 
   }
     //number of files found in search query
    if(!empty($getsearch)) { $outcount = $searchcount.' files'; }
    echo '</tbody></form><form method="post" enctype="multipart/form-data">Select: <input name="filearray[]" type="file" multiple required/><input type="submit" class="button" value="Upload" /></form>  <a href="'.bunnyapi_Functions::bunnyapi_currenturl().'&url=uploadbyurl" class="button">Upload by URL</a> '.$outcount.' <form action="" method="post"><input style="margin:5px;" type="text" name="search" placeholder="search"></form></table>'; 
?><script>function toggle(e){for(var c=document.querySelectorAll('input[type="checkbox"]'),t=0;t<c.length;t++)c[t]!=e&&(c[t].checked=e.checked)}</script><?php 
}


	/**
	 * bunnyapi_getLabel
	 *
	 * change the custom media library button label
	 * @since 1.0.0
	 * @param
	 */

  public static function bunnyapi_getLabel() {
    return 'Bunny.net Media';
  }

	/**
	 * bunnyapi_geturl
	 *
	 * change here the url of your custom upload button
	 * @since 1.0.0
	 * @param
	 */

  public static function bunnyapi_geturl() {
    return add_query_arg( array('page'=>'bunnycdn-media-library'), admin_url('upload.php') );
  }

	/**
	 * bunnyapi_render
	 *
	 * renders custom upload system
	 * @since 1.0.0
	 * @param
	 */

  public function bunnyapi_render() {
    if ( ! current_user_can( 'upload_files' ) ) {
      echo '<h2>Sorry, your permissions does not allow for file uploads.</h2>';
      return;
    }
  ?>
    <div class="wrap">
    <h2>Bunny.net Media</h2>
      <?php //call to display custom bunnycdn media
         self::bunnyapi_media_urls(); ?>
    </div>
  <?php
  }

	/**
	 * __construct
	 *
	 * constructs the bunnycdn media library
	 * @since 1.0.0
	 * @param
	 */

  function __construct() {
    //generate some bunnycdn media buttons
    add_action('load-upload.php', array($this, 'bunnyapi_indexbutton'));
    add_action('admin_menu', array($this, 'bunnyapi_submenu') );
    add_action( 'wp_before_admin_bar_render', array( $this, "bunnyapi_adminbar" ) );
    add_action('post-plupload-upload-ui', array($this, 'bunnyapi_mediaButton'));
  }

	/**
	 * bunnyapi_submenu
	 *
	 * adds a submenu for the Bunny.net Media
	 * @since 1.0.0
	 * @param
	 */

  function bunnyapi_submenu() {
    add_media_page( self::bunnyapi_getLabel(), self::bunnyapi_getLabel(), 'upload_files', 'bunnycdn-media-library', array($this, 'bunnyapi_render') ); 
  }


	/**
	 * bunnyapi_adminbar
	 *
	 * adds a adminBar for the Bunny.net Media
	 * @since 1.0.0
	 * @param
	 */


  function bunnyapi_adminbar() {
    if ( ! current_user_can( 'upload_files' ) || ! is_admin_bar_showing() ) return;
    global $wp_admin_bar;
    $wp_admin_bar->add_node( array(
      'parent' => 'new-content',
      'id' => 'bunnycdn-media-library',
      'title' => self::bunnyapi_getLabel(),
      'href' => self::bunnyapi_geturl()
    ) );
  }

	/**
	 * bunnyapi_mediabutton
	 *
	 * adds a mediaButton for the Bunny.net Media
	 * @since 1.0.0
	 * @param
	 */

  function bunnyapi_mediabutton() {
     //gutenberg and the classic editor do not have the same layout 
     //so adding in this check prevents any errors
    if ( !bunnyapi_Functions::bunnyapi_is_gutenberg_active()) {
    if ( current_user_can( 'upload_files' ) ) {
          echo '<div><p align="center">';
          echo '<input id="custom-browse-button" type="button" value="' . self::bunnyapi_getLabel() . '" class="button" />';
          echo '</p></div>';
          $this->bunnyapi_mediabuttonscript();
     }
    }
  }

	/**
	 * bunnyapi_mediabuttonscript
	 *
	 * enables the mediaButton click for Bunny.net Media
	 * @since 1.0.0
	 * @param
	 */

  function bunnyapi_mediabuttonscript() {
    if ( ! current_user_can( 'upload_files' ) ) return;
  ?>
    <script>
    jQuery(document).on('click', '#custom-browse-button', function(e) {
      e.preventDefault();
      window.location = '<?php echo self::bunnyapi_geturl(); ?>';
    });
    </script>
  <?php
  }

	/**
	 * bunnyapi_indexbutton
	 *
	 * sets the mediaButton click for Bunny.net Media as index
	 * @since 1.0.0
	 * @param
	 */


  function bunnyapi_indexbutton() {
    if ( ! current_user_can( 'upload_files' ) ) return;
    add_filter( 'esc_html', array(__CLASS__, 'bunnyapi_h2button'), 999, 2 );
  }


	/**
	 * bunnyapi_h2button
	 *
	 * sets the text for Bunny.net Media
	 * @since 1.0.0
	 * @param
	 */

  static function bunnyapi_h2button( $safe_text, $text ) {
    if ( ! current_user_can( 'upload_files' ) ) return $safe_text;
    if ( $text === __('Media Library') && did_action( 'all_admin_notices' ) ) {
      remove_filter( 'esc_html', array(__CLASS__, 'bunnyapi_h2button'), 999, 2 );
      $format = ' <a href="%s" class="add-new-h2">%s</a>';
      $mybutton = sprintf($format, esc_url(self::bunnyapi_geturl()), esc_html(self::bunnyapi_getLabel()) );
      $safe_text .= $mybutton;
    }
    return $safe_text;
  }


}


$ui = new bunnyapi_CustomMediaUI;


?>
