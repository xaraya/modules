<?php
/* -----------------------------------------------------------------------------------------
   $Id: content_preview.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2003  nextcommerce (content_preview.php,v 1.2 2003/08/25); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require('includes/application_top.php');

if ($_GET['pID']=='media') {
    $content_query=new xenQuery("SELECT
                    content_file,
                    content_name,
                    file_comment
                    FROM ".TABLE_PRODUCTS_CONTENT."
                    WHERE content_id='".$_GET['coID']."'");
      $q = new xenQuery();
      $q->run();
    $content_data=$q->output();

} else {
     $content_query=new xenQuery("SELECT
                    content_title,
                    content_heading,
                    content_text,
                    content_file
                    FROM ".TABLE_CONTENT_MANAGER."
                    WHERE content_id='".$_GET['coID']."'");
      $q = new xenQuery();
      $q->run();
    $content_data=$q->output();
 }
?>

<html <?php echo HTML_PARAMS; ?>>
<head>
<title><?php echo $page_title; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<div class="pageHeading"><?php echo $content_data['content_heading']; ?></div><br>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main">
 <?php
 if ($content_data['content_file']!=''){
if (strpos($content_data['content_file'],'.txt')) echo '<pre>';
if ($_GET['pID']=='media') {
    // display image
    if (eregi('.gif',$content_data['content_file']) or eregi('.jpg',$content_data['content_file']) or  eregi('.png',$content_data['content_file']) or  eregi('.tif',$content_data['content_file']) or  eregi('.bmp',$content_data['content_file'])) {
    echo xtc_image(xarTplGetImage(DIR_WS_CATALOG.'media/products/'.$content_data['content_file']);
    } else {
    include(DIR_FS_CATALOG.'media/products/'.$content_data['content_file']);
    }
} else {
include(DIR_FS_CATALOG.'media/content/'.$content_data['content_file']);
}
if (strpos($content_data['content_file'],'.txt')) echo '</pre>';
 } else {
echo $content_data['content_text'];
}
?>
</td>
          </tr>
        </table>







</body>
</html>
