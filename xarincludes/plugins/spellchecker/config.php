<?php
    $spellCheckerConfig = array();

    // Spellchecker class use - uncomment those classes that are wanted for use
    //require_once("classes/TinyPspellShell.class.php"); // Command line pspell
    require_once("classes/TinyGoogleSpell.class.php"); // Google web service
    //require_once("classes/TinyPspell.class.php"); // Internal PHP version

    // General settings - this is important
    $spellCheckerConfig['enabled'] = true;


    // Only use the following if you are using the pspell classes else don't worry about them
    // Default settings
    $spellCheckerConfig['default.language'] = 'en';
    $spellCheckerConfig['default.mode'] = PSPELL_FAST;

    // Normaly not required to configure
    $spellCheckerConfig['default.spelling'] = "";
    $spellCheckerConfig['default.jargon'] = "";
    $spellCheckerConfig['default.encoding'] = "";

    // Pspell shell specific settings
    $spellCheckerConfig['tinypspellshell.aspell'] = '/usr/bin/aspell';
    $spellCheckerConfig['tinypspellshell.tmp'] = '/tmp';
?>
