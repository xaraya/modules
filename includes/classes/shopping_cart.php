<?php
/* -----------------------------------------------------------------------------------------
   $Id: shopping_cart.php,v 1.4 2003/12/13 17:16:12 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(shopping_cart.php,v 1.32 2003/02/11); www.oscommerce.com
   (c) 2003  nextcommerce (shopping_cart.php,v 1.21 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_create_random_value.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_prid.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_prid.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_products_attribute_price.inc.php');




  class shoppingCart {
    var $contents, $total, $weight, $cartID, $content_type;

    function shoppingCart() {
      $this->reset();
    }

    function restore_contents() {

      if (!isset($_SESSION['customer_id'])) return false;

      // insert current cart contents in database
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $qty = $this->contents[$products_id]['qty'];
          $product_query = new xenQuery("select products_id from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "'");
          if (!$product_query->getrows()) {
            new xenQuery("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . $_SESSION['customer_id'] . "', '" . $products_id . "', '" . $qty . "', '" . date('Ymd') . "')");
            if (isset($this->contents[$products_id]['attributes'])) {
              reset($this->contents[$products_id]['attributes']);
              while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                new xenQuery("insert into " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id) values ('" . $_SESSION['customer_id'] . "', '" . $products_id . "', '" . $option . "', '" . $value . "')");
              }
            }
          } else {
            new xenQuery("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . $qty . "' where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "'");
          }
        }
      }

      // reset per-session cart contents, but not the database contents
      $this->reset(false);

      $products_query = new xenQuery("select products_id, customers_basket_quantity from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $_SESSION['customer_id'] . "'");
      $q = new xenQuery();
      $q->run();
      while ($products = $q->output()) {
        $this->contents[$products['products_id']] = array('qty' => $products['customers_basket_quantity']);
        // attributes
        $attributes_query = new xenQuery("select products_options_id, products_options_value_id from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products['products_id'] . "'");
      $q = new xenQuery();
      $q->run();
        while ($attributes = $q->output()) {
          $this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
        }
      }

      $this->cleanup();
    }

    function reset($reset_database = false) {

      $this->contents = array();
      $this->total = 0;
      $this->weight = 0;
      $this->content_type = false;

      if (isset($_SESSION['customer_id']) && ($reset_database == true)) {
        new xenQuery("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $_SESSION['customer_id'] . "'");
        new xenQuery("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . $_SESSION['customer_id'] . "'");
      }

      unset($this->cartID);
      if (isset($_SESSION['cartID'])) unset($_SESSION['cartID']);
    }

    function add_cart($products_id, $qty = '1', $attributes = '', $notify = true) {
      global $new_products_id_in_cart;

      $products_id = xtc_get_uprid($products_id, $attributes);
      if ($notify == true) {
        $_SESSION['new_products_id_in_cart'] = $products_id;
      }

      if ($this->in_cart($products_id)) {
        $this->update_quantity($products_id, $qty, $attributes);
      } else {
        $this->contents[] = array($products_id);
        $this->contents[$products_id] = array('qty' => $qty);
        // insert into database
        if (isset($_SESSION['customer_id'])) new xenQuery("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . $_SESSION['customer_id'] . "', '" . $products_id . "', '" . $qty . "', '" . date('Ymd') . "')");

        if (is_array($attributes)) {
          reset($attributes);
          while (list($option, $value) = each($attributes)) {
            $this->contents[$products_id]['attributes'][$option] = $value;
            // insert into database
            if (isset($_SESSION['customer_id'])) new xenQuery("insert into " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id) values ('" . $_SESSION['customer_id'] . "', '" . $products_id . "', '" . $option . "', '" . $value . "')");
          }
        }
      }
      $this->cleanup();

      // assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

    function update_quantity($products_id, $quantity = '', $attributes = '') {

      if (empty($quantity)) return true; // nothing needs to be updated if theres no quantity, so we return true..

      $this->contents[$products_id] = array('qty' => $quantity);
      // update database
      if (isset($_SESSION['customer_id'])) new xenQuery("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . $quantity . "' where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "'");

      if (is_array($attributes)) {
        reset($attributes);
        while (list($option, $value) = each($attributes)) {
          $this->contents[$products_id]['attributes'][$option] = $value;
          // update database
          if (isset($_SESSION['customer_id'])) new xenQuery("update " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " set products_options_value_id = '" . $value . "' where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "' and products_options_id = '" . $option . "'");
        }
      }
    }

    function cleanup() {

      reset($this->contents);
      while (list($key,) = each($this->contents)) {
        if ($this->contents[$key]['qty'] < 1) {
          unset($this->contents[$key]);
          // remove from database
          if (xtc_session_is_registered('customer_id')) {
            new xenQuery("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $key . "'");
            new xenQuery("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $key . "'");
          }
        }
      }
    }

    function count_contents() {  // get total number of items in cart
      $total_items = 0;
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $total_items += $this->get_quantity($products_id);
        }
      }

      return $total_items;
    }

    function get_quantity($products_id) {
      if (isset($this->contents[$products_id])) {
        return $this->contents[$products_id]['qty'];
      } else {
        return 0;
      }
    }


    function in_cart($products_id) {
      if (isset($this->contents[$products_id])) {
        return true;
      } else {
        return false;
      }
    }

    function remove($products_id) {

      unset($this->contents[$products_id]);
      // remove from database
      if (xtc_session_is_registered('customer_id')) {
        new xenQuery("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "'");
        new xenQuery("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . $_SESSION['customer_id'] . "' and products_id = '" . $products_id . "'");
      }

      // assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

    function remove_all() {
      $this->reset();
    }

    function get_product_id_list() {
      $product_id_list = '';
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $product_id_list .= ', ' . $products_id;
        }
      }

      return substr($product_id_list, 2);
    }

    function calculate() {
      $this->total = 0;
      $this->weight = 0;
      if (!is_array($this->contents)) return 0;

      reset($this->contents);
      while (list($products_id, ) = each($this->contents)) {
       $qty = $this->contents[$products_id]['qty'];



        // products price
        $product_query = new xenQuery("select products_id, products_price, products_discount_allowed, products_tax_class_id, products_weight from " . TABLE_PRODUCTS . " where products_id='" . xtc_get_prid($products_id) . "'");
      $q = new xenQuery();
      $q->run();
        if ($product = $q->output()) {
          $products_price=xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$product['products_id'],'price_special' =>$price_special=0,'quantity' =>$quantity=$qty));
          $this->total += $products_price;
          $this->weight += ($qty * $product['products_weight']);
        }

        // attributes price
        if (isset($this->contents[$products_id]['attributes'])) {
          reset($this->contents[$products_id]['attributes']);
          while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
            $attribute_price_query = new xenQuery("select pd.products_tax_class_id, p.options_values_price, p.price_prefix, p.options_values_weight, p.weight_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " p, " . TABLE_PRODUCTS . " pd where p.products_id = '" . $product['products_id'] . "' and p.options_id = '" . $option . "' and pd.products_id = p.products_id and p.options_values_id = '" . $value . "'");
      $q = new xenQuery();
      $q->run();
            $attribute_price = $q->output();

            $attribute_weight=$attribute_price['options_values_weight'];
            if ($attribute_price['weight_prefix'] == '+') {
              $this->weight += ($qty * $attribute_price['options_values_weight']);
            } else {
              $this->weight -= ($qty * $attribute_price['options_values_weight']);
            }

            if ($attribute_price['price_prefix'] == '+') {
              $this->total += xtc_get_products_attribute_price($attribute_price['options_values_price'], $tax_class=$attribute_price['products_tax_class_id'], $price_special=0, $quantity=$qty, $prefix=$attribute_price['price_prefix']);
            } else {
              $this->total -= xtc_get_products_attribute_price($attribute_price['options_values_price'], $tax_class=$attribute_price['products_tax_class_id'], $price_special=0, $quantity=$qty, $prefix=$attribute_price['price_prefix']);
            }
          }
        }
      }
      if ($_SESSION['customers_status']['customers_status_ot_discount_flag'] != 0) {
        $this->total -= $this->total/100*$_SESSION['customers_status']['customers_status_ot_discount'];
      }

    }

    function attributes_price($products_id) {
      if (isset($this->contents[$products_id]['attributes'])) {
        reset($this->contents[$products_id]['attributes']);
        while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
          $attribute_price_query = new xenQuery("select pd.products_tax_class_id, p.options_values_price, p.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " p, " . TABLE_PRODUCTS . " pd where p.products_id = '" . $products_id . "' and p.options_id = '" . $option . "' and pd.products_id = p.products_id and p.options_values_id = '" . $value . "'");
      $q = new xenQuery();
      $q->run();
          $attribute_price = $q->output();
          if ($attribute_price['price_prefix'] == '+') {
            $attributes_price += xtc_get_products_attribute_price($attribute_price['options_values_price'], $tax_class=$attribute_price['products_tax_class_id'], $price_special=0, $quantity=1, $prefix=$attribute_price['price_prefix']);
          } else {
            $attributes_price -= xtc_get_products_attribute_price($attribute_price['options_values_price'], $tax_class=$attribute_price['products_tax_class_id'], $price_special=0, $quantity=1, $prefix=$attribute_price['price_prefix']);
          }
        }
      }

      return $attributes_price;
    }

    function get_products() {
      if (!is_array($this->contents)) return false;

      $products_array = array();
      reset($this->contents);
      while (list($products_id, ) = each($this->contents)) {
        $products_query = new xenQuery("select p.products_id, pd.products_name,p.products_image, p.products_model, p.products_price, p.products_discount_allowed, p.products_weight, p.products_tax_class_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id='" . xtc_get_prid($products_id) . "' and pd.products_id = p.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      $q->run();
        if ($products = $q->output()) {
          $prid = $products['products_id'];
          $products_price = xarModAPIFunc('commerce','user','get_products_price',array('products_id' =>$products['products_id'],'price_special' =>$price_special=0,'quantity' =>$quantity=1));

          $products_array[] = array('id' => $products_id,
                                    'name' => $products['products_name'],
                                    'model' => $products['products_model'],
                                    'image' => $products['products_image'],
                                    'price' => $products_price,
//                                    'discount_allowed' => $max_product_discount,
                                    'quantity' => $this->contents[$products_id]['qty'],
                                    'weight' => $products['products_weight'],
                                    'final_price' => ($products_price + $this->attributes_price($products_id)),
//                                   'final_price' => ($products_price + $this->attributes_price($products_id)),
                                    'tax_class_id' => $products['products_tax_class_id'],
                                    'attributes' => $this->contents[$products_id]['attributes']);
        }
      }

      return $products_array;
    }

    function show_total() {
      $this->calculate();

      return $this->total;
    }

    function show_weight() {
      $this->calculate();

      return $this->weight;
    }

    function generate_cart_id($length = 5) {
      return xtc_create_random_value($length, 'digits');
    }

    function get_content_type() {
      $this->content_type = false;

      if ( (DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0) ) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          if (isset($this->contents[$products_id]['attributes'])) {
            reset($this->contents[$products_id]['attributes']);
            while (list(, $value) = each($this->contents[$products_id]['attributes'])) {
              $virtual_check_query = new xenQuery("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad where pa.products_id = '" . $products_id . "' and pa.options_values_id = '" . $value . "' and pa.products_attributes_id = pad.products_attributes_id");
      $q = new xenQuery();
      $q->run();
              $virtual_check = $q->output();

              if ($virtual_check['total'] > 0) {
                switch ($this->content_type) {
                  case 'physical':
                    $this->content_type = 'mixed';
                    return $this->content_type;
                    break;

                  default:
                    $this->content_type = 'virtual';
                    break;
                }
              } else {
                switch ($this->content_type) {
                  case 'virtual':
                    $this->content_type = 'mixed';
                    return $this->content_type;
                    break;

                  default:
                    $this->content_type = 'physical';
                    break;
                }
              }
            }
          } else {
            switch ($this->content_type) {
              case 'virtual':
                $this->content_type = 'mixed';
                return $this->content_type;
                break;

              default:
                $this->content_type = 'physical';
                break;
            }
          }
        }
      } else {
        $this->content_type = 'physical';
      }

      return $this->content_type;
    }

    function unserialize($broken) {
      for(reset($broken);$kv=each($broken);) {
        $key=$kv['key'];
        if (gettype($this->$key) != "user function")
        $this->$key=$kv['value'];
      }
    }

  }
?>