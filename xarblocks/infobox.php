<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003-4 Xaraya
//  (c) 2003 XT-Commerce
//   Third Party contributions:
//   Loginbox V1.0            Aubrey Kilian <aubrey@mycon.co.za>
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

/**
 * Initialise the block
 */
function commerce_infoboxblock_init()
{
    return array(
        'content_text' => '',
        'content_type' => 'text',
        'expire' => 0,
        'hide_empty' => true,
        'custom_format' => '',
        'hide_errors' => true,
        'start_date' => '',
        'end_date' => ''
    );
}

/**
 * Get information on the block ($blockinfo array)
 */
function commerce_infoboxblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => 'commerce_infobox_update',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true,
        'notes' => "content_type can be 'text', 'html', 'php' or 'data'"
    );
}

/**
 * Display function
 * @param $blockinfo array
 * @returns $blockinfo array
 */
function commerce_infoboxblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}



//$box_content='';


  if ($_SESSION['customers_status']['customers_status_image']!='') {
    $contentpath = 'admin/images/icons/' . $_SESSION['customers_status']['customers_status_image'];
    $loginboxcontent = '<center>' . xtc_image(xarTplGetImage($contentpath)) . '</center>';
  }
  $loginboxcontent .= BOX_LOGINBOX_STATUS . '<b>' . $_SESSION['customers_status']['customers_status_name'] . '</b><br>';
  if ($_SESSION['customers_status']['customers_status_show_price'] == 0) {
    $loginboxcontent .= NOT_ALLOWED_TO_SEE_PRICES_TEXT;
  } else  {
    if ($_SESSION['customers_status']['customers_status_show_price_tax'] == 1) {
      $loginboxcontent .= BOX_LOGINBOX_INCL . '<br>';
    } else {
      $loginboxcontent .= BOX_LOGINBOX_EXCL . '<br>';
    }
    if ($_SESSION['customers_status']['customers_status_discount'] != '0.00') {
      $loginboxcontent.=BOX_LOGINBOX_DISCOUNT . ' ' . $_SESSION['customers_status']['customers_status_discount'] . '%<br>';
    }
    if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] == 1  && $_SESSION['customers_status']['customers_status_ot_discount'] != '0.00') {
      $loginboxcontent .= BOX_LOGINBOX_DISCOUNT_TEXT . ' '  . $_SESSION['customers_status']['customers_status_ot_discount'] . ' % ' . BOX_LOGINBOX_DISCOUNT_OT . '<br>';
    }
  }



    $box_smarty->assign('BOX_CONTENT', $loginboxcontent);
    $box_smarty->assign('language', $_SESSION['language']);
/*          // set cache ID
  if (USE_CACHE=='false') {
  $box_smarty->caching = 0;
  $box_infobox= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_infobox.html');
  } else {
  $box_smarty->caching = 1;
  $box_smarty->cache_lifetime=CACHE_LIFETIME;
  $box_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$_SESSION['customers_status']['customers_status_id'];
  $box_infobox= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_infobox.html',$cache_id);
  }
  */
    $blockinfo['content'] = $data;
    return $blockinfo;
}
?>