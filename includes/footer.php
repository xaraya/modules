<?php
/* -----------------------------------------------------------------------------------------
   $Id: footer.php,v 1.3 2003/09/15 18:04:16 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(footer.php,v 1.26 2003/02/10); www.oscommerce.com
   (c) 2003  nextcommerce (footer.php,v 1.14 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
// include needed functions
require_once('inc/xtc_banner_exists.inc.php');
require_once('inc/xtc_display_banner.inc.php');
require_once('inc/xtc_update_banner_display_count.inc.php');

  require(DIR_WS_INCLUDES . 'counter.php');
?>
<table border="0" width="100%" cellspacing="0" cellpadding="1">
  <tr class="footer">
    <td class="footer">&#160;&#160;<?php echo strftime(DATE_FORMAT_LONG); ?>&#160;&#160;</td>
    <td align="right" class="footer">&#160;&#160;<?php echo $counter_now . ' ' . FOOTER_TEXT_REQUESTS_SINCE . ' ' . $counter_startdate_formatted; ?>&#160;&#160;</td>
  </tr>
</table>
    </td>
  </tr>
</table>
</td></tr></table>
</center>
<br>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" class="smallText">
<?php
/*
  The following copyright announcement can only be
  appropriately modified or removed if the layout of
  the site theme has been modified to distinguish
  itself from the default XT-Commerce-copyrighted
  theme.

  Please leave this comment intact together with the
  following copyright announcement.

  Copyright announcement changed due to the permissions
  from LG Hamburg from 28th February 2003 / AZ 308 O 70/03
*/
?>


<?php
echo FOOTER_TEXT_BODY;

  if ($banner = xtc_banner_exists('dynamic', '468x50')) {
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><?php echo xtc_display_banner('static', $banner); ?></td>
  </tr>
</table>

<?php
  }

/*

echo ('<font size="2" face="Verdana, Arial, Helvetica, sans-serif"><b>Session Debug:</b><br>');
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo '</font>';
*/
echo xtc_session_id();


?>
