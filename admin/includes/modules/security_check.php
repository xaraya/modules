<?php
/* --------------------------------------------------------------
   $Id: security_check.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2003  nextcommerce (security_check.php,v 1.2 2003/08/23); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/
$file_warning='';

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'includes/configure.php')),'444')) {
$file_warning .= '<br>'.DIR_FS_CATALOG.'includes/configure.php';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'includes/configure.org.php')),'444')) {
$file_warning .= '<br>'.DIR_FS_CATALOG.'includes/configure.org.php';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'admin/includes/configure.php')),'444')) {
$file_warning .= '<br>'.DIR_FS_CATALOG.'admin/includes/configure.php';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'admin/includes/configure.org.php')),'444')) {
$file_warning .= '<br>'.DIR_FS_CATALOG.'admin/includes/configure.org.php';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'admin/rss/')),'777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'admin/rss/')),'755')) {
$folder_warning .= '<br>'.DIR_FS_CATALOG.'admin/rss/';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'templates_c/')),'777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'templates_c/')),'755')) {
$folder_warning .=  '<br>'.DIR_FS_CATALOG.'templates_c/';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'cache/')),'777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'cache/')),'755')) {
$folder_warning .=  '<br>'.DIR_FS_CATALOG.'cache/';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'media/')),'777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'media/')),'755')) {
$folder_warning .=  '<br>'.DIR_FS_CATALOG.'media/';
}

if (!strpos(decoct(fileperms(DIR_FS_CATALOG.'media/content/')),'777') and !strpos(decoct(fileperms(DIR_FS_CATALOG.'media/content/')),'755')) {
$folder_warning .=  '<br>'.DIR_FS_CATALOG.'media/content/';
}

if ($file_warning!='' or $folder_warning != '') {
 ?>


<table style="border: 1px solid; border-color: #ff0000;" bgcolor="FDAC00" border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td>
<div class"main">
        <table width="100%" border="0">
          <tr>
            <td width="1"><?php echo xtc_image(xarTplGetImage('icons/big_warning.gif'); ?></td>
            <td class="main">
              <?php
 if ($file_warning!='') {

 echo TEXT_FILE_WARNING;

echo '<b>'.$file_warning.'</b><br>';
}

 if ($folder_warning!='') {

 echo TEXT_FOLDER_WARNING;

echo '<b>'.$folder_warning.'</b>';
}

$payment_query=new xenQuery("SELECT *
            FROM ".TABLE_CONFIGURATION."
            WHERE configuration_key = 'MODULE_PAYMENT_INSTALLED'");
      $q = new xenQuery();
      $q->run();
while ($payment_data=$q->output()) {
 $installed_payment=$payment_data['configuration_value'];


}
if ($installed_payment=='') {
echo '<br>'.TEXT_PAYMENT_ERROR;
}
$shipping_query=new xenQuery("SELECT *
            FROM ".TABLE_CONFIGURATION."
            WHERE configuration_key = 'MODULE_SHIPPING_INSTALLED'");
      $q = new xenQuery();
      $q->run();
while ($shipping_data=$q->output()) {
 $installed_shipping=$shipping_data['configuration_value'];


}
if ($installed_shipping=='') {
echo '<br>'.TEXT_SHIPPING_ERROR;
}
 ?>
            </td>
          </tr>
        </table>
      </div>
</td>
</tr>
</table>
<?php
}
?>
