INSTALLATION AND USE - XarTinyMCE Version 0.2.0

XarTinyMCE is a very configurable, lightweight WYSIWYG editor.
It is early in it's development stage so I expect further enhancements. 
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
   a. put the tinymce folder in your Xaraya modules directory
   b. go to the Adminpanel menu -> Modules -> View All
   c. Click on the "install" link of the tinymce module

2. The XarTinyMCE editor is not turned on by default.
   Once the tinymce module is installed it needs to be turned on by default.
   With this version you can set XarTinyMCE as the default WYSIWYG from the module's
   configuration.
   In case you have other editors, you can also set the default in Base module:
   a. go to the Adminpanels menu -> Base -> Modify Config
   b. choose the tab "Other"
   c. on the wysiwyg editor dropdown choose "tinymce" as the default editor

3. In order to make modules in Xaraya use the XarTinyMCE module to display WYSIWYG,
    you need to add the following snippet at the top of any of your page templates.
    eg usually this is default.xt in themes/yourtheme/pages directory.
    The code will affect any textareas in the template or included in that template.
    eg If you add it to default.xt page template it will affect all textareas
    in all templates that are also used in the default.xt template.
    You may wish to add it to one specific module or other template where you wish
    to convert the textarea to WYSIWYG (eg Xarbb newreply.xt or newtopic.xt):

    <xar:if condition="xarModGetVar('base','editor') eq 'tinymce'">
        <xar:template file="tinymce_insert" module="tinymce" type="module" />
    </xar:if>

    The default Mode in the configuration settings for XarTinyMCE is "All Text Areas"
    and enables the WYSIWYG action as described above.

    Additional method:
    You can become more specific by choosing the Mode of "Special Labelled Only" in
    the configuration options.
    You must then also add the attribute: mce_editable="true"
    to any "textarea" or "div" tag that you wish to use as a WYSIWYG textarea.

4. Alternatively, add a xarTinyMCE wysiwyg textarea to any of your articles and the editor.
   No need to add any template tags. Just set the configuration in Modify Config

IMAGE BROWSER (IBROWSER), UPLOADS MODULE, SECURITY
******************************
Please make sure you edit and update any settings in the
modules/tinymce/xartemplates/includes/ibrowserconfig.inc file

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

   That's it!
******************************

Feature requests and bug reports to:
http://bugs.xaraya.com

Miscellaneous discussion, news and download:
http://xaraya.athomeandabout.com

Thanks
Jo :)


