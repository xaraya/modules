<?php
// ================================================
// SPAW PHP WYSIWYG editor control
// ================================================
// Configuration file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-27
// ================================================
// lets rebuld include path for shop config
$loc_path=$_SERVER['PHP_SELF'];
$loc_path=str_replace('colorpicker.php','',$loc_path);
$loc_path=str_replace('confirm.php','',$loc_path);
$loc_path=str_replace('img.php','',$loc_path);
$loc_path=str_replace('img_library.php','',$loc_path);
$loc_path=str_replace('spaw_control.php','',$loc_path);
$loc_path=str_replace('table.php','',$loc_path);
$loc_path=str_replace('td.php','',$loc_path);
$loc_path=str_replace('classes/spaw/dialogs/','',$loc_path);

  if (file_exists($_SERVER['DOCUMENT_ROOT'].$loc_path.'local/configure.php')) {
    include($_SERVER['DOCUMENT_ROOT'].$loc_path.'local/configure.php');
  } else {
    include($_SERVER['DOCUMENT_ROOT'].$loc_path.'configure.php');
  }

// require_once ($_SERVER['DOCUMENT_ROOT'].$loc_path.'configure.php');


// base url for images
$spaw_base_url = HTTP_SERVER.'/';

$spaw_root=DIR_FS_ADMIN.DIR_WS_CLASSES.'spaw/';
$spaw_dir = HTTP_SERVER.DIR_WS_ADMIN.DIR_WS_CLASSES.'spaw/';
//$spaw_dir ='/spaw/';

$spaw_default_toolbars = 'default';
$spaw_default_theme = 'default';
$spaw_default_lang = 'de';
$spaw_default_css_stylesheet = $spaw_dir.'wysiwyg.css';

// add javascript inline or via separate file
$spaw_inline_js = true;

// use active toolbar (reflecting current style) or static
$spaw_active_toolbar = true;

// default dropdown content
$spaw_dropdown_data['style']['default'] = 'Normal';

$spaw_dropdown_data['font']['Arial'] = 'Arial';
$spaw_dropdown_data['font']['Courier'] = 'Courier';
$spaw_dropdown_data['font']['Tahoma'] = 'Tahoma';
$spaw_dropdown_data['font']['Times New Roman'] = 'Times';
$spaw_dropdown_data['font']['Verdana'] = 'Verdana';

$spaw_dropdown_data['fontsize']['1'] = '1';
$spaw_dropdown_data['fontsize']['2'] = '2';
$spaw_dropdown_data['fontsize']['3'] = '3';
$spaw_dropdown_data['fontsize']['4'] = '4';
$spaw_dropdown_data['fontsize']['5'] = '5';
$spaw_dropdown_data['fontsize']['6'] = '6';

$spaw_dropdown_data['paragraph']['Normal'] = 'Normal';
$spaw_dropdown_data['paragraph']['Heading 1'] = 'Heading 1';
$spaw_dropdown_data['paragraph']['Heading 2'] = 'Heading 2';
$spaw_dropdown_data['paragraph']['Heading 3'] = 'Heading 3';
$spaw_dropdown_data['paragraph']['Heading 4'] = 'Heading 4';
$spaw_dropdown_data['paragraph']['Heading 5'] = 'Heading 5';
$spaw_dropdown_data['paragraph']['Heading 6'] = 'Heading 6';

// image library related config

// allowed extentions for uploaded image files
$spaw_valid_imgs = array('gif', 'jpg', 'jpeg', 'png');

// allow upload in image library
$spaw_upload_allowed = true;

// image libraries
$spaw_imglibs = array(
  array(
    'value'   => DIR_WS_CATALOG.'images/content/',
    'text'    => 'Images-Content',
  ),
  array(
    'value'   => DIR_WS_CATALOG.'images/product_images/info_images/',
    'text'    => 'Products - Product-Info',
  ),
    array(
    'value'   => DIR_WS_CATALOG.'images/product_images/original_images/',
    'text'    => 'Products - Original',
  ),
    array(
    'value'   => DIR_WS_CATALOG.'images/product_images/popup_images/',
    'text'    => 'Products - Popup',
  ),
      array(
    'value'   => DIR_WS_CATALOG.'images/product_images/thumbnail_images/',
    'text'    => 'Products - Thumbnails',
  )
);


?>