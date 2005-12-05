List Style Plugin for the TinyMCE Editor
----------------------------------------

By Scott Eade (seade at policypoint dot net)
October 7, 2005
Version 1.01

The List Style plugin allows you to set the list-style-type CSS property on
lists within the TinyMCE editor.

Installation Instructions
-------------------------

* Install the files under the TinyMCE plugins directory.
* Add plugin to TinyMCE plugin option list.  Example: plugins : "liststyle"
* Add the liststyle button to the button list.  Example: theme_advanced_button3_add : "liststyle"

Initialization example
----------------------

	tinyMCE.init({
		theme : "advanced",
		mode : "textareas",
		plugins : "liststyle",
		theme_advanced_buttons3_add : "liststyle"
	});

Copyright and license
---------------------

* Copyright 2005 PolicyPoint Technologies Pty. Ltd.
* License: LGPL
