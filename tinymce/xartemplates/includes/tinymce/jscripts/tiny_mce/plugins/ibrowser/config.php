<?php
/**** START XARAYA MODIFICATION ****/
// we need to find the directory our server is opperating in
// hopefully this is complete :)
// This will only work if the site is installed in the document root of the website, not a dir
if(isset($_SERVER['DOCUMENT_ROOT'])) {
    $root_path = $_SERVER['DOCUMENT_ROOT'];
} elseif(isset($HTTP_SERVER_VARS['DOCUMENT_ROOT'])) {
    $root_path = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
} else {
    $root_path = getenv('DOCUMENT_ROOT');
}
// Now for same hacking ;)
if(isset($_SERVER['REQUEST_URI'])) {
    $scriptpath= dirname($_SERVER['REQUEST_URI']);
} elseif(isset($HTTP_SERVER_VARS['REQUEST_URI'])) {
    $scriptpath = dirname($HTTP_SERVER_VARS['REQUEST_URI']);
} else {
    $scriptpath= dirname(getenv('SCRIPT_NAME'));
}
//ew .. but it should work ;)
$scriptbase=str_replace('/modules/tinymce/xartemplates/includes/tinymce/jscripts/tiny_mce/plugins/ibrowser','',$scriptpath);
$realpath=$root_path.$scriptbase;
$realpath=str_replace('//','/',$realpath); //get rid of any double slashes

// include image library config settings
if (is_file($realpath.'/var/ibrowser/ibrowserconfig.inc')) {
    include_once $realpath.'/var/ibrowser/ibrowserconfig.inc';
} else {
    // look in the templates directory of this module for the default file
    include_once '../../../../../ibrowserconfig.inc';
}
/**** END XARAYA MODIFICATION ****/

$tinyMCE_dir = 'modules/tinymce/xartemplates/includes/tinymce/jscripts/tiny_mce/';

if (!ereg('/$', $_SERVER["DOCUMENT_ROOT"]))
  $tinyMCE_root = $_SERVER["DOCUMENT_ROOT"].$tinyMCE_dir;
else
  $tinyMCE_root = $_SERVER["DOCUMENT_ROOT"].substr($tinyMCE_dir,1,strlen($tinyMCE_dir)-1);

?>
