<?php


class bunnyapi_Functions {


	/**
	 * bunnyapi_api_key
	 *
	 * Function to return bunny apikey from the setting in WordPress 
	 *
	 * @since 1.0.0
	 * @param 
	 */
public static function bunnyapi_api_key() { 
   $apikey = sanitize_text_field(BunnyAPI::getOption( 'api_key' ));
   return(!empty($apikey) ? $apikey : NULL);
}



	/**
	 * bunnyapi_full_validation_apikey 
	 *
	 * Make sure API key is not empty and that it is actually valid through BunnyAPI
	 *
	 * @since 1.0.0
	 * @param
	 */

public static function bunnyapi_full_validation_apikey() { 
       //set the bunnyapi key with wp setting
      $bunnyapi_key = self::bunnyapi_api_key();
       //if empty return nothing
      if(empty($bunnyapi_key)) { return NULL; }

      // check to see what the actual key is
      $checkbunnyapi = self::bunnyapi_api_call('bunnyapi', NULL);
      
      if(empty($checkbunnyapi)) { return NULL; }

      $validation = self::bunnyapi_validate_apikey();

      if(preg_match('/not found/i', $validation)) { $validation = NULL; } 

      return $validation;

}



	/**
	 * bunnyapi_validate_apikey 
	 *
	 * Validates the BunnyAPI key by verifying it with HQ
	 *
	 * @since 1.0.0
	 * @param
	 */

public static function bunnyapi_validate_apikey() { 
      $checkbunnyapi = self::bunnyapi_api_call('bunnyapi', NULL);
      
      if(preg_match('/invalid/i', $checkbunnyapi)) { return NULL; } else { return $checkbunnyapi; }
}



	/**
	 * bunnyapi_clearcache
	 *
	 * Clear Bunny.net cache
	 *
	 * @since 1.0.0
	 * @param
	 */

public static function bunnyapi_clear_cache() { 
    self::bunnyapi_api_call('purge', NULL);
}

	/**
	 * bunnyapi_delete_all
	 *
	 * Delete All Bunny.net Media from Folder
	 *
	 * @since 1.0.0
	 * @deprecated 2.0.6
	 * @param
	 */

public static function bunnyapi_delete_all() { 
    self::bunnyapi_api_call('removeall', NULL);
}



	/**
	 * bunnyapi_upload_files
	 *
	 * Upload All Bunny.net Media 
	 *
	 * @since 1.0.0
	 * @param $folder
	 */

public static function bunnyapi_upload_files($files) { 
        $filearray = array();
        $tmparray = array();
        //grabbing actual filenames
        foreach($files["filearray"]["name"] as $file) { 
            $filearray[] = $file;
        }
        //grabbing temp files
        foreach($files["filearray"]["tmp_name"] as $tmpfile) { 
             $tmparray[] = $tmpfile;
        }
        $filesarray = array_combine(array_unique($tmparray), array_unique($filearray));
        foreach($filesarray as $key => $value) { 
             $absfinal = BunnyAPI_UPLOAD_PATH.'/'.$value;
             $finaldest = BunnyAPI_UPLOAD_URL.'/'.$value;
             if (move_uploaded_file($key, $absfinal )) {         
                  if(file_exists($absfinal)) { self::bunnyapi_api_call('upload', $finaldest); unlink($absfinal); } 
             sleep(1);
             }
        }
}


	/**
	 * bunnyapi_delete_files
	 *
	 * Delete Selected Files From Bunny.net Media
	 *
	 * @since 1.0.0
	 * @param (array) $files
	 */

public static function bunnyapi_delete_files($files) { 
    if(!empty($files)) { 
        foreach($files as $file) { 
            if(!preg_match('/bunnycdn-media-library/i', $file)) {  
               self::bunnyapi_api_call('delete', sanitize_file_name(basename($file)));
            }
        }
    }
}

	/**
	 * bunnyapi_copy_all
	 *
	 * Copy All Bunny.net Media from Folder
	 *
	 * @since 1.0.0
	 * @param $folder
	 */

public static function bunnyapi_copy_all($folder) { 
        $getmedia = self::bunnyapi_api_call('bunnylist', sanitize_file_name($folder));
        $prefix = self::bunnyapi_check_https();
        $bunnyurl = $prefix.self::bunnyhost().'/'.$folder; //default bunny hostname url with the site prefix
        if(!empty($getmedia)) { 
            foreach($getmedia as $getfile) { 
                $filename = basename($getfile["name"]);
                 //escape url just in case
                $finalurl = $bunnyurl.'/'.$filename; 
                self::bunnyapi_api_call('upload', $finalurl);
                //give bunnyapi time for processing
                sleep(3);
            }
       }
}



	/**
	 * bunnyapi_force_push
	 *
	 * Force Push WordPress Media Library to Bunny.net Media
	 *
	 * @since 1.0.0
	 * @update1.1.0
	 * @param 
	 */

public static function bunnyapi_force_push($folder) { 
    $query_media_urls = array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'posts_per_page' => -1,
    );

    $query_urls = new WP_Query( $query_media_urls ); 
    
        $i = 0; // due to limited resources, only upload 5 urls at a time
        //use url pointer to continue so the script starts to the next one 
        if(!empty($query_urls)) { 
            foreach($query_urls->posts as $url) { 
                $fullurl = esc_url_raw($url->guid);
//                $currurl = self::bunnyapi_currenturl().'&count='.$i;
                echo '<p>Uploaded... '.basename($fullurl).'</p>';                
                self::bunnyapi_api_call('upload', $fullurl);
                //give bunnyapi some time to process
                sleep(5);
                $i++;
            }
        }
        
}


	/**
	 * Check if Gutenberg is active.
	 * Must be used not earlier than plugins_loaded action fired.
	 *
	 * @return bool
	 */
	public static function bunnyapi_is_gutenberg_active() {
		$gutenberg    = false;
		$block_editor = false;

		if ( has_filter( 'replace_editor', 'gutenberg_init' ) ) {
			// Gutenberg is installed and activated.
			$gutenberg = true;
		}

		if ( version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' ) ) {
			// Block editor.
			$block_editor = true;
		}

		if ( ! $gutenberg && ! $block_editor ) {
			return false;
		}

		include_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( ! is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
			return true;
		}

		$use_block_editor = ( get_option( 'classic-editor-replace' ) === 'no-replace' );

		return $use_block_editor;
	}






	/**
	 * bunnyapi_setbunnyhost
	 *
	 * Set Bunny.net Hostname
	 *
	 * @since 1.0.0
	 * @param
	 */

public static function bunnyapi_setbunnyhost($hostname) { 
        global $bunnyhost;
        $setbunnyhost = $hostname;
        $bunnyhost = self::bunnyapi_api_call('bunnyhost', NULL); //get the default bunnyhost
        if(!empty($setbunnyhost) || strcmp($bunnyhost, $setbunnyhost !== 0)) { 
              // only change if not empty and different from current bunnyhost
	       self::bunnyapi_api_call('setbunnyhost', $setbunnyhost);
        }
}


	/**
	 * bunnyapi_storagezonelist
	 *
	 * Get Bunny.net Storage Zone List
	 *
	 * @since 1.1.0
	 * @param
	 */

public static function bunnyapi_storagezonelist() { 
        return self::bunnyapi_api_call('storagezonelist', NULL); //get the default bunnyhost
}


	/**
	 * bunnyapi_hostlist
	 *
	 * Get Bunny.net Storage Zone Host List
	 *
	 * @since 1.1.0
	 * @param
	 */

public static function bunnyapi_hostlist() { 
        return self::bunnyapi_api_call('hostlist', NULL); //get the default bunnyhost
}


	/**
	 * bunnyapi_downloadall
	 *
	 * Download Bunny.net Storage Zone
	 *
	 * @since 1.1.0
	 * @param
	 */


public static function bunnyapi_downloadall($folder) { 
    if(!empty($folder)) { 
       // return self::bunnyapi_api_call('downloadall', NULL); //get the default bunnyhost
       $bunnystorage = self::bunnyapi_api_call('bunnylist', sanitize_file_name($folder));
       
       if(!empty($bunnystorage)) { 
           $i = 0;
           $hostname = esc_url(BunnyAPI::getOption('hostname'));
           foreach($bunnystorage["data"] as $file) { 
               $bunnyfile = $hostname.'/'.$folder.'/'.$file["name"];
               echo '<p>Downloaded... '.basename($bunnyfile).'</p>';
               self::bunnyapi_downloadfile($bunnyfile);
               sleep(3);
            }
       }
    }
}



	/**
	 * bunnyapi_downloadfile
	 *
	 * Download Bunny.net Storage Zone
	 *
	 * @since 1.1.0
	 * @param
	 */


public static function bunnyapi_downloadfile($url) { 

    $timeout_seconds = 5;

    // Download file to temp dir
    $temp_file = download_url( $url, $timeout_seconds );
    if ( !is_wp_error( $temp_file ) ) {

        $wp_file_type = wp_check_filetype($temp_file);

        $filemime = $wp_file_type['type'];

	// Array based on $_FILE as seen in PHP file uploads
	$file = array(
		'name'     => basename($url), // ex: wp-header-logo.png
		'type'     => $filemime,
		'tmp_name' => $temp_file,
		'error'    => 0,
		'size'     => filesize($temp_file),
	);

	$overrides = array(
		// Tells WordPress to not look for the POST form
		// fields that would normally be present as
		// we downloaded the file from a remote server, so there
		// will be no form fields
		// Default is true
		'test_form' => false,

		// Setting this to false lets WordPress allow empty files, not recommended
		// Default is true
		'test_size' => true,
	);

	// Move the temporary file into the uploads directory
	$results = media_handle_sideload( $file, $overrides );
  }
}



/**
* bunnyapi_currenturl
*
* Current url of page
*
* @since 1.0.0
* @param
*/
public static function bunnyapi_currenturl() {
 return $_SERVER['REQUEST_URI'];
}


	/**
	 * bunnyapi_api_call
	 *
	 * Make a call to the bunnyapi API // upload, delete, etc.
	 *
	 * @since 1.0.0
	 * @param @action = check,clearcache,bunnyhost,bunnyapi,upload,delete @input = filename
	 */

public static function bunnyapi_api_call($action, $input) {
   $action = sanitize_text_field($action);
   $input = sanitize_text_field($input);
   $setbunnyhost = esc_url(BunnyAPI::getOption('hostname'));
   $getzone = sanitize_file_name(BunnyAPI::getOption('stozone'));
   $folder = sanitize_file_name(BunnyAPI::getOption('folder')); //esc_url(substr($getfolder, 4, strlen($getfolder))));
   if(empty($folder)) { $folder = BunnyAPI_DEFAULT_FOLDER; }
   switch ($action) {
        case "delete":
            $url = 'https://bunnyapi.com/?key='.self::bunnyapi_api_key().'&action='.$action.'&meta=wordpress&zone='.$getzone.'&folder='.$folder.'&filename='.$input; 
        break;
        case "removeall":
            $url = 'https://bunnyapi.com/?key='.self::bunnyapi_api_key().'&action='.$action.'&meta=wordpress&zone='.$getzone.'&folder='.$folder; 
        break;
        case "check":
            $url = 'https://bunnyapi.com/?key='.self::bunnyapi_api_key().'&action='.$action.'&meta=wordpress&url='.esc_url_raw($input); 
        break;
        case "upload":
            $url = 'https://bunnyapi.com/?key='.self::bunnyapi_api_key().'&action='.$action.'&meta=wordpress&zone='.$getzone.'&folder='.$folder.'&filename='.$input;
 	    break;
        case "downloadall":
            $url = 'https://bunnyapi.com/?key='.self::bunnyapi_api_key().'&action='.$action.'&meta=wordpress&zone='.$getzone.'&folder='.$folder.'&meta=wordpress';
     	break;
        case "hostlist":
            $url = 'https://bunnyapi.com/?key='.self::bunnyapi_api_key().'&action='.$action.'&meta=wordpress&zone='.$getzone.'&json=true&source=bunnycdn'; 
        break;
        case "storagezonelist":
            $url = 'https://bunnyapi.com/?key='.self::bunnyapi_api_key().'&action='.$action.'&meta=wordpress&json=true';
        break;
        case "setbunnyhost":
            $url = 'https://bunnyapi.com/?key='.self::bunnyapi_api_key().'&action='.$action.'&meta=wordpress&hostname='.$setbunnyhost; 
        break;
        case "purge":
        case "bunnyapi":
            $url = 'https://bunnyapi.com/?key='.self::bunnyapi_api_key().'&action='.$action.'&meta=wordpress';
        break;
        case "bunnyhost": 
            $url = 'https://bunnyapi.com/?key='.self::bunnyapi_api_key().'&action='.$action.'&meta=wordpress&zone='.$getzone.'&source=bunnycdn&json=true';
        break;
        case "bunnylist":
            $url = 'https://bunnyapi.com/?key='.self::bunnyapi_api_key().'&action='.$action.'&meta=wordpress&zone='.$getzone.'&type=files&folder='.$input.'&json=true';
        break;
   }
   
   if(!empty($url)) { 
       //sending to api for a response
   $getjson = wp_remote_get(esc_url_raw($url));
   if ( is_array( $getjson ) ) {
        $rjson = json_decode($getjson["body"], true);
        if(isset($rjson)) { $message = $rjson["output"]; }
   }

    //make sure there is something to send back
   if(isset($message) && !empty($message)) {return $message; } 
   }
}





/**
	 * bunnyhost
	 *
	 * get Bunny Hostname
	 *
	 * @since 1.0.0
	 * @param
	 */

public static function bunnyhost() { 
        if(!empty(self::bunnyapi_full_validation_apikey())) { 
            $gethost = self::bunnyapi_api_call('bunnyhost', NULL);
            if(empty($gethost)) { 
                return NULL; 
            } else { 
            if(!is_array($gethost)) { 
                    if(!preg_match('/empty/i', $gethost)) {
                        $hostname = $gethost["hostnames"][0]["hostname"];  //get the default bunnyhost
                    } else {
                        $hostname = NULL;
                    }
                }
            }
            if(empty($hostname)) { 
          	    return NULL; 
            } else {
                return $gethost; 
            }
        }
}



	/**
	 * bunnyapi_convert_year_month_url (inactive)
	 *
	 * Filter and replace month and year in any given URL
	 *
	 * @since 1.0.0
	 * @param @input url of media file
	 */
	 
public static function bunnyapi_convert_year_month_url($url) { 
    $search = array('/{year}/', '/{month}/', '/{day}/');
    $replace = array(date("Y"), date("m"), date("d"));
    return preg_replace($search, $replace, $url);
}
	 

	/**
	 * bunnyapi_check_https
	 *
	 * Check if site is secure or not and return the prefix
	 *
	 * @since 1.0.0
	 * @param @input the url of the media file
	 */

public static function bunnyapi_check_https() {
	if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
		return 'https://'; 
	}
	return 'http://';
    
}

} ?>
