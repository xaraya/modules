<?php
/* --------------------------------------------------------------
   $Id: new_product.php,v 1.3 2003/12/19 17:12:14 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.140 2003/03/24); www.oscommerce.com
   (c) 2003  nextcommerce (categories.php,v 1.37 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
   New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

   if ( ($_GET['pID']) && (!$_POST) )
   {
      $product_query = new xenQuery("select p.product_template,p.options_template,pd.products_name, pd.products_description,pd.products_short_description, pd.products_meta_title, pd.products_meta_description, pd.products_meta_keywords, pd.products_url, p.products_id, p.products_quantity, p.products_model, p.products_image, p.products_price, p.products_discount_allowed, p.products_weight, p.products_date_added, p.products_last_modified, date_format(p.products_date_available, '%Y-%m-%d') as products_date_available, p.products_status, p.products_tax_class_id, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . $_GET['pID'] . "' and p.products_id = pd.products_id and pd.language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $product = $q->output();
      $pInfo = new objectInfo($product);
    } elseif ($_POST) {
      $pInfo = new objectInfo($_POST);
      $products_name = $_POST['products_name'];
      $products_description = $_POST['products_description'];
      $products_short_description = $_POST['products_short_description'];
      $products_meta_title = $_POST['products_meta_title'];
      $products_meta_description = $_POST['products_meta_description'];
      $products_meta_keywords = $_POST['products_meta_keywords'];
      $products_url = $_POST['products_url'];
    } else {
      $pInfo = new objectInfo(array());
    }

    $manufacturers_array = array(array('id' => '', 'text' => TEXT_NONE));
    $manufacturers_query = new xenQuery("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($manufacturers = $q->output()) {
      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
                                     'text' => $manufacturers['manufacturers_name']);
    }

    $tax_class_array = array(array('id' => '0', 'text' => TEXT_NONE));
    $tax_class_query = new xenQuery("select tax_class_id, tax_class_title from " . TABLE_TAX_CLASS . " order by tax_class_title");
      $q = new xenQuery();
      if(!$q->run()) return;
    while ($tax_class = $q->output()) {
      $tax_class_array[] = array('id' => $tax_class['tax_class_id'],
                                 'text' => $tax_class['tax_class_title']);
    }

    $languages = xtc_get_languages();

    switch ($pInfo->products_status) {
      case '0': $status = false; $out_status = true; break;
      case '1':
      default: $status = true; $out_status = false;
    }
?>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script type="text/javascript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script type="text/javascript">
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
</script>

<tr><td>
 <?php $form_action = ($_GET['pID']) ? 'update_product' : 'insert_product'; ?>

<?php echo xtc_draw_form('new_product', FILENAME_CATEGORIES, 'cPath=' . $_GET['cPath'] . '&pID=' . $_GET['pID'] . '&action='.$form_action, 'post', 'enctype="multipart/form-data"'); ?>
<table width="100%" border="0">
  <tr>
    <td class="pageHeading"><?php echo sprintf(TEXT_NEW_PRODUCT, xtc_output_generated_category_path($current_category_id)); ?></td>
  </tr>
  <tr>
    <td class="main" ><br> <?php echo TEXT_PRODUCTS_STATUS; ?> <?php echo xtc_draw_separator('pixel_trans.gif', '24', '15') . '&#160;' . xtc_draw_radio_field('products_status', '1', $status) . '&#160;' . TEXT_PRODUCT_AVAILABLE . '&#160;' . xtc_draw_radio_field('products_status', '0', $out_status) . '&#160;' . TEXT_PRODUCT_NOT_AVAILABLE; ?></td>
  </tr>
  <tr>
    <td class="main"><table width="100%" border="0">
        <tr>
          <td class="main" width="127"><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?><br>
            <small>(YYYY-MM-DD)</small></td>
          <td class="main" width="834"><?php echo xtc_draw_separator('pixel_trans.gif', '24', '15') . '&#160;'; ?>
            <script type="text/javascript">dateAvailable.writeControl(); dateAvailable.dateFormat="yyyy-MM-dd";</script></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_MANUFACTURER; ?><?php echo xtc_draw_separator('pixel_trans.gif', '24', '15') . '&#160;' . commerce_userapi_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id); ?></td>
  </tr>
  <tr>
   <td class="main">
        <?php
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
 $default_array=array();
 // set default value in dropdown!
if ($content['content_file']=='') {
$default_array[]=array('id' => 'default','text' => TEXT_SELECT);
$default_value=$pInfo->product_template;
$files=array_merge($default_array,$files);
} else {
$default_array[]=array('id' => 'default','text' => TEXT_NO_FILE);
$default_value=$pInfo->product_template;
$files=array_merge($default_array,$files);
}
echo TEXT_CHOOSE_INFO_TEMPLATE.':';
echo commerce_userapi_draw_pull_down_menu('info_template',$files,$default_value);
?>
   </td>
  </tr>
    <tr>
   <td class="main">
        <?php
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
 // set default value in dropdown!
 $default_array=array();
if ($content['content_file']=='') {
$default_array[]=array('id' => 'default','text' => TEXT_SELECT);
$default_value=$pInfo->options_template;
$files=array_merge($default_array,$files);
} else {
$default_array[]=array('id' => 'default','text' => TEXT_NO_FILE);
$default_value=$pInfo->options_template;
$files=array_merge($default_array,$files);
}
echo TEXT_CHOOSE_OPTIONS_TEMPLATE.':';
echo commerce_userapi_draw_pull_down_menu('options_template',$files,$default_value);
?>
   </td>
  </tr>
  </table>
  <br><br>
  <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
  <table width="100%" border="0">
  <tr>
  <td bgcolor="000000" height="10"></td>
  </tr>
  <tr>
    <td bgcolor="#FFCC33" valign="top" class="main"><?php echo xtc_image(xarTplGetImage(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/'. $languages[$i]['image']), $languages[$i]['name']); ?>&#160;<?php echo TEXT_PRODUCTS_NAME; ?><?php echo xtc_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : xarModAPIFunc('commerce','user','get_products_name',array('id' =>$pInfo->products_id, $languages[$i]['id'])),'size=60')); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_URL . '&#160;<small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>'; ?><?php echo xtc_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : xtc_get_products_url($pInfo->products_id, $languages[$i]['id'])),'size=60'); ?></td>
  </tr>
</table>
<table width="100%" border="0">
  <tr>
    <td class="main" colspan="2"><b><?php echo TEXT_PRODUCTS_DESCRIPTION; ?></b><br>

       <?php
if (USE_SPAW=='true') {
$sw = new SPAW_Wysiwyg(
             $control_name='products_description_'.$languages[$i]['id'] , // control's name
               $value= (($products_description[$languages[$i]['id']]) ? stripslashes($products_description[$languages[$i]['id']]) : xtc_get_products_description($pInfo->products_id, $languages[$i]['id'])),                  // initial value
              $lang='',                   // language
              $mode = '',                 // toolbar mode
              $theme='default',                  // theme (skin)
              $width='100%',              // width
              $height='400px',            // height
              $css_stylesheet='',         // css stylesheet file for content
              $dropdown_data=''           // data for dropdowns (style, font, etc.)
            );
$sw->show();
} else {
echo xtc_draw_textarea_field('products_meta_description_' . $languages[$i]['id'], 'soft', '150', '15', (($products_meta_description[$languages[$i]['id']]) ? stripslashes($products_meta_description[$languages[$i]['id']]) : xtc_get_products_meta_description($pInfo->products_id, $languages[$i]['id'])));
}
?>
    </td>
  </tr>
  <tr>
    <td class="main" width="60%" rowspan="2" valign="top"><b><?php echo TEXT_PRODUCTS_SHORT_DESCRIPTION; ?></b><br>
      <?php
if (USE_SPAW=='true') {
       $sw = new SPAW_Wysiwyg(
             $control_name='products_short_description_' . $languages[$i]['id'] , // control's name
             $value= (($products_short_description[$languages[$i]['id']]) ? stripslashes($products_short_description[$languages[$i]['id']]) : xtc_get_products_short_description($pInfo->products_id, $languages[$i]['id'])),                  // initial value
              $lang='',                   // language
              $mode = 'mini',                 // toolbar mode
              $theme='default',                  // theme (skin)
              $width='100%',              // width
              $height='150px',            // height
              $css_stylesheet='',         // css stylesheet file for content
              $dropdown_data=''           // data for dropdowns (style, font, etc.)
            );
$sw->show();
} else {
echo xtc_draw_textarea_field('products_short_description_' . $languages[$i]['id'], 'soft', '60', '8', (($products_short_description[$languages[$i]['id']]) ? stripslashes($products_short_description[$languages[$i]['id']]) : xtc_get_products_short_description($pInfo->products_id, $languages[$i]['id'])));
}
      ?></td>
    <td class="main"><?php echo TEXT_META_TITLE; ?><br>
      <?php echo xtc_draw_textarea_field('products_meta_title[' . $languages[$i]['id'] . ']', 'soft', '60', '1', (($products_meta_title[$languages[$i]['id']]) ? stripslashes($products_meta_title[$languages[$i]['id']]) : xtc_get_products_meta_title($pInfo->products_id, $languages[$i]['id']))); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_META_DESCRIPTION; ?><br>
      <?php echo xtc_draw_textarea_field('products_meta_description[' . $languages[$i]['id'] . ']', 'soft', '60', '3', (($products_meta_description[$languages[$i]['id']]) ? stripslashes($products_meta_description[$languages[$i]['id']]) : xtc_get_products_meta_description($pInfo->products_id, $languages[$i]['id']))); ?><br>
      <?php echo TEXT_META_KEYWORDS; ?><br>
      <?php echo xtc_draw_textarea_field('products_meta_keywords[' . $languages[$i]['id'] . ']', 'soft', '60', '3', (($products_meta_keywords[$languages[$i]['id']]) ? stripslashes($products_meta_keywords[$languages[$i]['id']]) : xtc_get_products_meta_keywords($pInfo->products_id, $languages[$i]['id']))); ?>
    </td>
  </tr>
</table>

<?php } ?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr><?php echo HEADING_PRODUCT_OPTIONS; ?></tr>
<tr><td>
<table width="100%" border="0" bgcolor="f3f3f3" style="border: 1px solid; border-color: #cccccc;">
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_QUANTITY; ?><br><?php echo xtc_draw_input_field('products_quantity', $pInfo->products_quantity); ?></td>
          </tr>
          <tr><td colspan="4"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>

          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_WEIGHT; ?><br><?php echo xtc_draw_input_field('products_weight', $pInfo->products_weight); ?><?php echo TEXT_PRODUCTS_WEIGHT_INFO; ?></td>
          </tr>
          <tr><td colspan="4"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>

          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_MODEL; ?><br><?php echo  xtc_draw_input_field('products_model', $pInfo->products_model); ?></td>
          </tr>
          <tr><td colspan="4"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_IMAGE; ?><br>
            <?php echo  xtc_draw_file_field('products_image') . '<br>' .
            xtc_draw_separator('pixel_trans.gif', '24', '15') . '&#160;' . $pInfo->products_image .
<input type="hidden" name="products_previous_image" value="#$pInfo->products_image#">

            </td>
          </tr>
          <tr><td colspan="4"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
</table>
</td></tr>

<tr><td>
<table width="100%" border="0">
        <tr>
          <td colspan="4"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
        <tr>
          <td colspan="4"><?php include(DIR_WS_MODULES.'group_prices.php'); ?></td>
        </tr>
        <tr>
          <td colspan="4"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
</table>
</td></tr>

    <tr>
      <td class="main" align="right">
<input type="hidden" name="products_date_added" value="#(($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d'))#">

    <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_save.gif')#" border="0" alt=IMAGE_SAVE style="cursor:hand" onclick="return confirm(\''.SAVE_ENTRY.'\')">.
      . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>'; ?></td>
    </tr></form>