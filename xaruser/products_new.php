<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//   Third Party contributions:
//   Enable_Disable_Categories 1.3            Autor: Mikel Williams | mikel@ladykatcostumes.com
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

               // create smarty elements
//  $smarty = new Smarty;
  // include boxes
  require(DIR_WS_INCLUDES.'boxes.php');
  // include needed function
  require_once(DIR_FS_INC . 'xtc_get_short_description.inc.php');


  $breadcrumb->add(NAVBAR_TITLE_product_NEW, xarModURL('products','user','product_new');

 require(DIR_WS_INCLUDES . 'header.php');



  $product_new_array = array();

  $product_new_query_raw = "select DISTINCT p.product_id, pd.product_name, p.product_image, p.product_price, p.product_tax_class_id, IF(s.status, s.specials_new_product_price, NULL) as specials_new_product_price, p.product_date_added, m.manufacturers_name from " . TABLE_PRODUCTS . " p, " . TABLE_CATEGORIES . " c, " . TABLE_product_TO_CATEGORIES . " p2c left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id left join " . TABLE_product_DESCRIPTION . " pd on p.product_id = pd.product_id and pd.language_id = '" . $_SESSION['languages_id'] . "' left join " . TABLE_SPECIALS . " s on p.product_id = s.product_id where c.categories_status=1 and p.product_id = p2c.product_id and c.categories_id = p2c.categories_id and product_status = '1' order by p.product_date_added DESC, pd.product_name";

  $product_new_split = new splitPageResults($product_new_query_raw, $_GET['page'], MAX_DISPLAY_product_NEW);

  if (($product_new_split->number_of_rows > 0)) {
   $data['NAVIGATION_BAR'] = '
   <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText">'.$product_new_split->display_count(TEXT_DISPLAY_NUMBER_OF_product_NEW).'</td>
            <td align="right" class="smallText">'.TEXT_RESULT_PAGE . ' ' . $product_new_split->display_links(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array('page', 'info', 'x', 'y'))).'</td>
          </tr>
        </table>';

  }

$module_content='';
  if ($product_new_split->number_of_rows > 0) {
    $product_new_query = new xenQuery($product_new_split->sql_query);
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($product_new = $q->output()) {
      if (xarModAPIFunc('commerce','user','not_null',array('arg' => $product_new['specials_new_product_price']))) {
        $product_price = xarModAPIFunc('commerce','user','get_product_price',array('product_id' =>$product_new['product_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1));
      } else {
        $product_price = xarModAPIFunc('commerce','user','get_product_price',array('product_id' =>$product_new['product_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1));
      }

    $product_new['product_name'] = xarModAPIFunc('commerce','user','get_product_name',array('id' =>$product_new['product_id']));
    $product_new['product_short_description'] = xtc_get_short_description($product_new['product_id']);
    $module_content[]=array(
                            'product_NAME' => $product_new['product_name'],
                            'product_DESCRIPTION' => $product_new['product_short_description'],
                            'product_PRICE' => xarModAPIFunc('commerce','user','get_product_price',array('product_id' =>$product_new['product_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1)),
                            'product_LINK' => xarModURL('commerce','user','product_info', 'product_id=' . $product_new['product_id']),
                            'product_IMAGE' => xarTplGetImage('product_images/thumbnail_images/' . $product_new['product_image']),
                            'BUTTON_BUY_NOW'=>'<a href="' . xarModURL('commerce','user',(basename($PHP_SELF), xtc_get_all_get_params(array('action')) . 'action=buy_now&BUYproduct_id=' . $product_new['product_id'], 'NONSSL') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_buy_now.gif'),
        'alt' => TEXT_BUY . $product_new['product_name'] . TEXT_NOW));



    }
  } else {

$data['ERROR'] = TEXT_NO_NEW_PRODUCTS;

  }

    $data['language'] = $_SESSION['language'];
  $smarty->caching = 0;
  $data['module_content'] = $module_content;
  return data;
?>