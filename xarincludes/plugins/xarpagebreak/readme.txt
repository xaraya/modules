 Xaraya Page Break plugin for TinyMCE
----------------------------------------

About:
  TineMCE plugin that helps inserting and presenting the <!--pagebreak--> tag utilized by the Xaraya CMS.
  Note this tag only works in the articles module.
  Contributed by Spiros Gatzounas (sga@otenet.gr). Based on the code of the Flash plugin for TinyMCE by Michael Keck.

Installation instructions:
  * Copy the xarpagebreak directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list. Example: plugins : "xarpagebreak".
  * Add "img[src|alt|title]" to extended_valid_elements option.
  * Add the xarpagebreak button name to button list, example: theme_advanced_buttons3_add : "xarpagebreak".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "xarpagebreak",
    theme_advanced_buttons3_add : "xarpagebreak",
    extended_valid_elements : "img[src|alt|title]"
  });

