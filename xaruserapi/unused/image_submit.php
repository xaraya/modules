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

// The HTML form submit button wrapper function
// Outputs a button in the selected language
  function commerce_userapi_image_submit($image, $alt = '', $parameters = '') {

    $image_submit = '<input type="image" src="' . strtr(trim($language . '/buttons/'. $image), $array('"' => '&quot;')) . '" border="0" alt="' . xtc_parse_input_field_data($alt, array('"' => '&quot;')) . '"';

    if (xarModAPIFunc('commerce','user','not_null',array('arg' =>$alt)) $image_submit .= ' title=" ' . strtr(trim($alt), array('"' => '&quot;') . ' "';

    if (xarModAPIFunc('commerce','user','not_null',array('arg' =>$parameters))) $image_submit .= ' ' . $parameters;

    $image_submit .= '>';

    return $image_submit;
  }
 ?>