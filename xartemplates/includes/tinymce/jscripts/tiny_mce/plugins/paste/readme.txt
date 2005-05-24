 Paste plugin for TinyMCE
------------------------------

This plugin adds paste as plain text and paste from Word icons to TinyMCE. This plugin was developed by Ryan Demmer and modified by
the TinyMCE crew to be more general and some extra features where added.

Installation instructions:
  * Add plugin to TinyMCE plugin option list example: plugins : "paste".
  * Add the plaintext button name to button list, example: theme_advanced_buttons3_add : "pastetext,pasteword".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "paste",
    theme_advanced_buttons3_add : "pastetext,pasteword",
    plaintext_create_paragraphs : false
  });

Options:
 [paste_create_paragraphs] - If enabled double linefeeds are converted to paragraph
                             elements when using the plain text dialog. This is enabled by default.
