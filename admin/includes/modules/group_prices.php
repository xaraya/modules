<?php
/* --------------------------------------------------------------
   $Id: group_prices.php,v 1.4 2003/12/31 15:05:05 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(based on original files from OSCommerce CVS 2.2 2002/08/28 02:14:35); www.oscommerce.com
   (c) 2003  nextcommerce (group_prices.php,v 1.16 2003/08/21); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------
   based on Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/
  require_once(DIR_FS_INC .'xtc_get_tax_rate.inc.php');
  $i = 0;
  $group_query = new xenQuery("SELECT
                                   customers_status_image,
                                   customers_status_id,
                                   customers_status_name
                               FROM
                                   " . TABLE_CUSTOMERS_STATUS . "
                               WHERE
                                   language_id = '" . $_SESSION['languages_id'] . "' AND customers_status_id != '0'");
      $q = new xenQuery();
      if(!$q->run()) return;
  while ($group_values = $q->output()) {
    // load data into array
    $i++;
    $group_data[$i] = array(
      'STATUS_NAME' => $group_values['customers_status_name'],
      'STATUS_IMAGE' => $group_values['customers_status_image'],
      'STATUS_ID' => $group_values['customers_status_id']);
  }
  echo HEADING_PRICES_OPTIONS;
?><table width="100%" border="0" bgcolor="f3f3f3" style="border: 1px solid; border-color: #cccccc;">
          <tr>
            <td width="50%" class="main"><?php echo TEXT_PRODUCTS_PRICE; ?></td>
<?php
// calculate brutto price for display

if (PRICE_IS_BRUTTO=='true'){
xtc_get_tax_rate($pInfo->products_tax_class_id);
$products_price = xtc_round($pInfo->products_price*((100+xtc_get_tax_rate($pInfo->products_tax_class_id))/100),PRICE_PRECISION);

} else {
$products_price = xtc_round($pInfo->products_price,PRICE_PRECISION);
}




?>
            <td width="50%" class="main"><?php echo xtc_draw_input_field('products_price', $products_price); ?>
<?php
if (PRICE_IS_BRUTTO=='true'){
echo TEXT_NETTO .'<b>'.$currencies->format(xtc_round($pInfo->products_price,PRICE_PRECISION)).'</b>  ';
}
?>
</td>
          </tr>
<?php
  for ($col = 0, $n = sizeof($group_data); $col < $n+1; $col++) {
    if ($group_data[$col]['STATUS_NAME'] != '') {
?>
          <tr>
            <td style="border-top: 1px solid; border-color: #cccccc;" valign="top" class="main"><?php echo $group_data[$col]['STATUS_NAME']; ?></td>
<?php
if (PRICE_IS_BRUTTO=='true'){
$products_price = xtc_round(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id)*((100+xtc_get_tax_rate($pInfo->products_tax_class_id))/100),PRICE_PRECISION);

} else {
$products_price = xtc_round(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id),PRICE_PRECISION);
}

?>
            <td style="border-top: 1px solid; border-color: #cccccc;" class="main"><?php echo xtc_draw_input_field('products_price_' . $group_data[$col]['STATUS_ID'], $products_price);

if (PRICE_IS_BRUTTO=='true' && get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id)!='0'){
echo TEXT_NETTO . '<b>'.$currencies->format(xtc_round(get_group_price($group_data[$col]['STATUS_ID'], $pInfo->products_id),PRICE_PRECISION)).'</b>  ';
}


      if ($_GET['pID'] != '') {
        echo ' ' . TXT_STAFFELPREIS; ?> <img onmouseover="this.style.cursor='hand'" src="images/arrow_down.gif" height="12" width="12" onclick="toggleBox('staffel_<?php echo $group_data[$col]['STATUS_ID']; ?>');"><?php
      }
      if ($_GET['pID'] != '') {
      }
?><div id="staffel_<?php echo $group_data[$col]['STATUS_ID']; ?>" class="longDescription"><br><?php
      // ok, lets check if there is already a staffelpreis
      $staffel_query = new xenQuery("SELECT
                                         products_id,
                                         quantity,
                                         personal_offer
                                     FROM
                                         personal_offers_by_customers_status_" . $group_data[$col]['STATUS_ID'] . "
                                     WHERE
                                         products_id = '" . $pInfo->products_id . "' AND quantity != 1
                                     ORDER BY quantity ASC");
      echo '<table width="247" border="0" cellpadding="0" cellspacing="0">';
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($staffel_values = $q->output()) {
      // load data into array
?>
              <tr>
                <td width="20" class="main" style="border: 1px solid; border-color: #cccccc;"><?php echo $staffel_values['quantity']; ?></td>
                <td width="5">&#160;</td>
                <td nowrap width="142" class="main" style="border: 1px solid; border-color: #cccccc;">
<?php
if (PRICE_IS_BRUTTO=='true'){
$tax_query = new xenQuery("select tax_rate from " . TABLE_TAX_RATES . " where tax_class_id = '" . $pInfo->products_tax_class_id . "' ");
      $q = new xenQuery();
      if(!$q->run()) return;
$tax = $q->output();

$products_price = xtc_round($staffel_values['personal_offer']*((100+$tax['tax_rate'])/100),PRICE_PRECISION);

} else {
$products_price = xtc_round($staffel_values['personal_offer'],PRICE_PRECISION);
}
 echo $products_price;
 if (PRICE_IS_BRUTTO=='true'){
echo ' <br>'.TEXT_NETTO .'<b>'. $currencies->format(xtc_round($staffel_values['personal_offer'],PRICE_PRECISION)).'</b>  ';
}

 ?>
 </td>
                <td width="80" align="left"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?><a href="<?php echo xarModURL('commerce','admin',(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&function=delete&quantity=' . $staffel_values['quantity'] . '&statusID=' . $group_data[$col]['STATUS_ID'] . '&action=new_product&pID=' . $_GET['pID']); ?>"><?php echo xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE);; ?></a></td>
              </tr>
              <tr>
                <td colspan="3" height="5"></td>
              </tr>
<?php
      }

      echo '</table>';
      echo TXT_STK;
      echo xtc_draw_small_input_field('products_quantity_staffel_'.$group_data[$col]['STATUS_ID'], 0);
      echo TXT_PRICE;
      echo xtc_draw_input_field('products_price_staffel_'.$group_data[$col]['STATUS_ID'], 0);
      echo xtc_draw_separator('pixel_trans.gif', '10', '10');
      echo <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_insert.gif')#" border="0" alt=IMAGE_INSERT>;
?><br></td>
          </tr>
<?php
    }
  }
?></div>
          <tr>
            <td style="border-top: 1px solid; border-color: #cccccc;" class="main"><?php echo TEXT_PRODUCTS_DISCOUNT_ALLOWED; ?></td>
            <td style="border-top: 1px solid; border-color: #cccccc;" class="main"><?php echo xtc_draw_input_field('products_discount_allowed', $pInfo->products_discount_allowed); ?></td>
          </tr>
          <tr>
            <td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo TEXT_PRODUCTS_TAX_CLASS; ?></td>
            <td class="main"><?php echo commerce_userapi_draw_pull_down_menu('products_tax_class_id', $tax_class_array, $pInfo->products_tax_class_id); ?></td>
          </tr>
        </table>