Dictionary plugin for TinyMCE
This plugin utilizes the Merriam Webster website for word look-ups
Plugin URL: http://mudbomb.com
______________________________________________

Installation instructions:
  * Copy the dictionary directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "dictionary".
  * Add the dictionary button name to button list, example: theme_advanced_buttons3_add : "dictionary".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "dictionary",
    theme_advanced_buttons3_add : "dictionary"
  });
