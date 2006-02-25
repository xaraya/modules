INSTALLATION AND USE - XarTinyMCE Version 1.0.3

UPGRADES to 1.0.3:
IMPORTANT!! PLEASE SEE SPECIAL UPGRADE CONSIDERATIONS AT THE END OF THIS DOCUMENT

XarTinyMCE is a configurable, lightweight WYSIWYG editor.
See the documentation included in the docs directory of the module for
browse compatibility, and further options for customization.

The current version of this Xaraya integration provides for
configuration options via a user interface in the admin configuration area for XarTinyMCE.
There is also integration of an Image Browser.
This version sees new functionality in the Image Browser.
Image manager for xartinymce is also available for download separately.

NOTE: Make sure you use the NEW CONFIGURATION FILE for ibrowser in tinymce/xartemplates/includes directory.


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

To UPGRADE to Version 1.0.3:

There has been a number of major changes in tinymce that could BREAK COMPTIBILITY.
Please check the following to ensure xartinymce continues to work for you.

When you are ready to upgrade your tinymce files:

1. Remove ALL files in the tinymce module directory and subdirectories and
   replace with the upgraded module and files. There have been major
   changes in the file directory tree so please ensure you remove the old module.

2. Go to your Administration -> Modules ->View All
   Click Upgrade
   Click Activate

3. Go to your admin Dynamic Data -> Property Types -> Flush Property Cache

4. Go to Administration -> XarTinyMCE -> Modify Config
   Go to ***each*** TAB and ensure the values are as required.
   Make sure you check any new or upgraded options are filled in.
   Click Submit button on EACH screen to confirm.


MAJOR CHANGES
*************
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
This is a new Ibrowser image manager plugin with version 1.0.0 of xartinymce.

Please make sure you copy the ibrowser config.inc file in
modules/tinymce/xartemplates/includes/ibrowserconfig.inc to your
var/ibrowser/ directory and update the configuration settings there.

THERE IS A NEW IBROWSER CONFIGURATION FILE - Please update your old one.

Only relative paths are available at the moment until full ibrowser integration is completed.

Most settings will work as is, but you must set the image libraries to your own.
Also, keep in mind that using Xaraya's inbuilt Uploads and Images module
is much more secure. If you use the image browser, please be sure once you have
modified the file, to chmod the ibrowserconfig.inc file chmod 444 so it is not writeable.

Using the ibrowser also means that the path to your images will be available to those
browsing your site.

For added security, turn off Upload ability in the IBrowser and use the Uploads and Images module.
Point your Image library to the Image module's directory or trusted directory of Uploads.

An alternative to using the IBrowser is to install the Images and Uploads modules.
You can attach an Upload DD field to your articles and upload images to an image
or trusted directory. From there you can drag the link of the file to your wysiwyg editor.
Alternatively - cut and paste the image to your WYSIWYG editor from word or a supported image editor.

   That's it!
******************************

Feature requests and bug reports to:
http://bugs.xaraya.com

Miscellaneous discussion, news and download:
http://xaraya.athomeandabout.com

Thanks
Jo :)


