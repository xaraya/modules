INSTALLATION AND USE - TinyMCE Version 0.0.2

TinyMCE is a very configurable, lightweight WYSIWYG editor. 
It has limited options compared to HTMLArea, but is earlier in it's 
development stage so I expect further enhancements. In addition, it is very 
configurable and has some nice features not available in other wysiwyg editors.

See the documentation included in the docs directory of the module for 
browse compatibility, and further options for customization.

The current version of this Xaraya integration provides for some basic
configuration options via a user interface in the admin configuration area for TinyMCE.

****************************
To INSTALL and get started:

1. Install this module like you would any other Xaraya module. That is:
   a. put the tinymce folder in your Xaraya modules directory
   b. go to the Adminpanel menu -> Modules -> View All
   c. Click on the "install" link of the tinymce module

2. The TinyMCE editor is not turned on by default.
   Once the tinymce module is installed it needs to be turned on. To do this:
   a. go to the Adminpanels menu -> Base -> Modify Config
   b. choose the tab "Other"
   c. on the wysiwyg editor dropdown choose "tinymce" as the default editor

3. In order to make modules in Xaraya use the tinymce module to display wysiwyg,
    you need to add the following snippet at the top of any of your page templates.
    eg usually this is default.xt in themes/yourtheme/pages directory.
    You may have more than one page template and can choose to add it to one or more.

    <xar:if condition="xarModGetVar('base','editor') eq 'tinymce'">
        <xar:template file="tinymce_insert" module="tinymce" type="module" />
    </xar:if>

******************************
To UPGRADE:
1. Remove all files in the tinymce module directory and all files in it and 
   replace with the upgrade module and files
2. Go to your Administration -> Modules ->View All
   Click Upgrade
   Click Activate
3. Go to Administration -> TinyMCE -> Modify Config
   Check any new or upgraded options are filled in.
   
   That's it!
******************************
Feature requests and bug reports to:
http://bugs.xaraya.com

Miscellaneous discussion, news and download:
http://xaraya.athomeandabout.com

Thanks
Jo :)


