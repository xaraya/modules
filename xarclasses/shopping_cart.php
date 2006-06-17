<?php
// ----------------------------------------------------------------------
// Copyright (C) 2006: Marc Lutolf (mfl@netspan.ch) & Fabien Bel (fab@webu.fr)
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

	class shoppingCart
	{
		var $contents, $total, $weight;
		var $userid;
		var $carttable;

		/**
		* Constructor
		**/
		function __construct($args=null)
		{
			$this->reset(false);
			$this->userid = xarSessionGetVar('uid');
			$this->carttable = xarDBGetSiteTablePrefix() . '_carts_basket';
			$this->restore_contents();
		}

		/**
		* restore the content of the basket
		**/
		function restore_contents()
		{
			if (empty($this->userid)) return 0;

			// insert current cart contents in database
			if ($this->contents) {
				reset($this->contents);
				// List all products in cart
                while (list($item_id, ) = each($this->contents)) {
                $quantity = $this->contents[$item_id]['quantity'];

				$this->add_cart($item_id, $quantity);

				$q = new xenQuery('SELECT', $this->carttable,'item_id');
				$q->eq('customer_id', $this->userid);
				$q->eq('item_id', $item_id);
				if(!$q->run()) return;

				if ($q->output() != array()) {
					$q = new xenQuery('INSERT', $this->carttable);
					$q->addfield('customer_id', $this->userid);
					$q->addfield('item_id', $item_id);
					$q->addfield('quantity', $quantity);
					$q->addfield('date_added', time());
					if(!$q->run()) return;

				   /* if ($this->contents[$item_id]['attributes']) {
						reset($this->contents[$item_id]['attributes']);
						while (list($option, $value) = each($this->contents[$item_id]['attributes'])) {
							$q = new xenQuery('INSERT', $this->prefix . '_carts_customers_basket_attributes']);
							$q->addfield('customer_id', $this->userid);
							$q->addfield('item_id', $item_id);
							$q->addfield('products_options_id', $option);
							$q->addfield('products_options_value_id', value);
							if(!$q->run()) return;
						}
					}*/
				} else {
					$q = new xenQuery('UPDATE', $this->carttable);
					$q->addfield('quantity', $quantity);
					$q->eq('customer_id', $this->userid);
					$q->eq('item_id', $item_id);
					if(!$q->run()) return;
				}
			}
		}
		$this->load_basket();

		}

		/**
		* Load the basket with infos contained in the database
		**/
		function load_basket(){

		// reset per-session cart contents, but not the database contents
		$this->reset(FALSE);

		$q = new xenQuery('SELECT', $this->carttable,array('item_id','quantity'));
		$q->eq('customer_id', $this->userid);
		if(!$q->run()) return;

		$result = $q->output();
		$this->cleanup();


		foreach($result as $item) {

			$this->contents[$item['item_id']] = array('quantity' => $item['quantity']);
			// attributes
		   /* $q = new xenQuery('SELECT', $this->prefix . '_carts_customers_basket_attributes']);
			$q->addfields(array('option_id', 'option_value_id'));
			$q->eq('customer_id', $this->userid);
			$q->eq('item_id', $item_id);
			if(!$q->run()) return;

			while ($attributes = $q->output()) {
				  $this->contents[$products['item_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
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
				$q = new xenQuery('DELETE', $this->carttable);
				$q->eq('customer_id', $this->userid);
				if(!$q->run()) return;
				$q = new xenQuery('DELETE', $this->prefix . '_carts_customers_basket_attributes');
				$q->eq('customer_id', $this->userid);
				if(!$q->run()) return;
			}
		}

		/**
		* Add the products in the basket
		* @param $item_id the product
		* @param $quantity quantity wanted
		**/
		function add_cart($item_id, $quantity = '', $attributes = '')
		{

		   /* $item_id = xtc_get_uprid($item_id, $attributes);*/

		   if ($quantity == '') $quantity = '1'; // if no quantity is supplied, then add '1' to the customers basket


			if ($this->in_cart($item_id)) {
				$previous_quantity = $this->get_quantity($item_id);
				$this->update_quantity($item_id, $previous_quantity + 1, $attributes);
			}
			else {
				$this->contents[] = array($item_id);
				$this->contents[$item_id] = array('quantity' => $quantity);
				// insert into database
				if ($this->userid) {
					$q = new xenQuery('INSERT', $this->carttable);
					$q->addfield('customer_id', $this->userid);
					$q->addfield('item_id', $item_id);
					$q->addfield('quantity', $quantity);
					$q->addfield('date_added', date('Ymd'));
					if(!$q->run()) return;
				}
				/*if (is_array($attributes)) {
					reset($attributes);
					while (list($option, $value) = each($attributes)) {
						$this->contents[$item_id]['attributes'][$option] = $value;
						// insert into database
						if ($this->userid) {
							$q = new xenQuery('INSERT', $this->prefix . '_carts_customers_basket_attributes']);
							$q->addfield('customer_id', $this->userid);
							$q->addfield('item_id', $item_id);
							$q->addfield('products_options_id', $option);
							$q->addfield('products_options_value_id', value);
							if(!$q->run()) return;
						}
					}
				}
				$_SESSION['new_item_id_in_cart'] = $item_id;*/
			}

		}

		/**
		* Update the quantity of a product
		* @param $item_id the product
		* @param $quantity quantity wanted
		* @return boolean that says if the update is a success or not
		**/
		function update_quantity($item_id, $quantity = '', $attributes = '')
		{

			if ($quantity == '' || $quantity < 0) return true; // nothing needs to be updated if theres no quantity, so we return true..

			//We get the stock
			$stock = $this->in_stock($item_id);

				//if the stock is sufficient
				if ($stock >= $quantity){

					$this->contents[$item_id] = array('quantity' => $quantity);
					// update database
					if ($this->userid) {
						$q = new xenQuery('UPDATE', $this->carttable);
						$q->addfield('quantity', $quantity);
						$q->eq('customer_id', $this->userid);
						$q->eq('item_id', $item_id);
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
					$this->contents[$item_id]['attributes'][$option] = $value;
					// update database
					if ($this->userid) {
						$q = new xenQuery('UPDATE', $this->prefix . '_carts_customers_basket_attributes']);
						$q->addfield('products_options_value_id', value);
						$q->eq('customer_id', $this->userid);
						$q->eq('item_id', $item_id);
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
			while (list($item_id,) = each($this->contents)) {
				if ($this->contents[$item_id]['quantity'] < 1) {
					unset($this->contents[$item_id]);
					// remove from database
					if ($this->userid) {
						$q = new xenQuery('DELETE', $this->carttable);
						$q->eq('customer_id', $this->userid);
						$q->eq('item_id', $item_id);
						if(!$q->run()) return;

						$q = new xenQuery('DELETE', $this->prefix . '_carts_customers_basket_attributes');
						$q->eq('customer_id', $this->userid);
						$q->eq('item_id', $item_id);
						if(!$q->run()) return;
					}
				}
			}
		}

		/**
		* @return the number of differents types of items
		**/
		function count_contents()
		{  // get total number of items in cart
			$total_items = 0;
			if (is_array($this->contents)) {
				reset($this->contents);
				while (list($item_id, ) = each($this->contents)) {
					$total_items += $this->get_quantity($item_id);
				}
			}
			return $total_items;
		}

		/**
		* @param $item_id the product
		* @return the quantity of the products in the basket
		**/
		function get_quantity($item_id)
		{
			if (isset($this->contents[$item_id]['quantity'])) {
				return $this->contents[$item_id]['quantity'];
			}
			else {
				return 0;
			}
		}

		/**
		* @param $item_id the product
		* @return boolean true if the product is in the cart and false else
		**/
		function in_cart($item_id)
		{
			if (isset($this->contents[$item_id])) {
				return true;
			}
			else {
				return false;
			}
		}


		 /**
		* Give the quantity avalaibale in stocks
		* @param $item_id the product
		* @return stock
		**/
		function in_stock($item_id)
		{
			//The query
			$this->prefix = xarDBGetSiteTablePrefix();
			$q = new xenQuery('SELECT', $this->prefix . '_products_products', 'products_quantity');
			$q->eq('item_id', $item_id);
			$q->run();
			$result = $q->row();

			if (!empty($result)){
				return $result['products_quantity'];
			}
			else{
				return 0;
			}
		}

		/**
		* Remove a product
		* @param $item_id to remove
		**/
		function remove($item_id)
		{
			unset($this->contents[$item_id]);
			// remove from database
			if ($this->userid) {
				$q = new xenQuery('DELETE', $this->carttable);
				$q->eq('customer_id', $this->userid);
				$q->eq('item_id', $item_id);
				if(!$q->run()) return;
				/*
				$q = new xenQuery('DELETE', $this->prefix . '_carts_customers_basket_attributes']);
				$q->eq('customer_id', $this->userid);
				$q->eq('item_id', $item_id);
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
		* @return an array with all item_id
		**/
		function get_item_id_list()
		{
			//Array which will contains item_id
			$products = array();
			$i = 0;
			$item_id_list = '';
			if (is_array($this->contents)) {
				reset($this->contents);
				while (list($item_id, ) = each($this->contents)) {

					$item_id_list .= ', ' . $item_id;
				}
			}
			return substr($item_id_list, 2);
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
			while (list($item_id, ) = each($this->contents)) {
				$quantity = $this->contents[$item_id]['quantity'];

				// products price
				$q = new xenQuery('SELECT', $this->carttable);
				$q->addwfields(array('item_id', 'products_price', 'products_tax_class_id', 'products_weight'));
				$q->eq('item_id', xtc_get_prid($item_id));
				if(!$q->run()) return;

				if ($q->output() != array()) {
					$product = $q->output();
					$prid = $product['item_id'];
					$products_tax = xtc_get_tax_rate($product['products_tax_class_id']);
					$products_price = $product['products_price'];
					$products_weight = $product['products_weight'];

					$q = new xenQuery('SELECT', $this->prefix . '_carts_specials'],array('specials_new_products_price'));
					$q->eq('item_id', $prid);
					$q->eq('status', 1);
					if(!$q->run()) return;

					if ($q->output() != array()) {
						$specials = $q->output();
						$products_price = $specials['specials_new_products_price'];
					}
					$this->total += xarModAPIFunc('carts','user','add_tax',array('price' =>$products_price,'tax' =>$products_tax)) * $quantity;
					$this->weight += ($quantity * $products_weight);
				}

				// attributes price
				if ($this->contents[$item_id]['attributes']) {
					reset($this->contents[$item_id]['attributes']);
					include_once 'modules/xen/xarclasses/xenquery.php';
					$xartables = xarDBGetTables();
					while (list($option, $value) = each($this->contents[$item_id]['attributes'])) {
						$q = new xenQuery('SELECT', $this->prefix . '_carts_products_attributes']);
						$q->addfields(array('options_values_price', 'price_prefix'));
						$q->eq(item_id, $prid);
						$q->eq(options_id, $option);
						$q->eq(options_values_id, $value);
						if(!$q->run()) return;

						$attribute_price = $q->output();
						if ($attribute_price['price_prefix'] == '+') {
							$this->total += $quantity * xarModAPIFunc('commerce','user','add_tax',array('price' =>$attribute_price['options_values_price'],'tax' =>$products_tax));
						}
						else {
							$this->total -= $quantity * xarModAPIFunc('commerce','user','add_tax',array('price' =>$attribute_price['options_values_price'],'tax' =>$products_tax));
						}
					}
				}*/
			}


		function attributes_price($item_id)
		{
		   /* if ($this->contents[$item_id]['attributes']) {
				reset($this->contents[$item_id]['attributes']);
				while (list($option, $value) = each($this->contents[$item_id]['attributes'])) {
					$q = new xenQuery('SELECT', $this->prefix . '_carts_products_attributes']);
					$q->addfields(array('options_values_price', 'price_prefix'));
					$q->eq(item_id, $item_id);
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

			while (list($item_id, ) = each($this->contents)) {/*
				$q = new xenQuery('SELECT');
				$q->addtable($this->prefix . '_commerce_products'], 'p');
				$q->addtable($this->prefix . '_commerce_products_description'], 'pd');
				$q->addfields(array('p.item_id', 'pd.products_name', 'p.products_model', 'p.products_price', 'p.products_weight', 'p.products_tax_class_id'));
				$q->eq('p.item_id', xtc_get_prid($item_id));
				$q->join('pd.item_id', 'p.item_id');
				$q->eq('pd.language_id', $currentlang['id']);
				if(!$q->run()) return;

				$products = $q->output();
				if ($products != array()) {
					$prid = $products['item_id'];
					$products_price = $products['products_price'];

					$q = new xenQuery('SELECT',$this->prefix . '_commerce_specials']);
					$q->addfield('specials_new_products_price');
					$q->eq('item_id', $prid);
					$q->eq('status', 1);
					if(!$q->run()) return;

					if ($q->output() != array()) {
						$specials = $q->output();
						$products_price = $specials['specials_new_products_price'];
					}

					$products_array[] = array('id' => $item_id,
											'name' => $products['products_name'],
											'model' => $products['products_model'],
											'price' => $products_price,
											'quantity' => $this->contents[$item_id]['quantity'],
											'weight' => $products['products_weight'],
											'final_price' => ($products_price + $this->attributes_price($item_id)),
											'tax_class_id' => $products['products_tax_class_id'],
											'attributes' => $this->contents[$item_id]['attributes']);
				}
			*/

			   //We take info in the database

			   //Prepare the query
				$q = new xenQuery('SELECT', $this->prefix . '_products_products', array('item_id', 'products_model', 'products_price', 'products_weight' ));

			   //Find the product
			   $q->eq('item_id', $item_id);

			   //execute the query
			   $q->run();

			   $result = $q->output();

				if($result[0])
				{
					$products_array[$i]['id'] = $result[0]['item_id'];
					$products_array[$i]['model'] = $result[0]['products_model'];
					$products_array[$i]['price'] = $result[0]['products_price'];
					$products_array[$i]['weight'] = $result[0]['products_weight'];
					$products_array[$i]['quantity'] = $this->get_quantity($item_id);
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