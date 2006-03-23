List Style Plugin for the TinyMCE Editor
----------------------------------------

By Scott Eade (seade at policypoint dot net)
March 10, 2006
Version 1.1.0

The List Style plugin allows you to set the list-style-type CSS property on
lists within the TinyMCE editor.

Installation Instructions
-------------------------

* Install the files under the TinyMCE plugins directory.
* Add plugin to TinyMCE plugin option list.  Example: plugins : "liststyle"
* Add the liststyle button to the button list.
  Example: theme_advanced_button3_add : "liststyle"

Initialization example
----------------------

    tinyMCE.init({
        theme : "advanced",
        mode : "textareas",
        plugins : "liststyle",
        theme_advanced_buttons3_add : "liststyle"
    });

History
-------

* 2006-03-10: Version 1.1.0 released.
  - Updated for TinyMCE plugin architecture change introduced in TinyMCE 2.0.3.
    Use ListStyle 1.0.1 if you are using a TinyMCE 2.0.0 - 2.0.2.
* 2006-01-30: Version 1.0.2 released.
  - Fixed error that occurred when invoked on a non-LI element.
  - Consistently use single quotes in plugin.
  - Added compressed plugin file.
* 2005-10-11: Version 1.0.1 released.  Changes made thanks to spocke:
  - Fixed so it uses inst.getFocusElement instead of the deprecated
    tinyMCE.selectedElement.
  - Moved the style information to a separate .css file.
  - Made it possible for translation of all labels.
  - Translated the plugin into Swedish.
* 2005-10-07: Version 1.0 released.

Copyright and license
---------------------

* Copyright 2005-2006 PolicyPoint Technologies Pty. Ltd.
* License: LGPL
