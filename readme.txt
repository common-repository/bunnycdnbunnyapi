=== Bunny.net+BunnyAPI ===
Contributors: NoteToServices, Bunny.net
Link: https://bunnyapi.com
Author website: https://notetoservices.com
Tags: bunnycdn, bunnyapi, upload, organize, file, file, images, media, cdn, storage, cache
Requires at least: 5.x
Tested up to: 6.3.1
Stable tag: 2.0.7
Requires PHP: 7.4
Plugin URI: http://wordpress.org/extend/plugins/bunnycdn-bunnyapi/
Extension of: https://bunny.net, https://wordpress.org/plugins/bunnycdn/, https://bunnyapi.com
Donate link: https://paypal.me/andbenice

Upload your files to Bunny.net instead of WordPress Media Library.

== Description ==
Use the BunnyAPI plugin to upload files to the Bunny.net file storage zone and save space on your website while speeding up all images. 

== Details ==
BunnyAPI (https://bunnyapi.com) by NoteToServices (https://notetoservices.com) is a collaboration project with Bunny.net (https://bunny.net) to extend the functionality of the Bunny.net plugin (https://wordpress.org/plugins/bunnycdn/) to WordPress, which serves as a file storage content-delivery network service. 

Bunny.net+BunnyAPI allows you to upload to Bunny.net directly from your website and view or link to your images.

This plugin requires registration of an account at Bunny.net.com and registering your API key at BunnyAPI.com.

== Installation ==

1. Install and activate the Bunny.net+BunnyAPI plugin.
2. Navigate to BunnyAPI.com, enter in the required Bunny.net information, and grab your BunnyAPI key.
3. If you do not have the Bunny.net plugin installed, locate the settings page under 'Tools' and enter your BunnyAPI key. If you do have the Bunny.net plugin installed, it will be located under Bunny.net.
4. Upload files through the Bunny.net Media located under Media to your Bunny.net storage zone.
5. Display images with your Bunny.net link.

== Technical Details ==

Bunny.net is a CDN service that stores and delivers files at hyper speeds for an amazing price.

BunnyAPI (https://bunnyapi.com) is a service that connects to Bunny.net (https://bunny.net). 

BunnyAPI handles only the API communications between Bunny.net and third-party web applications, including WordPress (Bunny.net+BunnyAPI plugin). 

The Bunny.net+BunnyAPI plugin adds functionality for uploading images to Bunny.net and serving them directly from your Bunny.net storage zone with the default hostname or hostname of your choice. The link structure is changed to point to the file on Bunny.net.

The Bunny.net+BunnyAPI plugin uses WordPress API and Bunny.net API.

== URGENT NOTICE == 

It is imperative that your Bunny.net pullzone name and storagezone name match. 

CORRECT: a pullzone name and a storagezone name of "bunnystuff" will be matched and open up a channel for the plugin to communicate.
INCORRECT: a pullzone name of "bunnyzone" and a storagezone name of "bunnystuff" will not be matched and therefore the plugin cannot communicate.

Hence, your Pullzone and Storage zone must have an identical name.

== Frequently Asked Questions ==

Q: What does Bunny.net+BunnyAPI do?
A: BunnyAPI extends the functionality of Bunny.net to upload files to your storage zone and serve them directly from your website via Bunny.net.

Q: Can I use BunnyAPI without a plan? 
A: You will need to create an account, a pullzone, and a storage zone at Bunny.net.com and then you will need sign up for an API key at BunnyAPI.com in order to use BunnyAPI and Bunny.net together.

Q: Is BunnyAPI free?  
A: BunnyAPI.com requires registration of your API key which you will need to obtain at Bunny.net.com. BunnyAPI.com is a separate third-party entity with its own pricing rates apart from Bunny.net.com. Both services are not free and have separate pricing plans.

Q: Can I change the default Bunny.net link to a custom domain?
A: Absolutely. To do this, you must add your CNAME host and records at Bunny.net.com. You should ensure you can view or access your files at this domain location. (example: www.bunnyapi.com/bunnyapi-logo.jpg) Next, you will update the hostname on the Settings page. If no hostname is set, BunnyAPI will use your default Bunny.net hostname.

Q: How much storage space do I have?
A: Bunny.net.com provides you with an unlimited amount of storage space and charges based on a pay-as-you-go system, offering very affordable pricing, but you can set limitations within the Zone settings.

Q: Will my original files be deleted after they are uploaded?
A: Since you are not uploading directly to the Media Library, no files will ever be stored on your server longer than they need to be.

Q: Will BunnyAPI delete files from Bunny.net if I delete them on WordPress?
A: Deleting files from your Bunny.net Media Library on WordPress will delete them from your Bunny.net storage zone.

Q: Will my files be deleted if I delete them from Bunny.net?
A: If you delete your files directly from Bunny.net.com, they will no longer show up in the Bunny.net Media Library on your website.

Q: Can I change the upload folder?
A: You may change the folder to anything you want. 
Note: Changing it will not automatically move any files from the old folder to the new folder. 

Another thing to note is that you cannot have a blank upload folder, as BunnyAPI does not support root uploading through the plugin.

Q: Can I upload the WordPress Media Library?
A: Absolutely! From the BunnyAPI settings screen, click on "Export to Bunny.net Media Library" and let BunnyAPI go to work. 
Depending on the size of your library, it could take a while. 

If the script times out, you will need to increase numbers for: 
max_execution_time, max_input_time, memory_limit, post_max_size, upload_max_filesize

Q: Can I add a date to the folder structure?
A: This is not currently possible.

Q: I have multiple subfolders but they aren't showing.
A: BunnyAPI does not currently read recursively into directories.

Q: Can I upload files to subdirectories?
A: Absolutely, for example, you could have uploads/2021/01 or uploads/memes.

Q: Can I set a pre-existing folder that already has files in it on Bunny.net?
A: Absolutely. Just set the folder and BunnyAPI will load anything within the root of that folder.

Q: Will BunnyAPI work with Gutenberg?
A: BunnyAPI will work with Gutenberg, but not directly. In other words, you will simply insert a new block, add media, and insert from URL. Or you can paste the img shortcode into the post or page directly.

Q: How do I make my images faster?
A: Bunny.net is a CDN that provides speedy images through servers worldwide, but if you want to speed up your images even further, we highly recommend that in your Pull Zone settings, under Caching, change your Cache Expiration Time and Browser Cache Expiration Time time to Override: 1 year.
Visit the bunnycdn blog for more recommendations. 

Q: Does my website need to be secure?
A: It is highly recommended that you use a secure website and force SSL on your storage zone. Trying to display secure images from an unsecure website or vice versa may result in odd browser behavior from the browser. However, having a non-secure website will still display images.

Q: My images are not showing up for my custom domain.
A: You should have covered at least two steps in order to link to your custom domain.
1) You will need to go to your Pull Zone settings in Bunny.net and link a hostname.
2) Once you have linked the hostname, navigate to your domain registrar or host and add the CNAME Record. (Type: CNAME Name: cdn Value: *.b-cdn.net  * = Bunny.net Pullzone Name (or hostname))

Changes may take up to 10 minutes or more to go in effect.

For more information: https://support.bunny.net/hc/en-us/articles/207790279-How-to-set-up-a-custom-CDN-hostname

Q: My images are not appearing in Bunny.net Media.
A: Ensure that your hostname is properly set, the proper folder is set, there are actually files within the folder, or try clearing the Bunny.net cache.

Q: Does BunnyAPI alter my images?
A: Unlike WordPress which may scale down large images, BunnyAPI does not alter your images in any way and will upload them directly to your BunnycDN Storage zone.
Note: Upon importing your Bunny.net Media Library to your WordPress Media Library, WordPress native functions may scale your images.

Q: Why does it take long to upload or delete files?
A: It depends on your server connection to BunnyAPI, but it may take several minutes to upload or delete multiple files. It is advisable not to upload more than 20 files at a time.

Q: Does BunnyAPI use any tracking?
We record every API call to ensure quality and debugging purposes in case something goes wrong, including the BunnyAPI key, the action, the URL submitted, and the IP address of the server that the request came from. 

BunnyAPI does not track any additional information about your website or visitors.

Q: Can you explain what everything is on the Settings page?
A: Absolutely.

BunnyAPI key is the API key you will need to get from BunnyAPI.com which allows the plugin to work. This key creates a connection between BunnyAPI, Bunny.net, and your website.

Bunny.net Storage Zone is a list of all of your storage zones pulled from Bunny.net.

Bunny.net Hostname is the hostname you linked in the Pullzone settings under Linked Hostnames and your DNS CNAME. By default, it will look something like *.b-cdn.net (* = pullzone name or assigned hostname).

Bunny.net Folder is the folder on Bunny.net where your files will be uploaded.

Save Bandwidth will toggle whether thumbnail images are loaded in Bunny.net Media or instead only clickable links are served thus saving bandwidth.

Update BunnyAPI Settings updates the settings on the current page.

Clear Cache will send a request to Bunny.net to clear the cache of the folder.

Import To Media Library Media will download all files from Bunny.net Media into your local library.

Export to Bunny.net Media Library will upload everything from the WordPress Media library to Bunny.net Media.

Q: Do I need to install the Bunny.net plugin?
A: The Bunny.net+BunnyAPI plugin is independent of the Bunny.net plugin, but both plugins do compliment each other.

Q: Are you Bunny.net?
A: We are a third-party company known as NoteToServices (https://notetoservices.com) and developed this plugin in collaboration with Bunny.net.

== Screenshots ==
1. BunnyAPI.com Registration
2. Bunny.net API Credentials
3. BunnyAPI Media Library under Media -- API key required
4. BunnyAPI under Tools if Bunny.net is not installed
5. BunnyAPI Entry Key 
6. BunnyAPI Settings
7. BunnyAPI Storage Zone Selection
8. Bunny.net CNAME Update
9. Bunny.net Hostname Selection
10. BunnyAPI Media Library with button
11. BunnyAPI Media Library  
12. File list

== Changelog ==
= 2.0.7 = 
Fixed issue with images not uploading to the correct folder

= 2.0.6 = 
Deprecated: bunnyapi_delete_all
Fixed an issue when selecting the "Check All" box would delete all files regardless of not having all files selected

= 2.0.5 = 
-- UPDATE REQUIRED --
Added "WordPress" meta data to URL queries for better plugin function to bunnyapi.com
BunnyCDN evolution: https://bunny.net/blog/bunnycdn-is-evolving-introducing-bunnynet/

= 2.0.4 =
Fixed issue where CSS was being loaded into the frontend

= 2.0.3 = 
Updated URL pointers in Functions file

= 2.0.2 = 
Fixed a design issue within the Bunny.net Media Library when viewing the gallery on the editor screen of a post or page

= 2.0.1 = 
Fixed several illegal string offset errors and array issues in the Bunny.net Media Library

= 2.0.0 = 
Complete revamp of BunnyAPI.com and BunnyAPI plugin 

= 1.0.0 =
Basic functionality to connect WordPress and Bunny.net via BunnyAPI

== Known Issues ==

If nothing is showing up because  your storage zone name and zone name do match, the most likely issue 
is that you chose a different data storage network from the default. BunnyAPI.com does not have the capability of auto-detection for this yet. 
On BunnyAPI.com, you will need to go to FTP & API Access under your storage zone and take note of where the storage data center is. For example, 
by default, all storage is located at storage.bunnycdn.com, however you may have chosen NY, LA, or SG, in which case it would then be: 
ny.storage.bunnycdn.com

To change this manully, you need to change the Storage Hostname via:
https://bunnyapi.com/?key=API&action=storagehostname&zone=zone&host=main|ny|la|sg

== Additional Info ==

- Through Bunny.net Media, you can select a raw URL or a [img] shortcode. 

- The [img] shortcode has multiple built-in features including image src, alignment, title, alt, link, and target. src is the only requirement.

- The [img] shortcode does not require a closing tag. 

- The [img] shortcode may include quotes if you prefer, but quotes are not required.

[img] shortcode usage:

[img src=https://bunnyapi.com/bunnyapi-logo.jpg href=https://bunnyapi.com/bunnyapi-logo.jpg link=yes|no align=left|center|right alt="Bunny API Logo" title="BunnyAPI website" target=_blank|_self|_top|_parent display=inline|block|contents|flex|grid|inline-block|inline-flex|inline-grid|inline-table|list-item|run-in|table|none|initial|inherit data-src=none|https://bunnyapi.com/bunnyapi-logo.jpg]

[img] shortcode details:
src = url of image [required]
href = link to image or another url 
link = link directly to image or not
align = position of image
alt = alternative text for image
title = used for a title in the link
target = how you wish to open the link of the image
display = how you want your image to be displayed
data-src = data-src used for Javascript purposes, leave blank to capture the default src or none to remove data-src

== Copyright Info ==

Copyright (C) 2015-2021 [NoteToServices](https://www.notetoservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA

== Terms ==

By using this plugin and enabling BunnyAPI, you agree that your website will need to make external calls to BunnyAPI.com and Bunny.net.com.

If you do not agree to these terms, please uninstall and delete this plugin.

If you find this plugin useful, please give a review.

Bunny.net's Terms of Service: https://bunny.net/tos
BunnyAPI's Terms of Service: https://bunnyapi.com/?page=terms

BunnyAPI is a third-party service of Bunny.net and was developed by NoteToServices under permission of Bunny.net. 

If you have any issues with the BunnyAPI plugin, please use the BunnyAPI support forums on WordPress, not the Bunny.net forums.

Bunny.net is a product of BunnyWay.

You may not redistribute this plugin or alter the name in any way without permission from NoteToServices or BunnyWay.
