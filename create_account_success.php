<?php
/* -----------------------------------------------------------------------------------------
   $Id: create_account_success.php,v 1.3 2003/10/22 11:22:54 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(create_account_success.php,v 1.29 2003/02/13); www.oscommerce.com
   (c) 2003  nextcommerce (create_account_success.php,v 1.12 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  include( 'includes/application_top.php');

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_array_to_string.inc.php');

//  require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . FILENAME_CREATE_ACCOUNT_SUCCESS);

  $breadcrumb->add(NAVBAR_TITLE_1_CREATE_ACCOUNT_SUCCESS);
  $breadcrumb->add(NAVBAR_TITLE_2_CREATE_ACCOUNT_SUCCESS);

  if (sizeof($_SESSION['navigation']->snapshot) > 0) {
    $origin_href = xarModURL('commerce','user',($_SESSION['navigation']->snapshot['page'], xtc_array_to_string($_SESSION['navigation']->snapshot['get'], array(xtc_session_name())), $_SESSION['navigation']->snapshot['mode']);
    $_SESSION['navigation']->clear_snapshot();
  } else {
    $origin_href = xarModURL('commerce','user','default');
  }
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<?php include(DIR_WS_MODULES.FILENAME_METATAGS); ?>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td class="navLeft" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="0">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="tableBody" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td><?php echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'table_background_man_on_board.gif'), HEADING_TITLE); ?></td>
            <td valign="top" class="main"><div align="center" class="pageHeading"><?php echo HEADING_TITLE; ?></div><br><?php echo TEXT_ACCOUNT_CREATED; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td align="right"><br><?php echo '<a href="' . $origin_href . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_continue.gif'),
        'alt' => IMAGE_BUTTON_CONTINUE);
        . '</a>'; ?></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
    <td class="navRight" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="0">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>