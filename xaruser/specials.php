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

//    $smarty = new Smarty;
  // include boxes
  require(DIR_WS_INCLUDES.'boxes.php');

  require_once(DIR_FS_INC . 'xtc_get_short_description.inc.php');

  $breadcrumb->add(NAVBAR_TITLE_SPECIALS, xarModURL('commerce','user','specials');

 require(DIR_WS_INCLUDES . 'header.php');

  $specials_query_raw = "select p.product_id, pd.product_name, p.product_price, p.product_tax_class_id, p.product_image, s.specials_new_product_price from " . TABLE_PRODUCTS . " p, " . TABLE_product_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s where p.product_status = '1' and s.product_id = p.product_id and p.product_id = pd.product_id and pd.language_id = '" . $_SESSION['languages_id'] . "' and s.status = '1' order by s.specials_date_added DESC";
  $specials_split = new splitPageResults($specials_query_raw, $_GET['page'], MAX_DISPLAY_SPECIAL_PRODUCTS);



$module_content='';
    $row = 0;
    $specials_query = new xenQuery($specials_split->sql_query);
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($specials = $q->output()) {
      $row++;
      $product_price = xarModAPIFunc('commerce','user','get_product_price',array('product_id' =>$specials['product_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1));
      $image='';
      if ($specials['product_image']!='') {
      $image= xarTplGetImage('product_images/thumbnail_images/' . $specials['product_image']);
      }
      $module_content[]=array(
                            'product_ID' => $specials['product_id'],
                            'product_NAME' => $specials['product_name'],
                            'product_PRICE' => $product_price,
                            'product_LINK' => xarModURL('commerce','user','product_info', 'product_id=' . $specials['product_id']),
                            'product_IMAGE'=> $image,
                            'product_SHORT_DESCRIPTION' => xtc_get_short_description($new_products['product_id']));

    }

if (($specials_split->number_of_rows > 0)) {
$data['NAVBAR'] = '
<table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText">'.$specials_split->display_count(TEXT_DISPLAY_NUMBER_OF_SPECIALS).'</td>
            <td align="right" class="smallText">'.TEXT_RESULT_PAGE . ' ' . $specials_split->display_links(MAX_DISPLAY_PAGE_LINKS, xtc_get_all_get_params(array('page', 'info', 'x', 'y'))).'</td>
          </tr>
        </table>';
}


  $data['language'] =  $_SESSION['language'];
  $data['module_content'] = $module_content;
  $smarty->caching = 0;
  return data;

  ?>