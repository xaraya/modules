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

function commerce_userapi_show_category($args)
{
    global $foo, $categories_string, $id;

    // image for first level
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $img_1='<img src="' . xarTplGetImage($data['language'] . 'icon_arrow.jpg', 'commerce') . '">&nbsp;';

    for ($a=0; $a<$foo[$counter]['level']; $a++) {
        if ($foo[$counter]['level']=='1') {
            $categories_string .= "&nbsp;-&nbsp;";
        }
        $categories_string .= "&nbsp;&nbsp;";
    }

    if ($foo[$counter]['level']=='') {
        if (strlen($categories_string)=='0') {
            $categories_string .='<table width="100%"><tr><td class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">';
        }
        else {
            $categories_string .='</td></tr></table><table width="100%"><tr><td class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)">';
        }

    // image for first level
    $categories_string .= $img_1;
    $categories_string .= '<b><a href="';
    //<img src="templates/zanier/img/recht_small.gif">
    }
    else {
        $categories_string .= '<a href="';
    }
    if ($foo[$counter]['parent'] == 0) {
        $cPath_new = 'cPath=' . $counter;
    }
    else {
        $cPath_new = 'cPath=' . $foo[$counter]['path'];
    }

    $categories_string .= xtc_href_link(FILENAME_DEFAULT, $cPath_new);
    $categories_string .= '">';

    if ( ($id) && (in_array($counter, $id)) ) {
        $categories_string .= '<b>';
    }

    // display category name
    $categories_string .= $foo[$counter]['name'];

    if ( ($id) && (in_array($counter, $id)) ) {
        $categories_string .= '</b>';
    }

    if (xtc_has_category_subcategories($counter)) {
        $categories_string .= '';
    }
    if ($foo[$counter]['level']=='') {
        $categories_string .= '</a></b>';
    } else {
        $categories_string .= '</a>';
    }

    if (SHOW_COUNTS == 'true') {
        $products_in_category = xtc_count_products_in_category($counter);
        if ($products_in_category > 0) {
            $categories_string .= '&nbsp;(' . $products_in_category . ')';
        }
    }

    $categories_string .= '<br>';

    if ($foo[$counter]['next_id']) {
        xarModAPIFunc('commerce','user','show_category', array('node' => $foo[$counter]['next_id']));
    }
}

?>