<?php
/* --------------------------------------------------------------
   $Id: configuration.php,v 1.1 2003/09/06 22:05:29 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003  nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/

  require('includes/application_top.php');

  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'save':
        $configuration_value = xtc_db_prepare_input($_POST['configuration_value']);
        $cID = xtc_db_prepare_input($_GET['cID']);

        new xenQuery("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($configuration_value) . "', last_modified = now() where configuration_id = '" . xtc_db_input($cID) . "'");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $cID));
        break;
    }
  }

  $cfg_group_query = new xenQuery("select configuration_group_title from " . TABLE_CONFIGURATION_GROUP . " where configuration_group_id = '" . $_GET['gID'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
  $cfg_group = $q->output();
?>
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="80" rowspan="2"><?php echo xtc_image(xarTplGetImage(DIR_WS_ICONS.'heading_configuration.gif'); ?></td>
    <td class="pageHeading"><?php echo $cfg_group['configuration_group_title']; ?></td>
  </tr>
  <tr>
    <td class="main" valign="top">XT Configuration</td>
  </tr>
</table> </td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CONFIGURATION_TITLE; ?></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CONFIGURATION_VALUE; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&#160;</td>
              </tr>
<?php
  $configuration_query = new xenQuery("select configuration_key,configuration_id, configuration_value, use_function from " . TABLE_CONFIGURATION . " where configuration_group_id = '" . $_GET['gID'] . "' order by sort_order");

      $q = new xenQuery();
      if(!$q->run()) return;
  while ($configuration = $q->output()) {
    if ($_GET['gID'] == 6) {
      switch ($configuration['configuration_key']) {
        case 'MODULE_PAYMENT_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $payment_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($payment_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $payment_installed[$i]);
            }
          }
          break;

        case 'MODULE_SHIPPING_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $shipping_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($shipping_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/' . $shipping_installed[$i]);
            }
          }
          break;

        case 'MODULE_ORDER_TOTAL_INSTALLED':
          if ($configuration['configuration_value'] != '') {
            $ot_installed = explode(';', $configuration['configuration_value']);
            for ($i = 0, $n = sizeof($ot_installed); $i < $n; $i++) {
              include(DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/' . $ot_installed[$i]);
            }
          }
          break;
      }
    }
    if (xarModAPIFunc('commerce','user','not_null',array('arg' => $configuration['use_function']))) {
      $use_function = $configuration['use_function'];
      if (ereg('->', $use_function)) {
        $class_method = explode('->', $use_function);
        if (!is_object(${$class_method[0]})) {
          include(DIR_WS_CLASSES . $class_method[0] . '.php');
          ${$class_method[0]} = new $class_method[0]();
        }
        $cfgValue = xarModAPIFunc('commerce','admin','call_function',array(
                                    'function' => $class_method[1],
                                    'parameter' => $configuration['configuration_value'],
                                    'object' => ${$class_method[0]}), , )
                                 );
      } else {
        $cfgValue = xarModAPIFunc('commerce','admin','call_function',array(
                                    'function' => $use_function,
                                    'parameter' => $configuration['configuration_value'])
                                 );
      }
    } else {
      $cfgValue = $configuration['configuration_value'];
    }

    if (((!$_GET['cID']) || (@$_GET['cID'] == $configuration['configuration_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $cfg_extra_query = new xenQuery("select configuration_key, date_added, last_modified, use_function, set_function from " . TABLE_CONFIGURATION . " where configuration_id = '" . $configuration['configuration_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $cfg_extra = $q->output();

      $cInfo_array = xtc_array_merge($configuration, $cfg_extra);
      $cInfo = new objectInfo($cInfo_array);
    }

    if ( (is_object($cInfo)) && ($configuration['configuration_id'] == $cInfo->configuration_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $cInfo->configuration_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_CONFIGURATION, 'gID=' . $HTTP_GET_VARS['gID'] . '&cID=' . $configuration['configuration_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo constant(strtoupper( $configuration['configuration_key'].'_TITLE')); ?></td>
                <td class="dataTableContent"><?php $short_cfgValue = $cfgValue; if(is_string($short_cfgValue)) { if(strlen($short_cfgValue) > 30) $short_cfgValue = substr($short_cfgValue,0,30) . ' ...'; } echo htmlspecialchars($short_cfgValue); ?></td>

                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($configuration['configuration_id'] == $cInfo->configuration_id) ) { echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_arrow_right.gif'), ''); } else { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $configuration['configuration_id']) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_info.gif'), IMAGE_ICON_INFO) . '</a>'; } ?>&#160;</td>
              </tr>
<?php
  }
?>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'edit':
      $heading[] = array('text' => '<b>' .constant(strtoupper($cInfo->configuration_key .'_TITLE')) . '</b>');

      if ($cInfo->set_function) {
        eval('$value_field = ' . $cInfo->set_function . '"' . htmlspecialchars($cInfo->configuration_value) . '");');
      } else {
        $value_field = xtc_draw_input_field('configuration_value', $cInfo->configuration_value);
      }

      $contents = array('form' => xtc_draw_form('configuration', FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br><b>' .constant(strtoupper($cInfo->configuration_key .'_TITLE')) . '</b><br>' .xtc_get_lang_definition($cInfo->configuration_key,$config_lang,'_DESC'). '<br>' . $value_field);
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_UPDATE> . '&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;
    default:
      if (is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . constant(strtoupper($cInfo->configuration_key .'_TITLE')). '</b>');
        $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_CONFIGURATION, 'gID=' . $_GET['gID'] . '&cID=' . $cInfo->configuration_id . '&action=edit') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT); . '</a>');
        $contents[] = array('text' => '<br>' . constant(strtoupper($cInfo->configuration_key .'_DESC')));
        $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_ADDED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$cInfo->date_added)));
        if (xarModAPIFunc('commerce','user','not_null',array('arg' => $cInfo->last_modified))) $contents[] = array('text' => TEXT_INFO_LAST_MODIFIED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$cInfo->last_modified)));
      }
      break;
  }

  if ( (xarModAPIFunc('commerce','user','not_null',array('arg' => $heading))) && (xarModAPIFunc('commerce','user','not_null',array('arg' => $contents))) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
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