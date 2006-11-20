<?php
/* --------------------------------------------------------------
   $Id: accounting.php,v 1.2 2003/12/31 18:08:47 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercecoding standards www.oscommerce.com
   (c) 2003  nextcommerce (accounting.php,v 1.27 2003/08/24); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  if ($_GET['action']!='') {
    switch ($_GET['action']) {
      case 'setflag':
        xtc_set_admin_access($_GET['id'], $_GET['flag'], $_GET['cID']);
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'cID=' . $_GET['cID'], 'NONSSL'));
        break;
      }
    }
    if ($_GET['cID'] != '') {
      if ($_GET['cID'] == 1) {
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CUSTOMERS, 'cID=' . $_GET['cID'], 'NONSSL'));
      } else {
        $allow_edit_query = new xenQuery("select customers_status, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $_GET['cID'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
        $allow_edit = $q->output();
        if ($allow_edit['customers_status'] != 0 || $allow_edit == '') {
          xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CUSTOMERS, 'cID=' . $_GET['cID'], 'NONSSL'));
        }
      }
    }
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo $allow_edit['customers_lastname'].' '.$allow_edit['customers_firstname']; ?></td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table valign="top" width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr class="dataTableHeadingRow">
            <td class="dataTableHeadingContent"><?php echo TEXT_ACCESS; ?></td>
            <td class="dataTableHeadingContent"><?php echo TEXT_ALLOWED; ?></td>
          </tr>
        </table></td>
      </tr>
<?php
    $customers_id = xtc_db_prepare_input($_GET['cID']);
    $admin_access_query = new xenQuery("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . $_GET['cID'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
    $admin_access = $q->output();
    if ($admin_access == '') {
      new xenQuery("INSERT INTO " . TABLE_ADMIN_ACCESS . " (customers_id) VALUES ('" . $_GET['cID'] . "')");
      $admin_access_query = new xenQuery("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . $_GET['cID'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $admin_access = $q->output();
    }
?>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_CONFIGURATION; ?></td>
        <td><?php
    if ($admin_access['configuration'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=configuration&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=configuration&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_MODULES; ?></td>
        <td><?php
    if ($admin_access['modules'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=modules&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=modules&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_COUNTRIES; ?></td>
        <td><?php
    if ($admin_access['countries'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=countries&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=countries&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_LANGUAGES; ?></td>
        <td><?php
    if ($admin_access['languages'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=languages&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=languages&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_ZONES; ?></td>
        <td><?php
    if ($admin_access['zones'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=zones&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=zones&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_GEO_ZONES; ?></td>
        <td><?php
    if ($admin_access['geo_zones'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=geo_zones&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=geo_zones&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_TAX_CLASSES; ?></td>
        <td><?php
    if ($admin_access['tax_classes'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=tax_classes&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=tax_classes&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_TAX_RATES; ?></td>
        <td><?php
    if ($admin_access['tax_rates'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=tax_rates&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=tax_rates&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_CUSTOMERS; ?></td>
        <td><?php
    if ($admin_access['customers'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=customers&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=customers&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_ACCOUNTING; ?></td>
        <td><?php
    if ($admin_access['accounting'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=accounting&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=accounting&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_CUSTOMERS_STATUS; ?></td>
        <td><?php
    if ($admin_access['customers_status'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=customers_status&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=customers_status&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_ORDERS; ?></td>
        <td><?php
    if ($admin_access['orders'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=orders&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=orders&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td  class="dataTableContentRow"><?php echo BOX_CATEGORIES; ?></td>
        <td><?php
    if ($admin_access['categories'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=categories&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=categories&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_ATTRIBUTES_MANAGER; ?></td>
        <td><?php
    if ($admin_access['new_attributes'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=new_attributes&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=new_attributes&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_PRODUCTS_ATTRIBUTES; ?></td>
        <td><?php
    if ($admin_access['products_attributes'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=products_attributes&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=products_attributes&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_MANUFACTURERS; ?></td>
        <td><?php
    if ($admin_access['manufacturers'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=manufacturers&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=manufacturers&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_REVIEWS; ?></td>
        <td><?php
      if ($admin_access['reviews'] == '1') {
        echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=reviews&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=reviews&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_XSELL_PRODUCTS; ?></td>
        <td><?php
      if ($admin_access['xsell_products'] == '1') {
        echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=xsell_products&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=xsell_products&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_SPECIALS; ?></td>
        <td><?php
    if ($admin_access['specials'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=specials&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=specials&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_PRODUCTS_EXPECTED; ?></td>
        <td><?php
    if ($admin_access['stats_products_expected'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=stats_products_expected&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=stats_products_expected&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_PRODUCTS_VIEWED; ?></td>
        <td><?php
    if ($admin_access['stats_products_viewed'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=stats_products_viewed&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=stats_products_viewed&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_PRODUCTS_PURCHASED; ?></td>
        <td><?php
    if ($admin_access['stats_products_purchased'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=stats_products_purchased&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=stats_products_purchased&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_STATS_CUSTOMERS; ?></td>
        <td><?php
    if ($admin_access['stats_customers'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=stats_customers&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=stats_customers&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_BACKUP; ?></td>
        <td><?php
    if ($admin_access['backup'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=backup&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=backup&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_BANNER_MANAGER; ?></td>
        <td><?php
    if ($admin_access['banner_manager'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=banner_manager&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=banner_manager&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_CACHE; ?></td>
        <td><?php
    if ($admin_access['cache'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=cache&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=cache&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_DEFINE_LANGUAGE; ?></td>
        <td><?php
    if ($admin_access['define_language'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=define_language&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=define_language&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_FILE_MANAGER; ?></td>
        <td><?php
    if ($admin_access['file_manager'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=file_manager&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=file_manager&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_MAIL; ?></td>
        <td><?php
    if ($admin_access['mail'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=mail&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=mail&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_NEWSLETTERS; ?></td>
        <td><?php
    if ($admin_access['newsletters'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=newsletters&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=newsletters&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_SERVER_INFO; ?></td>
        <td><?php
    if ($admin_access['server_info'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=server_info&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=server_info&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_WHOS_ONLINE; ?></td>
        <td><?php
    if ($admin_access['whos_online'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=whos_online&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=whos_online&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
      <tr class="dataTable">
        <td class="dataTableContentRow"><?php echo BOX_CONTENT; ?></td>
        <td><?php
    if ($admin_access['content_manager'] == '1') {
      echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green.gif'), IMAGE_ICON_STATUS_GREEN, 10, 10) . '&#160;&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=0&id=content_manager&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red_light.gif'), IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
       echo '<a href="' . xarModURL('commerce','admin',(FILENAME_ACCOUNTING, 'action=setflag&flag=1&id=content_manager&cID=' . $customers_id, 'NONSSL') . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_green_light.gif'), IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&#160;&#160;' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_status_red.gif'), IMAGE_ICON_STATUS_RED, 10, 10);
    }
?></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>