<?php
/**** START XARAYA MODIFICATION ****/
// we need to find the directory our server is opperating in
// hopefully this is complete :)
if(isset($_SERVER['DOCUMENT_ROOT'])) {
    $root_path = $_SERVER['DOCUMENT_ROOT'];
} elseif(isset($HTTP_SERVER_VARS['DOCUMENT_ROOT'])) {
    $root_path = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
} else {
    $root_path = getenv('DOCUMENT_ROOT');
}
// include image library config settings
if (is_file($root_path.'/var/ibrowser/ibrowserconfig.inc')) {
    include_once $root_path.'/var/ibrowser/ibrowserconfig.inc';
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
