<?php

include '../../../../../ibrowserconfig.inc';
$tinyMCE_dir = 'modules/tinymce/xartemplates/includes/tinymce/jscripts/tiny_mce/';

if (!ereg('/$', $HTTP_SERVER_VARS['DOCUMENT_ROOT']))
  $tinyMCE_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].$tinyMCE_dir;
else
  $tinyMCE_root = $HTTP_SERVER_VARS['DOCUMENT_ROOT'].substr($tinyMCE_dir,1,strlen($tinyMCE_dir)-1);

?>