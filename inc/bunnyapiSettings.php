<?php
//add a url convert -- converts all current urls in all posts & pages to the bunnycdn url after a forced upload

class BunnyAPI 
{
	/**
	 * getOptions
	 *
	 * Returns the array of all the options with their default values in case they are not set
	 *
	 * @since 1.0.0
	 * @param 
	 */

	public static function getOptions() {
        return wp_parse_args(
			get_option('BunnyAPI'),
			array(
				"hostname" => BunnyAPI_PULLZONEDOMAIN,
				"stozone" => "",
				"folder" => BunnyAPI_DEFAULT_FOLDER,
				"api_key" => "",
                "savebandwidth" => ""
			)
		);
    }	

	/**
	 * getOption
	 *
	 * Returns the option value for the given option name. If the value is not set, the default is returned
	 *
	 * @since 1.0.0
	 * @param 
	 */

	public static function getOption($option)
	{
		$options = BunnyAPI::getOptions();
		return $options[$option];
	}

	/**
	 * validateSettings
	 *
	 * Returns the option value for the given option name. If the value is not set, the default is returned
	 *
	 * @since 1.0.0
	 * @param 
	 */
	
	public static function validateSettings($data)
	{ 
             //setting the hostname if there is data
             //clean, validate, escape the input from user
                if(isset($data['hostname'])) { $hostname =  BunnyAPI::cleanHostname(esc_url_raw($data['hostname'])); } else { $hostname =  bunnyapi_Functions::bunnyhost(); } 

             //setting a folder, removing any protocols or prefixes as this is just a folder
		if(isset($data['folder'])) { $folder = BunnyAPI::cleanHostname($data['folder']); } else { $folder = BunnyAPI_DEFAULT_FOLDER; }
 
 
                //BunnyAPI does not allow for a "root" upload and a folder must be set
                //uploads
                if(empty($folder)) { $folder = BunnyAPI_DEFAULT_FOLDER; }
                
		if(isset($data['stozone'])) { $stozone = BunnyAPI::cleanHostname(esc_url_raw($data['stozone'])); } else { $stozone = NULL; }
 
                //save bandwidth: will not load every single image individually and only text is loaded so no bandwidth is used from the download
		if(isset($data['savebandwidth'])) {  $savebandwidth = $data['savebandwidth']; } else { $savebandwidth = NULL; }

                 //set the BunnyAPI key
		if(isset($data['api_key'])) { $bunnyapi = $data['api_key']; } else { $bunnyapi = NULL; }
          //returning an array with the hostname, folder, bunnyapikey, and bandwidth option
		return array(
				"hostname" => 	          $hostname,
				"stozone" => 	          $stozone,
				"folder" => 		  sanitize_file_name ($folder),
				"api_key" => 	          sanitize_text_field($bunnyapi),
                "savebandwidth" =>        strtolower($savebandwidth)
			);
	}


	/**
	 * cleanHostname
	 *
	 * Returns the hostname filtered without the prefix
	 *
	 * @since 1.0.0
	 * @param 
	 */
	

	public static function cleanHostname($hostname)
	{
		$hostname = str_replace("http://", "", $hostname);
		$hostname = str_replace("https://", "", $hostname);

		return str_replace("/", "", $hostname);
	}

}


class BunnyAPISettings
{
	/**
	 * initialize
	 *
	 * Initialize the settings page
	 *
	 * @since 1.0.0
	 * @param 
	 */
	

	public static function initialize()
	{
   // if bunnycdn parent is installed, snuggle in!
    if(is_plugin_active('bunnycdn/bunnycdn.php')) { 

   add_submenu_page('bunnycdn', 'BunnyAPI', 'BunnyAPI', 'manage_options', __FILE__, array('BunnyAPISettings','bunnyapi_options_page'));

    } else {
        //otherwise, become a separate entity under tools!


        add_submenu_page( 'tools.php', 'BunnyAPI', 'BunnyAPI', 'manage_options', 'bunnycdn_extended', array(
				'BunnyAPISettings',
				'bunnyapi_options_page' )  );

    }
		//register BunnyAPI into WordPress
		register_setting('BunnyAPI', 'BunnyAPI', array("BunnyAPI", "validateSettings"));
		
	}


	/**
	 * bunnyapi_options_page
	 *
	 * Display the options page
	 *
	 * @since 1.0.0
	 * @param 
	 */
	
	public static function bunnyapi_options_page()
	{
		$options = BunnyAPI::getOptions();

                //load up our logo and UI:: where the magic is set!
		?> 
		<div style="width: 550px; padding-top: 20px; margin-left: auto; margin-right: auto; margin: auto; text-align: center; position: relative;">
			<a href="https://bunnyapi.com" target="_blank"><img border="0" src="<?php echo plugins_url('assets/bunnyapi-logo-bg.png', dirname(__FILE__) ); ?>"></img></a>
			<?php
                              // validation of BunnyAPI key
				if(empty(bunnyapi_Functions::bunnyapi_full_validation_apikey()))
				{
					echo '<h2>Enable Bunny.net+BunnyAPI</h2>';
				}
				else 
				{
					echo '<h2>Configure Bunny.net+BunnyAPI</h2>';
				}
			?>
			
			<form id="BunnyAPI_options_form" method="post" action="options.php">
				<?php settings_fields('BunnyAPI') ?>
			
				<!-- Simple settings -->
				<div id="BunnyAPI-simple-settings">
<?php
   //clear initial message
   $initmsg = NULL;

   //if the url contains clearcache command, send a command to clear the cache
   if(preg_match('/clearcache/i', bunnyapi_Functions::bunnyapi_currenturl())) { 
      bunnyapi_Functions::bunnyapi_clear_cache();
      $initmsg = sprintf(__('<p><span style="color:green;">Clear Cache successful.</span></p>', 'bunnyapi')); 
   }

   //if the url contains deleteall command, send a command to delete all
   if(preg_match('/deleteall/i', bunnyapi_Functions::bunnyapi_currenturl())) { 
      bunnyapi_Functions::bunnyapi_delete_all();
      $initmsg = sprintf(__('<p><span style="color:green;">Bunny.net Media deleted.</span></p>')); 
   }


   //if the url contains copy folder request, send a command to copy the OLD folder to the CURRENT folder
   if(preg_match('/cpfolder/i', bunnyapi_Functions::bunnyapi_currenturl())) {
      bunnyapi_Functions::bunnyapi_copy_all($_GET["cpfolder"]);
      $initmsg = sprintf(__('<p><span style="color:green;">Bunny.net Media copied.</span></p>', 'bunnyapi')); 
   }
   //if the url contains forcepush request, send a command to force the wordpress media library to upload to the bunnycdn media
   if(preg_match('/forcepush/i', bunnyapi_Functions::bunnyapi_currenturl())) {
      bunnyapi_Functions::bunnyapi_force_push($_GET["forcepush"]);
      $initmsg = sprintf(__('<p><span style="color:green;">WordPress Media Library uploaded to Bunny.net Media.</span></p>', 'bunnyapi')); 
   }

   //if the url contains download request, send a command to provide a download link to download Bunny.net Media
   if(preg_match('/download/i', bunnyapi_Functions::bunnyapi_currenturl())) {
      //sanitize and set the download folder
      if(isset($_GET["download"])) { 
      // bunnycdn media message containing download link
      $initmsg = sprintf(__('<p><span style="font-weight:bold;color:green;">Bunny.net Media imported to Local Media Library.</p>', 'bunnyapi'));
      }
    
     
      //assign bunnyapi key
      $bunny_apikey = BunnyAPI::getOption('api_key');
      
   } 
 
   //always checking to ensure the BunnyAPI key is not empty and being validated
  if(!empty(bunnyapi_Functions::bunnyapi_full_validation_apikey())) {
        echo __( $initmsg.'<p>View your account at <a href="https://bunny.net" target="_blank">Bunny.net</a> or connect a new storage zone at <a href="https://bunnyapi.com" target="_blank">BunnyAPI</a>.</p>', 'bunnyapi' );
   } else {
        echo __( '<p><a href="https://bunny.net" target="_blank">Sign up for an account at Bunny.net</a> and <a href="https://bunnyapi.com" target="_blank">register your Bunny.net account with BunnyAPI</a> to receive your BunnyAPI key.</p>', 'bunnyapi' );
   }
?>
					<table class="form-table">
						<tr valign="top">
						<th scope="row">
							BunnyAPI Key:
						</th>
						<td>
							<input type="text" name="BunnyAPI[api_key]" id="BunnyAPI_api_key" value="<?php echo BunnyAPI::getOption('api_key'); ?>" size="64" class="regular-text code" maxlength="12" />
							<p class="description">The BunnyAPI API key to manage the Bunny.net zones.</p>
						</td>
					</tr>



					</table>
				</div>
				
				
<?php 
//this fields only become visible with a valid BunnyAPI key
if(!empty(bunnyapi_Functions::bunnyapi_full_validation_apikey())) {  
$stozone = BunnyAPI::getOption('stozone');
$hostname = BunnyAPI::getOption('hostname');
if(isset($_GET["download"])) {  
    $folder = BunnyAPI::getOption('folder');
    bunnyapi_Functions::bunnyapi_downloadall($folder);
}
?>


				<table id="BunnyAPI-settings" class="form-table">
					<tr valign="top">
						<th scope="row">
							Bunny.net Storage Zone:
						</th>
						<td><select name="BunnyAPI[stozone]">
						<?php $stozones = bunnyapi_Functions::bunnyapi_storagezonelist();
						if(!empty($stozones)) { 
						    foreach($stozones as $sz) {
						        $stozname = $sz["Name"];
						        if(empty($stozone)) { $stozone = NULL; }
						        if(strcmp($stozone, $stozname) !== 0) { 
    						        echo '<option value="'.$stozname.'">'.$stozname.'</option>';
						        } else { 
    						        echo '<option value="'.$stozname.'" selected>'.$stozname.'</option>';  
						        }
						    }
						} else {
						  echo '<a href="'.$_SERVER['REQUEST_URI'].'"><button>Refresh Storage Zones</button></a>';
						}
						?>  
						     </select>
							<p class="description">List of all storage zones.</code>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							Bunny.net Hostname:
						</th>
						<td><?php $hostlist = bunnyapi_Functions::bunnyapi_hostlist($stozone); ?>
							<select name="BunnyAPI[hostname]">
							    <?php  
							if(!empty($stozone)) { 
							    if(isset($hostlist)) { 
							         $hostcheck = print_r($hostlist, true);
							         if(!preg_match('/empty/i', $hostcheck) || !empty($hostcheck)) { 
							             if(count($hostlist) >= 0) { $hostcount = count($hostlist) - 1; } else { count($hostcount); } 
							            //0 or greater items taken into account
							            for( $i = 0 ; $i <= $hostcount; $i++ ) { //loop through list of hosts
							                if(strcmp($hostname, $hostlist[$i]) !== 0) { // no match
    		    					    	 	       echo '<option value="'.$hostlist[$i].'">'.$hostlist[$i].'</option>';
							                } else { //match
							               	       echo '<option value="'.$hostlist[$i].'" selected>'.$hostlist[$i].'</option>';
							                }       
    							          }
    							    } else { 
									//preg_match('/empty/i', $hostlist) || empty($hostlist)
		    					            echo '<option value="" disabled>Storage Zone name not matched.</option>';
    							    } 
							    } else {  //empty($hostlist)
							        echo '<option value="" disabled>Storage Zone name not matched.</option>';
							    }
							} else { //empty($stozone)
							        echo '<option value="" disabled>Update this page to save Storage Zone name.</option>';
							}
    							 ?>
							</select>
							<p class="description">The hostname alias for the media library.<br>Default configuration <code>*.<?php echo BunnyAPI_PULLZONEDOMAIN; ?></code></p>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							Bunny.net Folder:
						</th>
						<td>
							<input type="text" name="BunnyAPI[folder]" id="BunnyAPI_folder" value="<?php echo (!empty(BunnyAPI::getOption('folder')) ? BunnyAPI::getOption('folder') : BunnyAPI_DEFAULT_FOLDER); ?>" size="64" class="regular-text code" />
							<p class="description">The Bunny.net folder where you wish to store your files.<br>Default configuration <code><?php echo BunnyAPI_DEFAULT_FOLDER; ?></code></p>
						</td>
					</tr>



			<tr valign="top">

						<th scope="row">
							Save Bandwidth:
	
						</th>
						<td>
							<select name="BunnyAPI[savebandwidth]"/> 
<?php
echo (BunnyAPI::getOption('savebandwidth') == 'yes') ? '<option value="yes" selected>Yes</option><option value="no">No</option>' : '<option value="no" selected>No</option><option value="Yes">Yes</option>';

?>

</select>
<p class="description">Toggle whether this site will download all images or just link to them. Default configuration <code>No</code></p>
						
						</td>
		         </tr>



			</table>
<?php } ?>
				<div>
					<p class="submit">
<input type="submit" name="BunnyAPI-save-button" id="BunnyAPI-save-button" class="button submit" style="color:#000;font-weight:bold;" value="<?php echo (empty(bunnyapi_Functions::bunnyapi_full_validation_apikey()) ? 'Enable BunnyAPI' : 'Update BunnyAPI Settings'); ?>">
						 
<?php //only visible with valid BunnyAPI key
if(!empty(bunnyapi_Functions::bunnyapi_full_validation_apikey())) { 
?>
<input type="button" id="BunnyAPI-clear-cache-button" onclick="javascript:clearcache();" style="font-weight:bold;" class="button submit" value="Clear Cache">   
						
  <br><br>

<input type="button" id="BunnyAPI-download-media-button" onclick="javascript:downloadall();" style="color:purple;font-weight:bold;" class="button submit" value="Import To Media Library"> 

<input onclick="javascript:pushall();" type="button" id="BunnyAPI-push-media-button" class="button submit" style="color:blue;font-weight:bold;" value="Export to Bunny.net Media Library">
 
<!-- <input onclick="javascript:copyall();" type="button" id="BunnyAPI-move-media-button" class="button submit" style="color:green;font-weight:bold;" value="Copy Bunny.net Media"> -->

<!-- <input onclick="javascript:deleteall();" type="button" id="BunnyAPI-delete-media-button" class="button submit" style="color:red;font-weight:bold;" value="Delete All Bunny.net Media"> -->


<p><small>Check out the <a href="<?php echo BunnyAPI_PLUGIN_URLDIR.BunnyAPI_PLUGIN_NAME; ?>/readme.txt" target="_blank">Readme file</a> for additional documentation.</small></p>
<?php } ?>
					</p>
				</div>


<script>

  function downloadall() {
if (confirm("Download the entire Bunny.net Media to WordPress Media Library?")) {
      window.location.replace(window.location.href + '&download=true');return true;
    }
  }
function clearcache() {
      window.location.replace(window.location.href + '&clearcache=true');return true;
}
function pushall() {
if (confirm("Upload the entire WordPress Media Library to Bunny.net Media?")) {
      window.location.replace(window.location.href + '&forcepush=<?php echo BunnyAPI::getOption('folder'); ?>');return true;
  }
}

function setHost(host) { 
    document.getElementById("BunnyAPI_hostname").value=host; 
} 
</script>
			</form>

			
		</div><?php
	}
}

?>
