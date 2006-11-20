<?php
/* --------------------------------------------------------------
   $Id: invoice.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(invoice.php,v 1.5 2003/05/14); www.oscommerce.com
   (c) 2003  nextcommerce (invoice.php,v 1.12 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  $oID = xtc_db_prepare_input($_GET['oID']);
  $orders_query = new xenQuery("select orders_id from " . TABLE_ORDERS . " where orders_id = '" . xtc_db_input($oID) . "'");

  include(DIR_WS_CLASSES . 'order.php');
  $order = new order($oID);
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">

<!-- body_text //-->
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td class="pageHeading"><?php echo nl2br(STORE_NAME_ADDRESS); ?></td>
        <td class="pageHeading" align="right"><?php echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'logo.gif'), 'neXTCommerce', '185', '95'); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td colspan="2"><?php echo xtc_draw_separator(); ?></td>
      </tr>
      <tr>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo ENTRY_SOLD_TO; ?></b></td>
          </tr>
          <tr>
            <td class="main">#xarModAPIFunc('commerce','user','address_format',array(
    'address_format_id' =>$order->customer['format_id'],
    'address' =>$order->customer,
    'html' =>1,
    'boln' =>'',
    'eoln' =>'<br>'))#</td>
          </tr>
          <tr>
            <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo $order->customer['telephone']; ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo '<a href="mailto:' . $order->customer['email_address'] . '"><u>' . $order->customer['email_address'] . '</u></a>'; ?></td>
          </tr>
        </table></td>
        <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo ENTRY_SHIP_TO; ?></b></td>
          </tr>
          <tr>
            <td class="main">#xarModAPIFunc('commerce','user','address_format',array(
    'address_format_id' =>$order->delivery['format_id'],
    'address' =>$order->delivery,
    'html' =>1,
    'boln' =>'',
    'eoln' =>'<br>'))#</td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
    <td><table border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main"><b><?php echo ENTRY_PAYMENT_METHOD; ?></b></td>
        <td class="main"><?php echo $order->info['payment_method']; ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent" colspan="2"><?php echo TABLE_HEADING_PRODUCTS; ?></td>
            <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_PRODUCTS_MODEL; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_EXCLUDING_TAX; ?></td>
<?php
  if ($order->products[0]['allow_tax'] == 1) {
?>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TAX; ?></td>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_PRICE_INCLUDING_TAX; ?></td>
<?php
  }
?>
            <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_TOTAL_INCLUDING_TAX; ?></td>
          </tr>
<?php
  for ($i = 0, $n = sizeof($order->products); $i < $n; $i++) {
?>
          <tr class="dataTableRow">
            <td class="dataTableContent" valign="top" align="right"><?php echo $order->products[$i]['qty']; ?>&#160;x</td>
            <td class="dataTableContent" valign="top"><?php echo $order->products[$i]['name'];
    if (sizeof($order->products[$i]['attributes']) > 0) {
      for ($j = 0, $k = sizeof($order->products[$i]['attributes']); $j < $k; $j++) {
?><br><nobr><small>&#160;<i> - <?php echo $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'];
        if ($order->products[$i]['attributes'][$j]['price'] != '0') echo ' (' . $order->products[$i]['attributes'][$j]['prefix'] . $currencies->format($order->products[$i]['attributes'][$j]['price'] * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . ')';
        echo '</i></small></nobr>';
      }
    }
?></td>
            <td class="dataTableContent" valign="top"><?php echo $order->products[$i]['model']; ?></td>
            <td class="dataTableContent" align="right" valign="top"><?php echo format_price($order->products[$i]['final_price'], 1, $order->info['currency'], $order->products[$i]['allow_tax'], $order->products[$i]['tax']); ?></td>
<?php
    if ($order->products[$i]['allow_tax'] == 1) {
      echo '<td class="dataTableContent" align="right" valign="top">';
      echo xtc_display_tax_value($order->products[$i]['tax']) . '%';
      echo '</td>' . "\n";
      echo '<td class="dataTableContent" align="right" valign="top"><b>';

      echo format_price($order->products[$i]['final_price'], 1, $order->info['currency'], 0, 0);

      echo '</b></td>' . "\n";
    }
    echo     '            <td class="dataTableContent" align="right" valign="top"><b>' . format_price(($order->products[$i]['final_price']*$order->products[$i]['qty']),1,$order->info['currency'],0,0). '</b></td>' . "\n";
    echo '          </tr>' . "\n";
  }
?>
          <tr>
            <td align="right" colspan="10"><table border="0" cellspacing="0" cellpadding="2">
<?php
  for ($i = 0, $n = sizeof($order->totals); $i < $n; $i++) {
    echo '              <tr>' . "\n" .
         '                <td align="right" class="smallText">' . $order->totals[$i]['title'] . '</td>' . "\n" .
         '                <td align="right" class="smallText">' . $order->totals[$i]['text'] . '</td>' . "\n" .
         '              </tr>' . "\n";
  }
?>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td class="main"><br><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td>
  </tr>
  <tr>
    <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
  </tr>
   <tr>
     <td class="main"><?php echo nl2br($order->info['comments']); ?></td>
   </tr>
</table><br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>


