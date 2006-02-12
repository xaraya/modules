Loremipsum plugin for TinyMCE

-----------------------------

About:
  This plugin generates dummy filler text for your pages. Useful during testing the editor and/or CMS systems.
  More info on what 'Lorem ipsum' is can be found here: http://www.lipsum.com/
  This is a plugi contributed by Gusztáv Pálvölgyi (Hungary, Sopron).
  
Usage:
  * Just place your text cursor anywhere in your tinyMCE editor and push plugin button.
  * You can choose from four texts. Two texts give you pseudo latin output, 
  one gives you sentences from Asimov's 'The robots of dawn' 
  and one gives you some text in hungarian language from 'Rendezvous with Rama' by Arthur C. Clarke
  
Adding own text:
  You get four texts with the plugin to choose from but you can easily add your own text too.
  Texts are in the text.js file.
  Each text is an array element, you have to separate sentences with the pipe (|) symbol.
  
Compatibility:
  I developed this plugin on a linux machine and tested on Firefox 1.0, tiny_MCE 1.43. It worked fine.
  
Installation instructions:
  * Copy the lorem directory to the plugins directory of TinyMCE (/jscripts/tiny_mce/plugins).
  * Add plugin to TinyMCE plugin option list example: plugins : "lorem".

Initialization example:
  tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    plugins : "lorem",
    theme_advanced_buttons3_add : "lorem"
  });