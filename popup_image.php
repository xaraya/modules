<?php
/* -----------------------------------------------------------------------------------------
   $Id: popup_image.php,v 1.3 2003/11/09 11:53:15 gwinger Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(popup_image.php,v 1.12 2001/12/12); www.oscommerce.com

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   Modified by BIA Solutions (www.biasolutions.com) to create a bordered look to the image

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  require('includes/application_top.php');

  $products_query = new xenQuery("select pd.products_name, p.products_image from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id where p.products_status = '1' and p.products_id = '" . $_GET['pID'] . "' and pd.language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
  $products_values = $q->output();


$number = 0;

// get x and y of the image
$img = DIR_WS_POPUP_IMAGES.$products_values['products_image'];
$size = GetImageSize("$img");
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<title><?php echo $products_values['products_name']; ?></title>
<base href="<?php echo (getenv('HTTPS') == 'on' ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<script type="text/javascript"><!--
var i=0;
function resize() {
  if (navigator.appName == 'Netscape') i=40;
  window.resizeTo(<? echo $size[0] ?> +100, <? echo $size[1] ?>+120-i);
   self.focus();
}
//--></script>
</head>
<body onload="resize();" >


<!-- xtc_image(xarTplGetImage($src, $alt = '', $width = '', $height = '', $params = '') -->

<table border=0 height=100% align=center valign=center><tr><td>
<table border=0 cellpadding=0 cellspacing=0>
<tr><td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "a1.gif"),"", 10, 10) ?></td>
    <td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "b1.gif"),"", 10, 10) ?></td>
    <td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "c1.gif"),"", $size[0]-$number, 10) ?></td>
    <td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "d1.gif"),"", 10, 10) ?></td>
    <td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "e1.gif"),"", 10, 10) ?></td></tr>

<tr><td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "a2.gif"),"", 10, 10) ?></td>
    <td rowspan=3 colspan=3 align=center><?  echo xtc_image(xarTplGetImage(DIR_WS_POPUP_IMAGES . $products_values['products_image']), $products_values['products_name'], $size[0], $size[1]); ?></td>
    <td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "e2.gif"),"", 10, 10) ?></td></tr>

<tr><td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "a3.gif"),"", 10, $size[1]-$number) ?></td>
    <td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "e3.gif"),"", 10, $size[1]-$number) ?></td></tr>

<tr><td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "a4.gif"),"", 10, 10) ?></td>
    <td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "e4.gif"),"", 10, 10) ?></td></tr>

<tr><td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "a5.gif"),"", 10, 10) ?></td>
    <td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "b5.gif"),"", 10, 10) ?></td>
    <td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "c5.gif"),"", $size[0]-$number, 10) ?></td>
    <td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "d5.gif"),"", 10, 10) ?></td>
    <td><? echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . "e5.gif"),"", 10, 10) ?></td></tr>

</table>
</td></tr></table>

</body>
</html>
