<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//   Third Party contribution:
//   Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
//   New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
//   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
//   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

function commerce_admin_categories()
{

   Released under the GNU General Public License
   --------------------------------------------------------------*/


  include ('includes/classes/image_manipulator.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  if ($_GET['function']) {
    switch ($_GET['function']) {
      case 'delete':
        new xenQuery("DELETE FROM personal_offers_by_customers_status_" . $_GET['statusID'] . " WHERE product_id = '" . $_GET['pID'] . "' AND quantity = '" . $_GET['quantity'] . "'");
    break;
    }
    xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&action=new_product&pID=' . $_GET['pID']));
  }
  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'setflag':
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if ($_GET['pID']) {
            xtc_set_product_status($_GET['pID'], $_GET['flag']);
          }
          if ($_GET['cID']) {
            xtc_set_categories_status($_GET['cID'], $_GET['flag']);
          }
        }

        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath']));
        break;

      case 'new_category':
      case 'edit_category':
        if (ALLOW_CATEGORY_DESCRIPTIONS == 'true')
        $_GET['action']=$_GET['action'] . '_ACD';
        break;

      case 'insert_category':
      case 'update_category':
        if (($_POST['edit_x']) || ($_POST['edit_y'])) {
          $_GET['action'] = 'edit_category_ACD';
        } else {
        $categories_id = xtc_db_prepare_input($_POST['categories_id']);
        if ($categories_id == '') {
        $categories_id = xtc_db_prepare_input($_GET['cID']);
        }
        $sort_order = xtc_db_prepare_input($_POST['sort_order']);
        $categories_status = xtc_db_prepare_input($_POST['categories_status']);
        $q->addfield('sort_order',$sort_order, 'categories_status' => $categories_status);

        if ($_GET['action'] == 'insert_category') {
          $insert_sql_data = array('parent_id' => $current_category_id,
                                   'date_added' => 'now()');
          $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
          xtc_db_perform(TABLE_CATEGORIES, $sql_data_array);
          $categories_id = xtc_db_insert_id();
        } elseif ($_GET['action'] == 'update_category') {
          $update_sql_data = array('last_modified' => 'now()');
          $sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
          xtc_db_perform(TABLE_CATEGORIES, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\'');
        }

        $languages = xtc_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $categories_name_array = $_POST['categories_name'];
          $language_id = $languages[$i]['id'];
          $q->addfield('categories_name',xtc_db_prepare_input($categories_name_array[$language_id]));
          if (ALLOW_CATEGORY_DESCRIPTIONS == 'true') {
              $q->addfield('categories_name',xtc_db_prepare_input($_POST['categories_name'][$language_id]));
                                      $q->addfield('categories_heading_title',xtc_db_prepare_input($_POST['categories_heading_title'][$language_id]));
                                      $q->addfield('categories_description',xtc_db_prepare_input($_POST['categories_description'][$language_id]));
                                      $q->addfield('categories_meta_title',xtc_db_prepare_input($_POST['categories_meta_title'][$language_id]));
                                      $q->addfield('categories_meta_description',xtc_db_prepare_input($_POST['categories_meta_description'][$language_id]));
                                      $q->addfield('categories_meta_keywords',xtc_db_prepare_input($_POST['categories_meta_keywords'][$language_id]));
            }

          if ($_GET['action'] == 'insert_category') {
            $insert_sql_data = array('categories_id' => $categories_id,
                                     'language_id' => $languages[$i]['id']);
            $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
            xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array);
          } elseif ($_GET['action'] == 'update_category') {
            xtc_db_perform(TABLE_CATEGORIES_DESCRIPTION, $sql_data_array, 'update', 'categories_id = \'' . $categories_id . '\' and language_id = \'' . $languages[$i]['id'] . '\'');
          }
        }

            if ($categories_image = new upload('categories_image', DIR_FS_CATALOG_IMAGES)) {
            new xenQuery("update " . TABLE_CATEGORIES . " set categories_image = '" . xtc_db_input($categories_image->filename) . "' where categories_id = '" . (int)$categories_id . "'");
            }

          xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&cID=' . $categories_id));
        }
        break;


      case 'delete_category_confirm':
        if ($_POST['categories_id']) {
          $categories_id = xtc_db_prepare_input($_POST['categories_id']);

          $categories = xtc_get_category_tree($categories_id, '', '0', '', true);
          $products = array();
          $product_delete = array();

          for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            $product_ids_query = new xenQuery("select product_id from " . TABLE_product_TO_CATEGORIES . " where categories_id = '" . $categories[$i]['id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
            while ($product_ids = $q->output()) {
              $products[$product_ids['product_id']]['categories'][] = $categories[$i]['id'];
            }
          }

          reset($products);
          while (list($key, $value) = each($products)) {
            $category_ids = '';
            for ($i = 0, $n = sizeof($value['categories']); $i < $n; $i++) {
              $category_ids .= '\'' . $value['categories'][$i] . '\', ';
            }
            $category_ids = substr($category_ids, 0, -2);

            $check_query = new xenQuery("select count(*) as total from " . TABLE_product_TO_CATEGORIES . " where product_id = '" . $key . "' and categories_id not in (" . $category_ids . ")");
      $q = new xenQuery();
      if(!$q->run()) return;
            $check = $q->output();
            if ($check['total'] < '1') {
              $product_delete[$key] = $key;
            }
          }

          // Removing categories can be a lengthy process
          @xtc_set_time_limit(0);
          for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            xtc_remove_category($categories[$i]['id']);
          }

          reset($product_delete);
          while (list($key) = each($product_delete)) {
            xtc_remove_product($key);
          }
        }

        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath));
        break;
      case 'delete_product_confirm':
        if ( ($_POST['product_id']) && (is_array($_POST['products_categories'])) ) {
          $product_id = xtc_db_prepare_input($_POST['product_id']);
          $products_categories = $_POST['products_categories'];

          for ($i = 0, $n = sizeof($products_categories); $i < $n; $i++) {
            new xenQuery("delete from " . TABLE_product_TO_CATEGORIES . " where product_id = '" . xtc_db_input($product_id) . "' and categories_id = '" . xtc_db_input($products_categories[$i]) . "'");
          }

          $products_categories_query = new xenQuery("select count(*) as total from " . TABLE_product_TO_CATEGORIES . " where product_id = '" . xtc_db_input($product_id) . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
          $products_categories = $q->output();

          if ($products_categories['total'] == '0') {
            xtc_remove_product($product_id);
          }
        }

        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath));
        break;
      case 'move_category_confirm':
        if ( ($_POST['categories_id']) && ($_POST['categories_id'] != $_POST['move_to_category_id']) ) {
          $categories_id = xtc_db_prepare_input($_POST['categories_id']);
          $new_parent_id = xtc_db_prepare_input($_POST['move_to_category_id']);
          new xenQuery("update " . TABLE_CATEGORIES . " set parent_id = '" . xtc_db_input($new_parent_id) . "', last_modified = now() where categories_id = '" . xtc_db_input($categories_id) . "'");
        }

        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id . '&cID=' . $categories_id));
        break;
      case 'move_product_confirm':
        $product_id = xtc_db_prepare_input($_POST['product_id']);
        $new_parent_id = xtc_db_prepare_input($_POST['move_to_category_id']);

        $duplicate_check_query = new xenQuery("select count(*) as total from " . TABLE_product_TO_CATEGORIES . " where product_id = '" . xtc_db_input($product_id) . "' and categories_id = '" . xtc_db_input($new_parent_id) . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
        $duplicate_check = $q->output();
        if ($duplicate_check['total'] < 1) new xenQuery("update " . TABLE_product_TO_CATEGORIES . " set categories_id = '" . xtc_db_input($new_parent_id) . "' where product_id = '" . xtc_db_input($product_id) . "' and categories_id = '" . $current_category_id . "'");

        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $new_parent_id . '&pID=' . $product_id));
        break;
      case 'insert_product':
      case 'update_product':

// START IN-SOLUTION Zurückberechung des Nettopreises falls der Bruttopreis übergeben wurde
        if (PRICE_IS_BRUTTO=='true' && $_POST['product_price']){
                $tax_query = new xenQuery("select tax_rate from " . TABLE_TAX_RATES . " where tax_class_id = '".$_POST['product_tax_class_id']."' ");
      $q = new xenQuery();
      if(!$q->run()) return;
                $tax = $q->output();
                $_POST['product_price'] = ($_POST['product_price']/($tax['tax_rate']+100)*100);
         }
        // END IN-SOLUTION



        if ( ($_POST['edit_x']) || ($_POST['edit_y']) ) {
          $_GET['action'] = 'new_product';
        } else {
          $product_id = xtc_db_prepare_input($_GET['pID']);
          $product_date_available = xtc_db_prepare_input($_POST['product_date_available']);

          $product_date_available = (date('Y-m-d') < $product_date_available) ? $product_date_available : 'null';

          $q->addfield('product_quantity',xtc_db_prepare_input($_POST['product_quantity']),
                                  $q->addfield('product_model',xtc_db_prepare_input($_POST['product_model']),
                                  $q->addfield($q->addfield('product_price',xtc_db_prepare_input($_POST['product_price']),
                                  $q->addfield('product_discount_allowed',xtc_db_prepare_input($_POST['product_discount_allowed']),
                                  $q->addfield('product_date_available',$product_date_available,
                                  $q->addfield('product_weight',xtc_db_prepare_input($_POST['product_weight']),
                                  $q->addfield('product_status',xtc_db_prepare_input($_POST['product_status']),
                                  $q->addfield('product_tax_class_id',xtc_db_prepare_input($_POST['product_tax_class_id']),
                                  $q->addfield('product_template',xtc_db_prepare_input($_POST['info_template']),
                                  $q->addfield('options_template',xtc_db_prepare_input($_POST['options_template']),
                                  $q->addfield('manufacturers_id',xtc_db_prepare_input($_POST['manufacturers_id']));


          if ($product_image = new upload('product_image', DIR_FS_CATALOG_ORIGINAL_IMAGES, '777', '', true)) {
          $product_image_name = $product_image->filename;
          $q->addfield('product_image',xtc_db_prepare_input($product_image_name));

   require(DIR_WS_INCLUDES . 'product_thumbnail_images.php');
   require(DIR_WS_INCLUDES . 'product_info_images.php');
   require(DIR_WS_INCLUDES . 'product_popup_images.php');

          } else {
          $product_image_name = $_POST['product_previous_image'];
          }

          if (isset($_POST['product_image']) && xarModAPIFunc('commerce','user','not_null',array('arg' => $_POST['product_image'])) && ($_POST['product_image'] != 'none')) {
            $q->addfield('product_image',xtc_db_prepare_input($_POST['product_image']));
          }

          if ($_GET['action'] == 'insert_product') {
            $insert_sql_data = array('product_date_added' => 'now()');
            $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);
            xtc_db_perform(TABLE_PRODUCTS, $sql_data_array);
            $product_id = xtc_db_insert_id();
            new xenQuery("insert into " . TABLE_product_TO_CATEGORIES . " (product_id, categories_id) values ('" . $product_id . "', '" . $current_category_id . "')");
          } elseif ($_GET['action'] == 'update_product') {
            $update_sql_data = array('product_last_modified' => 'now()');
            $sql_data_array = xtc_array_merge($sql_data_array, $update_sql_data);
            xtc_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', 'product_id = \'' . xtc_db_input($product_id) . '\'');
          }

          $languages = xtc_get_languages();
          // Here we go, lets write Group prices into db
          // start
          $i = 0;
          $group_query = new xenQuery("SELECT customers_status_id  FROM " . TABLE_CUSTOMERS_STATUS . " WHERE language_id = '" . $_SESSION['languages_id'] . "' AND customers_status_id != '0'");
      $q = new xenQuery();
      if(!$q->run()) return;
          while ($group_values = $q->output()) {
            // load data into array
            $i++;
            $group_data[$i] = array('STATUS_ID' => $group_values['customers_status_id']);
          }
          for ($col = 0, $n = sizeof($group_data); $col < $n+1; $col++) {
            if ($group_data[$col]['STATUS_ID'] != '') {
              $personal_price = xtc_db_prepare_input($_POST['product_price_' . $group_data[$col]['STATUS_ID']]);
              if ($personal_price == '' or $personal_price=='0.0000') {
              $personal_price = '0.00';
              } else {
            if (PRICE_IS_BRUTTO=='true'){
                $tax_query = new xenQuery("select tax_rate from " . TABLE_TAX_RATES . " where tax_class_id = '" . $_POST['product_tax_class_id'] . "' ");
      $q = new xenQuery();
      if(!$q->run()) return;
                $tax = $q->output();
                $personal_price= ($personal_price/($tax['tax_rate']+100)*100);
          }
          $personal_price=xtc_round($personal_price,PRICE_PRECISION);
}

              new xenQuery("UPDATE personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . " SET personal_offer = '" . $personal_price . "' WHERE product_id = '" . $product_id . "' AND quantity = '1'");
            }
          }
          // end
          // ok, lets check write new staffelpreis into db (if there is one)
          $i = 0;
          $group_query = new xenQuery("SELECT customers_status_id FROM " . TABLE_CUSTOMERS_STATUS . " WHERE language_id = '" . $_SESSION['languages_id'] . "' AND customers_status_id != '0'");
      $q = new xenQuery();
      if(!$q->run()) return;
          while ($group_values = $q->output()) {
            // load data into array
            $i++;
            $group_data[$i]=array('STATUS_ID' => $group_values['customers_status_id']);
          }
          for ($col = 0, $n = sizeof($group_data); $col < $n+1; $col++) {
            if ($group_data[$col]['STATUS_ID'] != '') {
              $quantity = xtc_db_prepare_input($_POST['product_quantity_staffel_' . $group_data[$col]['STATUS_ID']]);
              $staffelpreis = xtc_db_prepare_input($_POST['product_price_staffel_' . $group_data[$col]['STATUS_ID']]);
            if (PRICE_IS_BRUTTO=='true'){
                $tax_query = new xenQuery("select tax_rate from " . TABLE_TAX_RATES . " where tax_class_id = '" . $_POST['product_tax_class_id'] . "' ");
      $q = new xenQuery();
      if(!$q->run()) return;
                $tax = $q->output();
                $staffelpreis= ($staffelpreis/($tax['tax_rate']+100)*100);
          }
          $staffelpreis=xtc_round($staffelpreis,PRICE_PRECISION);
              if ($staffelpreis!='' && $quantity!='') {
                new xenQuery("INSERT INTO personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . " (price_id, product_id, quantity, personal_offer) VALUES ('', '" . $product_id . "', '" . $quantity . "', '" . $staffelpreis . "')");
              }
            }
          }
          for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
            $q->addfield('product_name',xtc_db_prepare_input($_POST['product_name'][$language_id]));
                                    $q->addfield('product_description',xtc_db_prepare_input($_POST['product_description_'.$language_id]));
                                    $q->addfield('product_short_description',xtc_db_prepare_input($_POST['product_short_description_'.$language_id]));
                                    $q->addfield('product_url',xtc_db_prepare_input($_POST['product_url'][$language_id]));
                                    $q->addfield('product_meta_title',xtc_db_prepare_input($_POST['product_meta_title'][$language_id]));
                                    $q->addfield('product_meta_description',xtc_db_prepare_input($_POST['product_meta_description'][$language_id]));
                                    $q->addfield('product_meta_keywords',xtc_db_prepare_input($_POST['product_meta_keywords'][$language_id]));

            if ($_GET['action'] == 'insert_product') {
              $insert_sql_data = array('product_id' => $product_id,
                                       'language_id' => $language_id);
              $sql_data_array = xtc_array_merge($sql_data_array, $insert_sql_data);

              xtc_db_perform(TABLE_product_DESCRIPTION, $sql_data_array);
            } elseif ($_GET['action'] == 'update_product') {
              xtc_db_perform(TABLE_product_DESCRIPTION, $sql_data_array, 'update', 'product_id = \'' . xtc_db_input($product_id) . '\' and language_id = \'' . $language_id . '\'');
            }
          }

          xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $product_id));
        }
        break;
      case 'copy_to_confirm':
        if ( (xarModAPIFunc('commerce','user','not_null',array('arg' => $_POST['product_id']))) && (xarModAPIFunc('commerce','user','not_null',array('arg' => $_POST['categories_id']))) ) {
          $product_id = xtc_db_prepare_input($_POST['product_id']);
          $categories_id = xtc_db_prepare_input($_POST['categories_id']);

          if ($_POST['copy_as'] == 'link') {
            if ($_POST['categories_id'] != $current_category_id) {
              $check_query = new xenQuery("select count(*) as total from " . TABLE_product_TO_CATEGORIES . " where product_id = '" . xtc_db_input($product_id) . "' and categories_id = '" . xtc_db_input($categories_id) . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
              $check = $q->output();
              if ($check['total'] < '1') {
                new xenQuery("insert into " . TABLE_product_TO_CATEGORIES . " (product_id, categories_id) values ('" . xtc_db_input($product_id) . "', '" . xtc_db_input($categories_id) . "')");
              }
            } else {
              $messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_CATEGORY, 'error');
            }
          } elseif ($_POST['copy_as'] == 'duplicate') {
            $product_query = new xenQuery("select product_quantity, product_model, product_image, product_price, product_discount_allowed, product_date_available, product_weight, product_tax_class_id, manufacturers_id from " . TABLE_PRODUCTS . " where product_id = '" . xtc_db_input($product_id) . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
            $product = $q->output();
            new xenQuery("insert into " . TABLE_PRODUCTS . " (product_quantity, product_model,product_image, product_price, product_discount_allowed, product_date_added, product_date_available, product_weight, product_status, product_tax_class_id, manufacturers_id) values ('" . $product['product_quantity'] . "', '" . $product['product_model'] . "', '" . $product['product_image'] . "', '" . $product['product_price'] . "', '" . $product['product_discount_allowed'] . "',  now(), '" . $product['product_date_available'] . "', '" . $product['product_weight'] . "', '0', '" . $product['product_tax_class_id'] . "', '" . $product['manufacturers_id'] . "')");
            $dup_product_id = xtc_db_insert_id();

            $description_query = new xenQuery("select language_id, product_name, product_description,product_short_description, product_meta_title, product_meta_description, product_meta_keywords, product_url from " . TABLE_product_DESCRIPTION . " where product_id = '" . xtc_db_input($product_id) . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
            while ($description = $q->output()) {
              new xenQuery("insert into " . TABLE_product_DESCRIPTION . " (product_id, language_id, product_name, product_description, product_short_description, product_meta_title, product_meta_description, product_meta_keywords, product_url, product_viewed) values ('" . $dup_product_id . "', '" . $description['language_id'] . "', '" . addslashes($description['product_name']) . "', '" . addslashes($description['product_description']) . "','" . addslashes($description['product_short_description']) . "', '" . $description['product_url'] . "', '0')");
            }

            new xenQuery("insert into " . TABLE_product_TO_CATEGORIES . " (product_id, categories_id) values ('" . $dup_product_id . "', '" . xtc_db_input($categories_id) . "')");
            $product_id = $dup_product_id;
          }
  }

        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $categories_id . '&pID=' . $product_id));
        break;
    }
  }

  // check if the catalog image directory exists
  if (is_dir(DIR_FS_CATALOG_IMAGES)) {
    if (!is_writeable(DIR_FS_CATALOG_IMAGES)) $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
  } else {
    $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
  }

<div id="spiffycalendar" class="text"></div>

  //----- new_category / edit_category (when ALLOW_CATEGORY_DESCRIPTIONS is 'true') -----
  if ($_GET['action'] == 'new_category_ACD' || $_GET['action'] == 'edit_category_ACD') {
  include('new_categorie.php');
  //----- new_category_preview (active when ALLOW_CATEGORY_DESCRIPTIONS is 'true') -----
  } elseif ($_GET['action'] == 'new_category_preview') {
  // removed
  } elseif ($_GET['action'] == 'new_product') {
  include('new_product.php');
  } elseif ($_GET['action'] == 'new_product_preview') {
  // preview removed
  } else {
  include('categories_view.php');
  }
}
?>