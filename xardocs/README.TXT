INSTALLATION AND USE - XarTinyMCE Version 1.5.3

UPGRADES to 1.5.3:
PLEASE SEE UPGRADE CONSIDERATIONS AT THE END OF THIS DOCUMENT

XarTinyMCE is a configurable, lightweight WYSIWYG editor.
See the documentation included in the docs directory of the module for
browse compatibility, and further options for customization.

The current version of this Xaraya integration provides for
configuration options via a user interface in the admin configuration area for XarTinyMCE.
There is also integration of a multipurpose Filebrowser allowing you to browse, upload, and link file, 
or create and rename files and directories.


NOTE: Make sure you use the COMBINED CONFIGURATION FILE for
      filebrowser, ibrowser and imanager found in tinymce/xartemplates/includes directory.

Image manager (imanager), along with IBrowser and a number of other custom xartinymce plugins are
available for download separately at http://xarigami.com

****************************
To INSTALL and get started:

1. Install this module like you would any other Xaraya module. That is:
   - Put the tinymce folder in your Xaraya modules directory
   - Go to the Administration menu -> Modules -> View All
   - Click on the "install" link of the tinymce module

2. Make sure you have the HTML module installed, and your allowed tags 
   set in addition to allowing with attributes if relevant.
   The HTML module determines what tags are allowed and *if* attributes are allowed.
   It does not determine what attributes are allowed. Tinymce applies additional
   filtering for tags and attributes and works with a default set (see the usage instructions).

3. Go to xarTinyMCE modify config Install Options tab: 
   - Activate TinyMCE by checking the Activate tinymce checkbox
   - Set the Mode: Optimal method is to use "Specifically labelled text areas"
     and xarTinymce GUI DD property to replace your text areas
   - Set the mode to Manual if you are using specially labelled text areas or with DD properties
   
4. Clear your xaraya and also your browser cache before using.

5. For filebrowsing with FileBrowser or image management with the ibrowser and imanager plugin,
   copy the tinymceconfig.inc.php file in tinymce/xartemplates/includes/tinymceconfig.inc.php
   to your installation var/tinymce/tinymceconfig.inc.php (create the directory if it doesn't exist)
   Make sure you update the file paths and any other relevant variables.

Please read the Overview Documentation in the TinyMCE administration area for more detail.

******************************

To UPGRADE to Version 1.5.3:

There has been a number of major changes in tinymce that could BREAK COMPTIBILITY.
Please check the following to ensure xartinymce continues to work for you.

When you are ready to upgrade your tinymce files:

1. Remove ALL files in the tinymce module directory and subdirectories and
   replace with the upgraded module and files.
      
2. Go to your Administration -> Modules ->View All
   Click Upgrade
   Click Activate

3. Go to Administration -> XarTinyMCE -> Modify Config
   Go to ***each*** TAB and ensure the values are as required.
   Make sure you check any new or upgraded options are filled in.
   **Click Submit button**** on EACH screen to confirm and update the configuration and 
   generated javascript saved from your settings.

4. Update your custom templates. Pay particular attention to the following as variables have changed:

   - If you are using the activation snippet at the tope of your pages, please change the modvar
     from ('base','default') to ('tinymce','activetinymce').
     
   - If you have textarea or tinymce GUI DD property template overrides, compare them against the
     changed ones in this version and update yours accordingly.
   
   - Copy the tinymceconfig.inc.php file in tinymce/xartemplates/includes/tinymceconfig.inc.php
   to your installation var/tinymce/tinymceconfig.inc.php (create the directory if it doesn't exist)
   Make sure you update the file paths in the tinymceconfig.inc.php file.

MAJOR CHANGES
*************
See the changelog.txt in the xardocs directory or at http://xarigami.com/contrails/versions/xtm.


IMAGE BROWSER (IBROWSER), IMAGE MANAGER (IMANAGER), FILEBROWSER and  SECURITY
******************************
Filebrowser has been integrated as general file and image browser integrated into xartinymce.
Please note that using Filebrowser can leave your upload directories insecured. You will need to make
provision for protecting access to the upload directory via filebrowser.

For a more secure, but image only browser and upload application see ibrowser and imanager.

The updated xartinymce ibrowser and image manager plugin can be downloaded at http://xarigami.com

Please make sure you copy the tinymceconfig.inc.php file in
modules/tinymce/xartemplates/includes/tinymceconfig.inc.php to your
var/tinymce/ directory and update the configuration settings there.

Most settings will work as is, but you must set the image libraries to your own.

Using the filebrowser, ibrowser, imanager means that the path to your images will be available to those
browsing your site. If you want obfuscated paths and file names, please use the Xaraya Uploads module.

For added security, turn off Upload ability in the filebrowser, or the iBrowser and iManager plugins and 
use the Uploads and Images module. Point your Image library to the Image module's directory or 
trusted directory of Uploads.

KNOWN ISSUES
******************************
The equivalent iBrowser and iManager for xarTinymce 1.5.3 is iBrowser and iManager tested and working with IE 6, IE7, IE8
This current iBrowser and iManager  will not work in Safari or Opera.
******************************

Feature requests and bug reports to:
http://xarigami.com/contrails/displayproject/xtm

Resources, downloads, news, development discussion on xartinymce:
http://xarigami.com/project/xartinymce

Thanks
Jo :)


