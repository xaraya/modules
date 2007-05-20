<?php
/**
 * File: $Id$
 *
 * class xarProducts
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by WebU
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.webu.fr
 *
 * @subpackage Products module
 * @author Bel Fabien fab@webu.fr
*/

//the xenQuery class for queries
sys::import('modules.xen.xarclasses.xenquery');

/**
 * class xarProducts
 * Serve to manage the catalog of the website
 * @author Bel Fabien fab@webu.fr
 */
class xarProducts
{

/*** Attributes: ***/

  // it's all database that we eract

  var $productsTable;

  var $productsNotificationTable;

  var $productsGratuatedTable;

  var $productsAttributesTable;

  var $rolesTable;


  /**
   * The constructor
   * initialise tables
   * @access public
   */
  function xarProducts( )
  {

      $xartable = xarDB::getTables();
      $this->productsTable = $xartable['products_products'];
      $this->productsAttributesTable = $xartable['products_products_attributes'];

      /* TODO LATER
      $this->rolesTable = $xartable['roles'];
      $this->productsNotificationTable = $xartable['products_products_notifications'];
      $this->productsGratuatedTable = $xartable['xar_products_products_graduated_prices'];
      */


  } // end of member function xarProducts

  /**
   * Add a product in the catalog
   * @param products_status
   * @param dateAvailable
   * @param products_sort
   * @param products_model
   * @param fsk18
   * @param shipping_status
   * @param products_quantity
   * @param products_weight
   * @param products_price
   * @param iid itemid to attach the product
   * @param itemtype itemtype to attach the produc
   * @param modid moduleid to attach the produc
   * @access public
   */
  function addProduct($args)
  {

      extract($args);


      if (!isset($products_status)){
       return;
      }

      if (!isset($dateAvailable)){
       return;
      }

      if (!isset($products_sort)){
       return;
      }

      if (!isset($products_model)){
       return;
      }

      if (!isset($fsk18)){
       return;
      }

      if (!isset($shipping_status)){
       return;
      }

      if (!isset($products_quantity)){
       return;
      }

      if (!isset($products_weight)){
       return;
      }

      if (!isset($products_price)){
       return;
      }

      if (!isset($iid)){
       return;
      }

      if (!isset($itemtype)){
       return;
      }

      if (!isset($modid)){
       return;
      }

      // Transform the date in a timestamp format
      $dateTransform = explode('-', $dateAvailable);
      $dateA = mktime(0, 0, 0, $dateTransform[1], $dateTransform[2], $dateTransform[0]);

      //INSERTION in the product table

          $q = new xenQuery('INSERT', $this->productsTable);

           $tablefields = array(
            array('name' => 'products_quantity',      'value' => $products_quantity),
            array('name' => 'products_shippingtime',     'value' => $shipping_status),
            array('name' => 'products_model',    'value' => $products_model),
            array('name' => 'products_sort', 'value' =>  $products_sort),
            array('name' => 'products_price',  'value' => $products_price),
            array('name' => 'products_discount_allowed',  'value' => 0.00),
            array('name' => 'products_date_added',  'value' => time()),
            array('name' => 'products_date_available',  'value' => $dateA),
            array('name' => 'products_weight',  'value' => $products_weight),
            array('name' => 'products_status',  'value' => $products_status),
            array('name' => 'products_fsk18',  'value' => $fsk18),
            array('name' => 'xar_modid',  'value' => $modid),
            array('name' => 'xar_itemtype',  'value' => $itemtype),
            array('name' => 'xar_itemid',  'value' => $iid)
        );

        $q->addfields($tablefields);

        if (!$q->run()) return;

  } // end of member function addProduct

  /**
   * Remove a product in database
   * @param iid itemid to remove the product
   * @param itemtype itemtype to remove the produc
   * @param modid moduleid to remove the produc
   * @access public
   */
  function removeProduct($args)
  {

      extract($args);

      if (!isset($iid)){
       return;
      }

      if (!isset($itemtype)){
       return;
      }

      if (!isset($modid)){
       return;
      }

      $q = new xenQuery('DELETE', $this->productsTable);

      $q->eq('xar_modid', $modid);
      $q->eq('xar_itemtype', $itemtype);
      $q->eq('xar_itemid', $iid);

      if (!$q->run()) return;

  } // end of member function removeProduct

  /**
   * Update the product in data base
   * @param products_id
   * @param products_status
   * @param dateAvailable
   * @param products_sort
   * @param products_model
   * @param fsk18
   * @param shipping_status
   * @param products_quantity
   * @param products_weight
   * @param products_price
   * @access public
   */
  function modifyProduct($args)
  {

      extract($args);
      if (!isset($products_id)){
       return;
      }

      if (!isset($products_status)){
       return;
      }

      if (!isset($dateAvailable)){
       return;
      }

      if (!isset($products_sort)){
       return;
      }

      if (!isset($products_model)){
       return;
      }

      if (!isset($fsk18)){
       return;
      }

      if (!isset($shipping_status)){
       return;
      }

      if (!isset($products_quantity)){
       return;
      }

      if (!isset($products_weight)){
       return;
      }

      if (!isset($products_price)){
       return;
      }


      // Transform the date in a timestamp format
      $dateTransform = explode('-', $dateAvailable);
      $dateA = mktime(0, 0, 0, $dateTransform[1], $dateTransform[2], $dateTransform[0]);

      //UPDATE in the product table

          $q = new xenQuery('UPDATE', $this->productsTable);

           $tablefields = array(
            array('name' => 'products_quantity',      'value' => $products_quantity),
            array('name' => 'products_shippingtime',     'value' => $shipping_status),
            array('name' => 'products_model',    'value' => $products_model),
            array('name' => 'products_sort', 'value' =>  $products_sort),
            array('name' => 'products_price',  'value' => $products_price),
            array('name' => 'products_discount_allowed',  'value' => 0.00),
            array('name' => 'products_date_added',  'value' => time()),
            array('name' => 'products_date_available',  'value' => $dateA),
            array('name' => 'products_weight',  'value' => $products_weight),
            array('name' => 'products_status',  'value' => $products_status),
            array('name' => 'products_fsk18',  'value' => $fsk18)
        );

        $q->eq('products_id', $products_id);
        $q->addfields($tablefields);

        if (!$q->run()) return;
  } // end of member function modifyProduct

  /**
   * Give all infos in product table
   * @param iid itemid
   * @param itemtype
   * @param modid
   * @return infosProduct infos about the product associate with the item
   * @access public
   */
  function getInfosProduct($args)
  {

     extract($args);

     //Select the good product
     $q = new xenQuery('SELECT');
     $q->addtable($this->productsTable, 'products');
     $q->addfields(array( 'products_id',
                          'products_quantity',
                          'products_shippingtime',
                          'products_model',
                          'group_ids',
                          'products_sort',
                          'products_price',
                          'products_discount_allowed',
                          'products_date_added',
                          'products_last_modified',
                          'products_date_available',
                          'products_weight',
                          'products_status',
                          'products_tax_class_id',
                          'manufacturers_id',
                          'products_ordered',
                          'products_fsk18'));

     $q->eq('xar_modid', $modid);
     $q->eq('xar_itemid', $iid);
     $q->eq('xar_itemtype', $itemtype);

     $q->run();

     return $q->output();


  } // end of member function getInfosProduct

  /**
   *
   * @param  id_product    * @param  id_attribute    * @return
   * @access public
   */
  function assignAttribut( $id_product,  $id_attribute )
  {

  } // end of member function assignAttribut

  /**
   *
   * @param  id_products    * @param  id_attribut    * @return
   * @access public
   */
  function removeAttribute( $id_products,  $id_attribut )
  {

  } // end of member function removeAttribute

  /**
   *
   * @param  id of the product    * @param  newPrice    * @param datetime expireDate    * @return
   * @access public
   */
  function addSpecialOffer( $args)
  {

  } // end of member function addSpecialOffer

  /**
   *
   * @param  id    * @return
   * @access public
   */
  function removeSpecialOffer( $id )
  {

  } // end of member function removeSpecialOffer

  /**
   *
   * @return void
   * @access public
   */
  function getAllProducts( )
  {

  } // end of member function getAllProducts





} // end of xarProducts
?>
