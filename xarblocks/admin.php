<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003-4 Xaraya
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

/**
 * Initialise the block
 */
function commerce_admincommerceblock_init()
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
function commerce_admincommerceblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => '',
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
function commerce_admincommerceblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}

//$box_content='';


  $orders_contents = '';
  $orders_status_query = new xenQuery("select orders_status_name, orders_status_id from " . TABLE_ORDERS_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      $q->run();
  while ($orders_status = $q->output()) {
    $orders_pending_query = new xenQuery("select count(*) as count from " . TABLE_ORDERS . " where orders_status = '" . $orders_status['orders_status_id'] . "'");
      $q = new xenQuery();
      $q->run();
    $orders_pending = $q->output();
    $orders_contents .= '<a href="' . xarModURL('commerce','user',(FILENAME_ORDERS, 'selected_box=customers&status=' . $orders_status['orders_status_id']) . '">' . $orders_status['orders_status_name'] . '</a>: ' . $orders_pending['count'] . '<br>';
  }
  $orders_contents = substr($orders_contents, 0, -4);

  $customers_query = new xenQuery("select count(*) as count from " . TABLE_CUSTOMERS);
      $q = new xenQuery();
      $q->run();
  $customers = $q->output();
  $products_query = new xenQuery("select count(*) as count from " . TABLE_PRODUCTS . " where products_status = '1'");
      $q = new xenQuery();
      $q->run();
  $products = $q->output();
  $reviews_query = new xenQuery("select count(*) as count from " . TABLE_REVIEWS);
      $q = new xenQuery();
      $q->run();
  $reviews = $q->output();
  $admin_image = '<a href="' . xarModURL('commerce','user',(FILENAME_START,'').'">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES.'admin_button.gif') .'</a>';
  if ($cPath != '' && $product_info['products_id'] != '') {
    $admin_link='<a href="' . xarModURL('commerce','user',(FILENAME_EDIT_PRODUCTS, 'cPath=' . $cPath . '&pID=' . $product_info['products_id']) . '&action=new_product' . '" target="_blank">' . xtc_image(xarTplGetImage('icons/edit_product.gif') . '</a>';
  }

  $box_content= '<b>' . BOX_TITLE_STATISTICS . '</b><br>' . $orders_contents . '<br>' .
                                         BOX_ENTRY_CUSTOMERS . ' ' . $customers['count'] . '<br>' .
                                         BOX_ENTRY_PRODUCTS . ' ' . $products['count'] . '<br>' .
                                         BOX_ENTRY_REVIEWS . ' ' . $reviews['count'] .'<br>' .
                                         $admin_image . '<br>' .$admin_link;


    $box_smarty->assign('BOX_TITLE', BOX_HEADING_ADMIN);
    $box_smarty->assign('BOX_CONTENT', $box_content);

    $box_smarty->caching = 0;
    $box_smarty->assign('language', $_SESSION['language']);
    $box_admin= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_admin.html');
    $blockinfo['content'] = $data;
    return $blockinfo;
}
?>