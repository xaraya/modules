<?php

include '../../../../../ibrowserconfig.inc';
$tinyMCE_dir = 'modules/tinymce/xartemplates/includes/tinymce/jscripts/tiny_mce/';

if (!ereg('/$', $_SERVER["DOCUMENT_ROOT"]))
  $tinyMCE_root = $_SERVER["DOCUMENT_ROOT"].$tinyMCE_dir;
else
  $tinyMCE_root = $_SERVER["DOCUMENT_ROOT"].substr($tinyMCE_dir,1,strlen($tinyMCE_dir)-1);

?>
