  <?php
/* -----------------------------------------------------------------------------------------
   $Id: product_attributes.php,v 1.10 2003/12/30 09:02:31 fanta2k Exp $

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
//$module_smarty=new Smarty;
$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');

    $products_attributes_query = new xenQuery("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$_GET['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      $q->run();
    $products_attributes = $q->output();
    if ($products_attributes['total'] > 0) {
      $products_options_name_query = new xenQuery("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$_GET['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . $_SESSION['languages_id'] . "' order by popt.products_options_name");

        $row = 0;
    $col = 0;
    $products_options_data=array();
      $q = new xenQuery();
      $q->run();
      while ($products_options_name = $q->output()) {
        $selected = 0;
        $products_options_array = array();

    $products_options_data[$row]=array(
                    'NAME'=>$products_options_name['products_options_name'],
                    'ID' => $products_options_name['products_options_id'],
                    'DATA' =>'');
        $products_options_query = new xenQuery("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix,pa.attributes_stock, pa.attributes_model from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int)$_GET['products_id'] . "' and pa.options_id = '" . $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . $_SESSION['languages_id'] . "' order by pov.products_options_values_name");
        $col = 0;
      $q = new xenQuery();
      $q->run();
        while ($products_options = $q->output()) {
          $products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
          if ($products_options['options_values_price'] != '0') {
                 $products_options_array[sizeof($products_options_array)-1]['text'] .=  ' '.$products_options['price_prefix'].' '.xtc_get_products_attribute_price($products_options['options_values_price'], $tax_class=$product_info['products_tax_class_id'],$price_special=0,$quantity=1,$prefix=$products_options['price_prefix']).' '.$_SESSION['currency'] ;
          }
          $price='';
          if ($products_options['options_values_price']!='0.00') {
          $price = xtc_format_price(xtc_get_products_attribute_price($products_options['options_values_price'], $tax_class=$product_info['products_tax_class_id'],$price_special=0,$quantity=1,$prefix=$products_options['price_prefix']),1,0,1);
          }
          $products_options_data[$row]['DATA'][$col]=array(
                                    'ID' => $products_options['products_options_values_id'],
                                    'TEXT' =>$products_options['products_options_values_name'],
                                    'PRICE' =>$price,
                                    'PREFIX' =>$products_options['price_prefix']);

        $col++;
        }
      $row++;
      }

    }
  // template query
  $template_query=new xenQuery("SELECT
                                options_template
                                FROM ".TABLE_PRODUCTS."
                                WHERE products_id='".$_GET['products_id']."'");
      $q = new xenQuery();
      $q->run();
  $template_data=$q->output();
  if ($template_data['options_template']=='' or $template_data['options_template']=='default') {
          $files=array();
 if ($dir= opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/')){
 while  (($file = readdir($dir)) !==false) {
        if (is_file( DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/'.$file) and ($file !="index.html")){
        $files[]=array(
                        'id' => $file,
                        'text' => $file);
        }//if
        } // while
        closedir($dir);
 }
  $template_data['options_template']=$files[0]['id'];
  }

  $module_smarty->assign('language', $_SESSION['language']);
  $module_smarty->assign('options',$products_options_data);
  // set cache ID
  if (USE_CACHE=='false') {
  $module_smarty->caching = 0;
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/product_options/'.$template_data['options_template']);
  } else {
  $module_smarty->caching = 1;
  $module_smarty->cache_lifetime=CACHE_LIFETIME;
  $module_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_GET['products_id'].$_SESSION['language'].$_SESSION['customers_status']['customers_status_name'];
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/product_options/'.$template_data['options_template'],$cache_id);
  }
  $info_smarty->assign('MODULE_product_options',$module);

 ?>