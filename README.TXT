INSTALLATION AND USE - XarTinyMCE Version 1.1.0

UPGRADES to 1.1.0:
IMPORTANT!! PLEASE SEE SPECIAL UPGRADE CONSIDERATIONS AT THE END OF THIS DOCUMENT

XarTinyMCE is a configurable, lightweight WYSIWYG editor.
See the documentation included in the docs directory of the module for
browse compatibility, and further options for customization.

The current version of this Xaraya integration provides for
configuration options via a user interface in the admin configuration area for XarTinyMCE.
There is also integration of an multipurpose filebrowser in addition to ibrowser a specific image browser.
This version sees new functionality with enhanced block formats, new layer
and style plugins and better filebrowser integration.
Image manager (imanager) for xartinymce is also available for download separately.

NOTE: Make sure you use the NEW COMBINED CONFIGURATION FILE for 
      filebrowser, ibrowser and imanager found in tinymce/xartemplates/includes directory.


****************************
To INSTALL and get started:

1. Install this module like you would any other Xaraya module. That is:
   - Put the tinymce folder in your Xaraya modules directory
   - Go to the Adminpanel menu -> Modules -> View All
   - Click on the "install" link of the tinymce module

2. Make sure you have the HTML module installed, and your allowed tags 
   set in addition to allowing with attributes if relevant.
   The HTML module determines what tags are allowed and *if* attributes are allowed.
   It does not determine what attributes are allowed. Tinymce applies additional
   filtering for tags and attributes and works with a default set (see the usage instructions).

3. Go to xarTinyMCE modify config
   - Set TinyMCE as the default editor
   - Set the Mode: Optimal method is to use "Specifically labelled text areas"
     and xarTinymce GUI DD property to replace your text areas
   - Set the mode to Manual if you are using specially labelled text areas or with DD properties
   
4. Clear your xaraya and also your browser cache before using.

Please read the Overview Documentation in the TinyMCE administration area for more detail.

******************************

To UPGRADE to Version 1.1.0:

There has been a number of major changes in tinymce that could BREAK COMPTIBILITY.
Please check the following to ensure xartinymce continues to work for you.

When you are ready to upgrade your tinymce files:

1. Remove ALL files in the tinymce module directory and subdirectories and
   replace with the upgraded module and files. There have been major
   changes in the file directory tree so please ensure you remove the old module.
   
2. Copy the tinymceconfig.inc file in tinymce/xartemplates/includes/tinymceconfig.inc
   to your installation var/tinymce/tinymceconfig.inc (create the directory if it doesn't exist)
   Make sure you update the file paths as this configuration sets the paths for ibrowser,
   imanager and the new filebrowser functionality.

3. Go to your Administration -> Modules ->View All
   Click Upgrade
   Click Activate

4. Go to Administration -> XarTinyMCE -> Modify Config
   Go to ***each*** TAB and ensure the values are as required.
   Make sure you check any new or upgraded options are filled in.
   **Click Submit button**** on EACH screen to confirm and update the configuration and 
   generated javascript saved from your settings.


MAJOR CHANGES
*************
Version 1.1.0 has unified and futher enhanced configuration file 'tinymceconfig.inc' that replaces
the ibrowserconfig.inc and imanagerconfig.inc files. It is used for the new filebrowser, ibrowser and imanager.
It should be located in var/tinymce directory instead of the var/ibrowser directory.
Please make sure you update this file. Also check new additional blockformats for the drop down selection format list.

Version 1.0.4 has unified configuration file 'tinymceconfig.inc' that replaces
the ibrowserconfig.inc and imanagerconfig.inc files. It should be located in var/tinymce directory
instead of the var/ibrowser directory. Please make sure you update this file.

In Version 1.0.3 the main tinymce project had a complete rewrite and this means some Plugins are
not working, until the authors update these. The cleanup routine has been totally rewritten
as has a lot of the code to use classes. Plugins known to have broken with these changes are
removed. The IBrowser plugin has been updated for symlinks.

In Version 0.9.0 the textarea_trigger setting for mceEditable = [true|false] is deprecated
in favour of using classes to activate or inactivate the editor. Please use
editor_selector (default to mceEditor) and editor_deselector (default to mceNoEditor) to
set the class used to activate or deactivate your textareas in custom configurations.
This is recommended for w3c standards support.


IMAGE BROWSER (IBROWSER), UPLOADS MODULE, SECURITY
******************************
There is a new filebrowser and updated ibrowser image manager plugin with version 1.1.0 of xartinymce.

Please make sure you copy the NEW tinymceconfig.inc file in
modules/tinymce/xartemplates/includes/tinymceconfig.inc to your
var/tinymce/ directory and update the configuration settings there.

THERE IS A NEW FILEBROWSER/IBROWSER/IMANAGER CONFIGURATION FILE - Please update your old one.

Most settings will work as is, but you must set the image libraries to your own.

Using the filebrowser, ibrowser, imanager means that the path to your images will be available to those
browsing your site. If you want obfuscated paths and file names, please use the Xaraya Uploads module.

For added security, turn off Upload ability in the filebrowser, or the iBrowser and iManager plugins and 
use the Uploads and Images module. Point your Image library to the Image module's directory or 
trusted directory of Uploads.


   That's it!
******************************

Feature requests and bug reports to:
http://bugs.xaraya.com

Miscellaneous discussion, news and download:
http://xaraya.athomeandabout.com

Thanks
Jo :)


