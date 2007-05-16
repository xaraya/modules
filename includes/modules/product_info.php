  <?php
/* -----------------------------------------------------------------------------------------
   $Id: product_info.php,v 1.20 2003/12/31 17:39:30 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(product_info.php,v 1.94 2003/05/04); www.oscommerce.com
   (c) 2003      nextcommerce (product_info.php,v 1.46 2003/08/25); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist
   New Attribute Manager v4b                            Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Cross-Sell (X-Sell) Admin 1                          Autor: Joshua Dechant (dreamscape)
   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
// $info_smarty = new Smarty;
 $info_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');



  $product_info_query = new xenQuery("select p.products_discount_allowed,p.products_id, pd.products_name, pd.products_description, p.products_model, p.products_quantity,p.products_weight, p.products_image,p.products_status,p.products_ordered, pd.products_url, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id,p.product_template,p.product_template from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and pd.products_id = p.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "'");
  if (!$product_info_query->getrows()) { // product not found in database
  $info_smarty->assign('TEXT_NOT_FOUND','<tr><td class="main"><br>'. TEXT_PRODUCT_NOT_FOUND.'</td></tr><tr><td align="right"><br><a href="'. xarModURL('commerce','user','default').'">'. xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_continue.gif'),'alt' => IMAGE_BUTTON_CONTINUE)
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_continue.gif'),'alt' => IMAGE_BUTTON_CONTINUE).'</a></td></tr>');

  } else {
    new xenQuery("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int)$_GET['products_id'] . "' and language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
    $product_info = $q->output();

    $products_price=xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$product_info['products_id'],'price_special' =>$price_special=1,'quantity' =>$quantity=1));

    // check if customer is allowed to add to cart
    if ($_SESSION['customers_status']['customers_status_show_price']!='0') {
    $info_smarty->assign('ADD_CART_BUTTON',xtc_draw_input_field('products_qty', '1','size="3"') . ' ' . xtc_draw_hidden_field('products_id', $product_info['products_id']) .
    <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_in_cart.gif')#" border="0" alt=IMAGE_BUTTON_IN_CART>.
    );
    }

    $info_smarty->assign('FORM_ACTION',xarModURL('commerce','user','product_info', xtc_get_all_get_params(array('action')) . 'action=add_product'));
    $info_smarty->assign('PRODUCTS_PRICE',$products_price);
    $info_smarty->assign('PRODUCTS_ID',$product_info['products_id']);
    $info_smarty->assign('PRODUCTS_NAME',$product_info['products_name']);
    $info_smarty->assign('PRODUCTS_MODEL',$product_info['products_model']);
    $info_smarty->assign('PRODUCTS_QUANTITY',$product_info['products_quantity']);
    $info_smarty->assign('PRODUCTS_WEIGHT',$product_info['products_weight']);
    $info_smarty->assign('PRODUCTS_STATUS',$product_info['products_status']);
    $info_smarty->assign('PRODUCTS_ORDERED',$product_info['products_ordered']);
    $info_smarty->assign('PRODUCTS_PRINT', '<img src="'.DIR_WS_ICONS.'print.gif"  style="cursor:hand" onClick="javascript:window.open(\''.xarModURL('commerce','user',(FILENAME_PRINT_PRODUCT_INFO,'products_id='.$_GET['products_id']).'\', \'popup\', \'toolbar=0, width=640, height=600\')">');
    $info_smarty->assign('PRODUCTS_DESCRIPTION',stripslashes($product_info['products_description']));
    $info_smarty->assign('PRODUCTS_IMAGE',xarModURL('commerce','user',(DIR_WS_INFO_IMAGES . $product_info['products_image']));
    $info_smarty->assign('PRODUCTS_POPUP_LINK','javascript:popupWindow(\'' . xarModURL('commerce','user',(FILENAME_POPUP_IMAGE, 'pID=' . $product_info['products_id']) . '\')');

        if ($_SESSION['customers_status']['customers_status_public'] == 1 && $_SESSION['customers_status']['customers_status_discount'] != '0.00') {
      $discount = $_SESSION['customers_status']['customers_status_discount'];
      if ($product_info['products_discount_allowed'] < $_SESSION['customers_status']['customers_status_discount']) $discount = $product_info['products_discount_allowed'];
      if ($discount != '0.00' ) {
        $info_smarty->assign('PRODUCTS_DISCOUNT',$discount . '%');
};

    }

include(DIR_WS_MODULES . 'product_attributes.php');
include(DIR_WS_MODULES.'product_reviews.php');


    if (xarModAPIFunc('commerce','user','not_null',array('arg' => $product_info['products_url']))) {
    $info_smarty->assign('PRODUCTS_URL',sprintf(TEXT_MORE_INFORMATION, xarModURL('commerce','user',(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($product_info['products_url']), 'NONSSL', true, false)));

    }

    if ($product_info['products_date_available'] > date('Y-m-d H:i:s')) {
        $info_smarty->assign('PRODUCTS_DATE_AVIABLE',sprintf(TEXT_DATE_AVAILABLE, xarModAPIFunc('commerce','user','date_long',array('raw_date' =>$product_info['products_date_available']))));


    } else {
        $info_smarty->assign('PRODUCTS_ADDED',sprintf(TEXT_DATE_ADDED, xarModAPIFunc('commerce','user','date_long',array('raw_date' =>$product_info['products_date_added']))));

    }

 if ($_SESSION['customers_status']['customers_status_graduated_prices'] == 1) {
 include(DIR_WS_MODULES.FILENAME_GRADUATED_PRICE);
 }
 include(DIR_WS_MODULES . FILENAME_PRODUCTS_MEDIA);
 include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);
  }
  if ($product_info['product_template']=='' or $product_info['product_template']=='default') {
          $files=array();
          if ($dir= opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/')){
          while  (($file = readdir($dir)) !==false) {
        if (is_file( DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'.$file) and ($file !="index.html")){
        $files[]=array(
                        'id' => $file,
                        'text' => $file);
        }//if
        } // while
        closedir($dir);
        }
  $product_info['product_template']=$files[0]['id'];
  }
  $info_smarty->assign('language', $_SESSION['language']);
  // set cache ID
  if (USE_CACHE=='false') {
  $info_smarty->caching = 0;
  $product_info= $info_smarty->fetch(CURRENT_TEMPLATE.'/module/product_info/'.$product_info['product_template']);
  } else {
  $info_smarty->caching = 1;
  $info_smarty->cache_lifetime=CACHE_LIFETIME;
  $info_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_GET['products_id'].$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'].$_SESSION['currency'];
  $product_info= $info_smarty->fetch(CURRENT_TEMPLATE.'/module/product_info/'.$product_info['product_template'],$cache_id);
  }


  $smarty->assign('main_content',$product_info);

  ?>