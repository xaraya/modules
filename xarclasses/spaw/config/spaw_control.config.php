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

// include xtc config file

global  $spaw_dir,
        $spaw_default_toolbars,
        $spaw_default_theme,
        $spaw_default_lang,
        $spaw_default_css_stylesheet,
        $spaw_inline_js,
        $spaw_active_toolbar,
        $spaw_dropdown_data,
        $spaw_valid_imgs,
        $spaw_upload_allowed,
        $spaw_imglibs;

$spaw_dir = $spaw_root;
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

//TODO: check whether these indices actually make sense
$spaw_dropdown_data['var'][xarML('First Name')] = 'Firstname';
$spaw_dropdown_data['var'][xarML('Last Name')] = 'Lastname';



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
    'value'   => 'modules/commerce/xarimages/content/',
    'text'    => 'Images-Content',
  ),
  array(
    'value'   => 'you/need/to/change/this/too/',
    'text'    => 'Not configured',
  ),
);


?>