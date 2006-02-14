<?php
// ----------------------------------------------------------------------
// Copyright (C) 2006: Marc Lutolf (marcinmilan@xaraya.com) & Fabien Bel (fab@webu.fr)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce(shopping_cart.php,v 1.32 2003/02/11); www.oscommerce.com
//  (c) 2003  nextcommerce (shopping_cart.php,v 1.21 2003/08/17); www.nextcommerce.org
//   Third Party contributions:
//   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist
// ----------------------------------------------------------------------

include_once 'modules/xen/xarclasses/xenquery.php';

/**
* Class which manage a basket when a user is logged
**/
    class shoppingCart
    {
        var $contents, $total, $weight;
        var $userid;
        var $prefix; 

        /**
        * Constructor
        **/
        function shoppingCart()
        {
            $this->reset(false);
            $this->userid = xarSessionGetVar('uid');
            $this->prefix= xarDBGetSiteTablePrefix();
            $this->restore_contents();

        }

        /**
        * restore the content of the basket
        **/
        function restore_contents()
        {
            if (!$this->userid) return 0;

            // insert current cart contents in database
            if ($this->contents) {
                reset($this->contents);
                // List all products in cart
                while (list($products_id, ) = each($this->contents)) {
                $qty = $this->contents[$products_id]['qty'];
                
                $this->add_cart($products_id, $qty);
                
                $q = new xenQuery('SELECT', $this->prefix . '_carts_customers_basket',array('products_id'));
                $q->eq('customers_id', $this->userid);
                $q->eq('products_id', $products_id);
                if(!$q->run()) return;

                if ($q->output() != array()) {
                    $q = new xenQuery('INSERT', $this->prefix . '_carts_customers_basket');
                    $q->addfield('customers_id', $this->userid);
                    $q->addfield('products_id', $products_id);
                    $q->addfield('customers_basket_quantity', $qty);
                    $q->addfield('customers_basket_date_added', date('Ymd'));
                    if(!$q->run()) return;

                   /* if ($this->contents[$products_id]['attributes']) {
                        reset($this->contents[$products_id]['attributes']);
                        while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                            $q = new xenQuery('INSERT', $this->prefix . '_carts_customers_basket_attributes']);
                            $q->addfield('customers_id', $this->userid);
                            $q->addfield('products_id', $products_id);
                            $q->addfield('products_options_id', $option);
                            $q->addfield('products_options_value_id', value);
                            if(!$q->run()) return;
                        }
                    }*/
                }
                else {
                    $q = new xenQuery('UPDATE', $this->prefix . '_carts_customers_basket');
                    $q->addfield('customers_basket_quantity', $qty);
                    $q->eq('customers_id', $this->userid);
                    $q->eq('products_id', $products_id);
                    if(!$q->run()) return;
                }
            }
        }


            $this->load_basket();         
            
        }
        
        /** 
        * Load the basket with infos contains in the database
        **/
        function load_basket(){
            
        // reset per-session cart contents, but not the database contents
        $this->reset(FALSE);

        $q = new xenQuery('SELECT', $this->prefix . '_carts_customers_basket',array('products_id','customers_basket_quantity'));
        $q->eq('customers_id', $this->userid);
        if(!$q->run()) return;
        
        $result = $q->output();
        $this->cleanup();
                

        foreach($result as $products) {

            $this->contents[$products['products_id']] = array('qty' => $products['customers_basket_quantity']);
            // attributes
           /* $q = new xenQuery('SELECT', $this->prefix . '_carts_customers_basket_attributes']);
            $q->addfields(array('products_options_id', 'products_options_value_id'));
            $q->eq('customers_id', $this->userid);
            $q->eq('products_id', $products_id);
            if(!$q->run()) return;

            while ($attributes = $q->output()) {
                  $this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
                }*/
            }
        }

        /**
        * Empty the cart
        * @param reset_database boolean which notices if we empty the database too
        **/
        function reset($reset_database = false)
        {
            $this->contents = array();
            $this->total = 0;

            if ($this->userid && $reset_database) {
                $q = new xenQuery('DELETE', $this->prefix . '_carts_customers_basket');
                $q->eq('customers_id', $this->userid);
                if(!$q->run()) return;
                $q = new xenQuery('DELETE', $this->prefix . '_carts_customers_basket_attributes');
                $q->eq('customers_id', $this->userid);
                if(!$q->run()) return;
            }
        }

        /**
        * Add the products in the basket
        * @param $products_id the product
        * @param $quantity quantity wanted
        **/
        function add_cart($products_id, $qty = '', $attributes = '')
        {

           /* $products_id = xtc_get_uprid($products_id, $attributes);*/
           
           if ($qty == '') $qty = '1'; // if no quantity is supplied, then add '1' to the customers basket
           

            if ($this->in_cart($products_id)) {
                $previous_qty = $this->get_quantity($products_id);
                $this->update_quantity($products_id, $previous_qty + 1, $attributes);
            }
            else {              
                $this->contents[] = array($products_id);
                $this->contents[$products_id] = array('qty' => $qty);
                // insert into database
                if ($this->userid) {
                    $q = new xenQuery('INSERT', $this->prefix . '_carts_customers_basket');
                    $q->addfield('customers_id', $this->userid);
                    $q->addfield('products_id', $products_id);
                    $q->addfield('customers_basket_quantity', $qty);
                    $q->addfield('customers_basket_date_added', date('Ymd'));
                    if(!$q->run()) return;
                }
                /*if (is_array($attributes)) {
                    reset($attributes);
                    while (list($option, $value) = each($attributes)) {
                        $this->contents[$products_id]['attributes'][$option] = $value;
                        // insert into database
                        if ($this->userid) {
                            $q = new xenQuery('INSERT', $this->prefix . '_carts_customers_basket_attributes']);
                            $q->addfield('customers_id', $this->userid);
                            $q->addfield('products_id', $products_id);
                            $q->addfield('products_options_id', $option);
                            $q->addfield('products_options_value_id', value);
                            if(!$q->run()) return;
                        }
                    }
                }
                $_SESSION['new_products_id_in_cart'] = $products_id;*/
            }
            
        }

        /**
        * Update the quantity of a product
        * @param $products_id the product
        * @param $quantity quantity wanted
        * @return boolean that says if the update is a success or not
        **/
        function update_quantity($products_id, $quantity = '', $attributes = '')
        {
            
            if ($quantity == '' || $quantity < 0) return true; // nothing needs to be updated if theres no quantity, so we return true..
            
            //We get the stock
            $stock = $this->in_stock($products_id);
                
                //if the stock is sufficient
                if ($stock >= $quantity){
                    
                    $this->contents[$products_id] = array('qty' => $quantity);
                    // update database
                    if ($this->userid) {
                        $q = new xenQuery('UPDATE', $this->prefix . '_carts_customers_basket');
                        $q->addfield('customers_basket_quantity', $quantity);
                        $q->eq('customers_id', $this->userid);
                        $q->eq('products_id', $products_id);
                        if(!$q->run()) return false;
                    }else{
                        return false;   
                    }
                    
                }
                else{
                        return false;   
                }

            /*
            if (is_array($attributes)) {
                reset($attributes);
                while (list($option, $value) = each($attributes)) {
                    $this->contents[$products_id]['attributes'][$option] = $value;
                    // update database
                    if ($this->userid) {
                        $q = new xenQuery('UPDATE', $this->prefix . '_carts_customers_basket_attributes']);
                        $q->addfield('products_options_value_id', value);
                        $q->eq('customers_id', $this->userid);
                        $q->eq('products_id', $products_id);
                        $q->eq('products_options_id', $option);
                        if(!$q->run()) return;
                    }
                }
            }*/
            $this->cleanup();
           return true;
        }

        /**
        * Clean the basket
        **/
        function cleanup()
        {
            reset($this->contents);
            while (list($key,) = each($this->contents)) {
                if ($this->contents[$key]['qty'] < 1) {
                    unset($this->contents[$key]);
                    // remove from database
                    if ($this->userid) {
                        $q = new xenQuery('DELETE', $this->prefix . '_carts_customers_basket');
                        $q->eq('customers_id', $this->userid);
                        $q->eq('products_id', $key);
                        if(!$q->run()) return;
                        
                        $q = new xenQuery('DELETE', $this->prefix . '_carts_customers_basket_attributes');
                        $q->eq('customers_id', $this->userid);
                        $q->eq('products_id', $key);
                        if(!$q->run()) return;
                    }
                }
            }
        }

        /**
        * @return the number of differents types of products
        **/
        function count_contents()
        {  // get total number of items in cart
            $total_items = 0;
            if (is_array($this->contents)) {
                reset($this->contents);
                while (list($products_id, ) = each($this->contents)) {
                    $total_items += $this->get_quantity($products_id);
                }
            }
            return $total_items;
        }

        /**
        * @param $products_id the product
        * @return the quantity of the products in the basket
        **/
        function get_quantity($products_id)
        {
            if (isset($this->contents[$products_id]['qty'])) {
                return $this->contents[$products_id]['qty'];
            }
            else {
                return 0;
            }
        }

        /**
        * @param $products_id the product
        * @return boolean true if the product is in the cart and false else
        **/
        function in_cart($products_id)
        {
            if (isset($this->contents[$products_id])) {
                return true;
            }
            else {
                return false;
            }
        }
        
        
         /**
        * Give the quantity avalaibale in stocks
        * @param $products_id the product 
        * @return stock 
        **/
        function in_stock($products_id)
        {
            //The query
            $this->prefix = xarDBGetSiteTablePrefix();
            $q = new xenQuery('SELECT', $this->prefix . '_products_products', array('products_quantity'));
            $q->eq('products_id', $products_id);
            $q->run();
            $result = $q->output();
            
            if ($result[0]){
                return $result[0]['products_quantity'];  
            }
            else{
                return 0;
            }
        }

        /**
        * Remove a product
        * @param $products_id to remove
        **/
        function remove($products_id)
        {
            unset($this->contents[$products_id]);
            // remove from database
            if ($this->userid) {
                $q = new xenQuery('DELETE', $this->prefix . '_carts_customers_basket');
                $q->eq('customers_id', $this->userid);
                $q->eq('products_id', $products_id);
                if(!$q->run()) return;
                /*
                $q = new xenQuery('DELETE', $this->prefix . '_carts_customers_basket_attributes']);
                $q->eq('customers_id', $this->userid);
                $q->eq('products_id', $products_id);
                if(!$q->run()) return;*/
            }
        }

        /**
        * Delete all
        **/
        function remove_all()
        {
              $this->reset();
        }

        /**
        * Get products list
        * @return an array with all products_id
        **/
        function get_product_id_list()
        {
            //Array which will contains products_id
            $products = array();
            $i = 0;
            $product_id_list = '';
            if (is_array($this->contents)) {
                reset($this->contents);
                while (list($products_id, ) = each($this->contents)) {
                    
                    $product_id_list .= ', ' . $products_id;
                }
            }
            return substr($product_id_list, 2);
        }

        /**
        * Calcul the total price and weight
        **/
        function calculate()
        {
            $this->total = 0;
            $this->weight = 0;
           
           //Get all products of the basket
           $products = $this->get_products();
           
           foreach ($products as $prod){
                $this->total = $this->total + $prod['sum'];
                $this->weight= $this->weight + $prod['weight'];
           }
            /*if (!is_array($this->contents)) return 0;

            reset($this->contents);
            while (list($products_id, ) = each($this->contents)) {
                $qty = $this->contents[$products_id]['qty'];

                // products price
                $q = new xenQuery('SELECT', $this->prefix . '_carts_customers_basket');
                $q->addwfields(array('products_id', 'products_price', 'products_tax_class_id', 'products_weight'));
                $q->eq('products_id', xtc_get_prid($products_id));
                if(!$q->run()) return;

                if ($q->output() != array()) {
                    $product = $q->output();
                    $prid = $product['products_id'];
                    $products_tax = xtc_get_tax_rate($product['products_tax_class_id']);
                    $products_price = $product['products_price'];
                    $products_weight = $product['products_weight'];

                    $q = new xenQuery('SELECT', $this->prefix . '_carts_specials'],array('specials_new_products_price'));
                    $q->eq('products_id', $prid);
                    $q->eq('status', 1);
                    if(!$q->run()) return;

                    if ($q->output() != array()) {
                        $specials = $q->output();
                        $products_price = $specials['specials_new_products_price'];
                    }
                    $this->total += xarModAPIFunc('carts','user','add_tax',array('price' =>$products_price,'tax' =>$products_tax)) * $qty;
                    $this->weight += ($qty * $products_weight);
                }

                // attributes price
                if ($this->contents[$products_id]['attributes']) {
                    reset($this->contents[$products_id]['attributes']);
                    include_once 'modules/xen/xarclasses/xenquery.php';
                    $xartables = xarDBGetTables();
                    while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                        $q = new xenQuery('SELECT', $this->prefix . '_carts_products_attributes']);
                        $q->addfields(array('options_values_price', 'price_prefix'));
                        $q->eq(products_id, $prid);
                        $q->eq(options_id, $option);
                        $q->eq(options_values_id, $value);
                        if(!$q->run()) return;

                        $attribute_price = $q->output();
                        if ($attribute_price['price_prefix'] == '+') {
                            $this->total += $qty * xarModAPIFunc('commerce','user','add_tax',array('price' =>$attribute_price['options_values_price'],'tax' =>$products_tax));
                        }
                        else {
                            $this->total -= $qty * xarModAPIFunc('commerce','user','add_tax',array('price' =>$attribute_price['options_values_price'],'tax' =>$products_tax));
                        }
                    }
                }*/
            }
        

        function attributes_price($products_id)
        {
           /* if ($this->contents[$products_id]['attributes']) {
                reset($this->contents[$products_id]['attributes']);
                while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                    $q = new xenQuery('SELECT', $this->prefix . '_carts_products_attributes']);
                    $q->addfields(array('options_values_price', 'price_prefix'));
                    $q->eq(products_id, $products_id);
                    $q->eq(options_id, $option);
                    $q->eq(options_values_id, $value);
                    if(!$q->run()) return;

                    $attribute_price = $q->output();
                    if ($attribute_price['price_prefix'] == '+') {
                        $attributes_price += $attribute_price['options_values_price'];
                    }
                    else {
                        $attributes_price -= $attribute_price['options_values_price'];
                    }
                }
            }
            return $attributes_price;*/
        }

        /**
        * return products with information that you need for the basket
        * @return $products_array an array which contain all infos
        **/
        function get_products()
        {
            if (!is_array($this->contents)) return 0;
            $products_array = array();
            $i = 0;
            reset($this->contents);

            $languages = xarModAPIFunc('commerce','user','get_languages');
            $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
            $language = $localeinfo['lang'] . "_" . $localeinfo['country'];
            $currentlang = xarModAPIFunc('commerce','user','get_language',array('locale' => $language));
            $language_id = $currentlang['id'];

            while (list($products_id, ) = each($this->contents)) {/*
                $q = new xenQuery('SELECT');
                $q->addtable($this->prefix . '_commerce_products'], 'p');
                $q->addtable($this->prefix . '_commerce_products_description'], 'pd');
                $q->addfields(array('p.products_id', 'pd.products_name', 'p.products_model', 'p.products_price', 'p.products_weight', 'p.products_tax_class_id'));
                $q->eq('p.products_id', xtc_get_prid($products_id));
                $q->join('pd.products_id', 'p.products_id');
                $q->eq('pd.language_id', $currentlang['id']);
                if(!$q->run()) return;

                $products = $q->output();
                if ($products != array()) {
                    $prid = $products['products_id'];
                    $products_price = $products['products_price'];

                    $q = new xenQuery('SELECT',$this->prefix . '_commerce_specials']);
                    $q->addfield('specials_new_products_price');
                    $q->eq('products_id', $prid);
                    $q->eq('status', 1);
                    if(!$q->run()) return;

                    if ($q->output() != array()) {
                        $specials = $q->output();
                        $products_price = $specials['specials_new_products_price'];
                    }

                    $products_array[] = array('id' => $products_id,
                                            'name' => $products['products_name'],
                                            'model' => $products['products_model'],
                                            'price' => $products_price,
                                            'quantity' => $this->contents[$products_id]['qty'],
                                            'weight' => $products['products_weight'],
                                            'final_price' => ($products_price + $this->attributes_price($products_id)),
                                            'tax_class_id' => $products['products_tax_class_id'],
                                            'attributes' => $this->contents[$products_id]['attributes']);
                }
            */
                       
               //We take info in the database
               
               //Prepare the query
                $q = new xenQuery('SELECT', $this->prefix . '_products_products', array('products_id', 'products_model', 'products_price', 'products_weight' ));
               
               //Find the product
               $q->eq('products_id', $products_id);
               
               //execute the query
               $q->run();
               
               $result = $q->output();

                if($result[0])
                {
                    $products_array[$i]['id'] = $result[0]['products_id'];
                    $products_array[$i]['model'] = $result[0]['products_model'];
                    $products_array[$i]['price'] = $result[0]['products_price'];
                    $products_array[$i]['weight'] = $result[0]['products_weight'];
                    $products_array[$i]['quantity'] = $this->get_quantity($products_id);
                    $products_array[$i]['sum'] = $result[0]['products_price'] * $products_array[$i]['quantity'];
                    $i++;                    
                }
                             
           }     
                                  
            return $products_array;
        }

        /**
        * @return the total price
        **/
        function show_total()
        {           
            return $this->total;
        }

        /**
        * @return the total weight
        **/
        function show_weight()
        {           
            return $this->weight;
        }

        function unserialize($broken)
        {
            for(reset($broken);$kv=each($broken);) {
                $key=$kv['key'];
                if (gettype($this->$key)!="user function")
                    $this->$key=$kv['value'];
            }
        }
    }
   
?>