<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

function commerce_userapi_create_spaw($args)
{
    include_once 'modules/commerce/xarclasses/spaw/spaw_control.class.php';
    extract($args);
    if (!isset($name)) $name = 'Unknown window';
    $initialvalue = isset($value) ? $value : '' ;
    $sw = new SPAW_Wysiwyg(
                  $control_name = $name,      // control's name
                  $value= $initialvalue,      // initial value
                  $lang='',                   // language
                  $mode = 'full',                 // toolbar mode
                  $theme='default',                  // theme (skin)
                  $width='100%',              // width
                  $height='400px',            // height
                  $css_stylesheet='',         // css stylesheet file for content
                  $dropdown_data=''           // data for dropdowns (style, font, etc.)
                );
    return $sw->show();
}
?>