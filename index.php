<?php
/* -----------------------------------------------------------------------------------------
   $Id: index.php,v 1.13 2003/12/14 13:11:47 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(default.php,v 1.84 2003/05/07); www.oscommerce.com
   (c) 2003  nextcommerce (default.php,v 1.13 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Enable_Disable_Categories 1.3            Autor: Mikel Williams | mikel@ladykatcostumes.com
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  include( 'includes/application_top.php');

  // create smarty elements
//  $smarty = new Smarty;
  // include boxes
  require(DIR_WS_INCLUDES.'boxes.php');

  // the following cPath references come from application_top.php
  $category_depth = 'top';
  if (isset($cPath) && xarModAPIFunc('commerce','user','not_null',array('arg' => $cPath))) {
    $categories_products_query = new xenQuery("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . $current_category_id . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
    $cateqories_products = $q->output();
    if ($cateqories_products['total'] > 0) {
      $category_depth = 'products'; // display products
    } else {
      $category_parent_query = new xenQuery("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . $current_category_id . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $category_parent = $q->output();
      if ($category_parent['total'] > 0) {
        $category_depth = 'nested'; // navigate through the categories
      } else {
        $category_depth = 'products'; // category has no products, but display the 'no products' message
      }
    }
  }


 require(DIR_WS_INCLUDES . 'header.php');


  include (DIR_WS_MODULES . 'default.php');
  $data['language'] = $_SESSION['language'];

  $smarty->caching = 0;
  $smarty->display(CURRENT_TEMPLATE . '/index.html');
 ?>