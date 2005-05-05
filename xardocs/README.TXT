INSTALLATION AND USE - XarTinyMCE Version 0.5.1

UPGRADES to 0.5.5:
PLEASE SEE SPECIAL UPGRADE CONSIDERATIONS AT THE END OF THIS DOCUMENT

XarTinyMCE is a configurable, lightweight WYSIWYG editor.
In addition to being very configurable it has some nice features not
available in other wysiwyg editors.

See the documentation included in the docs directory of the module for
browse compatibility, and further options for customization.

The current version of this Xaraya integration provides for some basic
configuration options via a user interface in the admin configuration area for XarTinyMCE.
This version sees integration of a basic image browser.


****************************
To INSTALL and get started:

1. Install this module like you would any other Xaraya module. That is:
   - Put the tinymce folder in your Xaraya modules directory
   - Go to the Adminpanel menu -> Modules -> View All
   - Click on the "install" link of the tinymce module

2. The XarTinyMCE editor is not turned on by default.
   Once the tinymce module is installed it needs to be turned on.

   You can now do this in the XarTinyMCE Module Configuration area

   Alternatively:
   - go to the Adminpanels menu -> Base -> Modify Config
   - choose the tab "Other"
   - on the wysiwyg editor dropdown choose "tinymce" as the default editor

3. You can use XarTinyMCE module to enable wysiwyg in two basic ways
   (there are variations on these) by setting the MODE and ACTIVATION method:

  I. Setting the Mode : All Text Areas or Specifically Labelled
   a. Set the Mode to All Text areas. This will automatically enable WYSIWYG
      in ALL textareas. From Version 0.4.0 you *do not* need to
     add anything to your templates.

   b.You can become more specific by choosing the Mode of
     "Special Labelled Only" in the configuration options. You *do not* need to
     add anything to your templates.

     xarTinymce will only display the WYSIWYG text area when a
     textarea or div is marked with the attribute:
          mce_editable="true".
     The xarTinyMCE module allows you to do this manually yourself,
     or easily by using the xarTinyMCE GUI Dynamic Data Property.

     You can choose the xarTinyMCE GUI Dynamic Data Property as one of
     the field types in the Articles module when you define your
     Publication types (if you have current bitkeeper code or weekly snapshots),
     or add a xarTinyMCE Dynamic Data GUI property to your Articles module pubtypes
     or your xarPages page types or any other module where you need a WYSIWYG textarea.
     This will give you a WYSIWYG editor property where you want it without
     having to add any tags to your templates.

  II. Setting the Activation Method : Manual or Automatic
     Choose Automatic to have xarTinyMce automatically activated. You do not have to add any 
     template snippets. Alternatively choose <strong>Manual Override</strong> if you wish to 
     turn off automatic activation. 
     
     You can then manually override the loading of the WYSIWYG by adding your specific template 
     snippet code below to your page or other specific templates. All textareas contained or 
     included in the template where you have the XarTinymce tags are converted to WYSIWYG 
     according to the "All Text Area" or "Special Labelled Only" setting above.
     Alternatively, replace the variable "jstring" in an override template for the tinymce_insert.xd 
     template with your own tinymce variables.
     
      <xar:if condition="xarModGetVar('base','editor') eq 'tinymce'">
          <xar:template file="tinymce_insert" module="tinymce" type="module" />
      </xar:if>

IMAGE BROWSER (IBROWSER), UPLOADS MODULE, SECURITY
******************************
Please make sure you copy the ibrowser config.inc file in
modules/tinymce/xartemplates/includes/ibrowserconfig.inc to your
var/ibrowser/ directory and update the configuration settings there.
Only relative paths are available at the moment until full ibrowser integration is completed.

Most settings will work as is, but you must set the image libraries to your own.
Also, keep in mind that using Xaraya's inbuilt Uploads and Images module
is much more secure. If you use the image browser, please be sure once you have
modified the file, to chmod the ibrowserconfig.inc file chmod 444 so it is not writeable.

Using the ibrowser also means that the path to your images will be available to those
browsing your site.

For added security, turn of Upload ability in the IBrowser and use the Uploads and Images module.
Point your Image library to the Image module's directory or trusted directory of Uploads.

An alternative to using the IBrowser is to install the Images and Uploads modules.
You can attach an Upload DD field to your articles and upload images to an image
or trusted directory. From there you can drag the link of the file to your wysiwyg editor.
Alternatively - cut and paste the image to your WYSIWYG editor from word or a supported image editor.

******************************

To UPGRADE:
1. Remove all files in the tinymce module directory and all files in it and
   replace with the upgrade module and files
2. Go to your Administration -> Modules ->View All
   Click Upgrade
   Click Activate
3. Go to Administration -> XarTinyMCE -> Modify Config
   Check any new or upgraded options are filled in.
   
4. If you are upgrading to Version 0.4.0 please make sure you note the new
   activation methods. If you have added the snippet at the top of your templates you
   MUST set the Mode to Manual - the new 3rd option.

   That's it!
******************************

Feature requests and bug reports to:
http://bugs.xaraya.com

Miscellaneous discussion, news and download:
http://xaraya.athomeandabout.com

Thanks
Jo :)


