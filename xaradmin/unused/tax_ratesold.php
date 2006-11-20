<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

function commerce_admin_tax_rates()
{


  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'insert':
        $tax_zone_id = xtc_db_prepare_input($_POST['tax_zone_id']);
        $tax_class_id = xtc_db_prepare_input($_POST['tax_class_id']);
        $tax_rate = xtc_db_prepare_input($_POST['tax_rate']);
        $tax_description = xtc_db_prepare_input($_POST['tax_description']);
        $tax_priority = xtc_db_prepare_input($_POST['tax_priority']);
        $date_added = xtc_db_prepare_input($_POST['date_added']);

        new xenQuery("insert into " . TABLE_TAX_RATES . " (tax_zone_id, tax_class_id, tax_rate, tax_description, tax_priority, date_added) values ('" . xtc_db_input($tax_zone_id) . "', '" . xtc_db_input($tax_class_id) . "', '" . xtc_db_input($tax_rate) . "', '" . xtc_db_input($tax_description) . "', '" . xtc_db_input($tax_priority) . "', now())");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_TAX_RATES));
        break;

      case 'save':
        $tax_rates_id = xtc_db_prepare_input($_GET['tID']);
        $tax_zone_id = xtc_db_prepare_input($_POST['tax_zone_id']);
        $tax_class_id = xtc_db_prepare_input($_POST['tax_class_id']);
        $tax_rate = xtc_db_prepare_input($_POST['tax_rate']);
        $tax_description = xtc_db_prepare_input($_POST['tax_description']);
        $tax_priority = xtc_db_prepare_input($_POST['tax_priority']);
        $last_modified = xtc_db_prepare_input($_POST['last_modified']);

        new xenQuery("update " . TABLE_TAX_RATES . " set tax_rates_id = '" . xtc_db_input($tax_rates_id) . "', tax_zone_id = '" . xtc_db_input($tax_zone_id) . "', tax_class_id = '" . xtc_db_input($tax_class_id) . "', tax_rate = '" . xtc_db_input($tax_rate) . "', tax_description = '" . xtc_db_input($tax_description) . "', tax_priority = '" . xtc_db_input($tax_priority) . "', last_modified = now() where tax_rates_id = '" . xtc_db_input($tax_rates_id) . "'");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $tax_rates_id));
        break;

      case 'deleteconfirm':
        $tax_rates_id = xtc_db_prepare_input($_GET['tID']);

        new xenQuery("delete from " . TABLE_TAX_RATES . " where tax_rates_id = '" . xtc_db_input($tax_rates_id) . "'");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_TAX_RATES, 'page=' . $_GET['page']));
        break;
    }
  }

  $rates_query_raw = "select r.tax_rates_id, z.geo_zone_id, z.geo_zone_name, tc.tax_class_title, tc.tax_class_id, r.tax_priority, r.tax_rate, r.tax_description, r.date_added, r.last_modified from " . TABLE_TAX_CLASS . " tc, " . TABLE_TAX_RATES . " r left join " . TABLE_GEO_ZONES . " z on r.tax_zone_id = z.geo_zone_id where r.tax_class_id = tc.tax_class_id";
  $rates_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $rates_query_raw, $rates_query_numrows);
  $rates_query = new xenQuery($rates_query_raw);
      $q = new xenQuery();
      if(!$q->run()) return;
  while ($rates = $q->output()) {
    if (((!$_GET['tID']) || (@$_GET['tID'] == $rates['tax_rates_id'])) && (!$trInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $trInfo = new objectInfo($rates);
    }

    if ( (is_object($trInfo)) && ($rates['tax_rates_id'] == $trInfo->tax_rates_id) ) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $rates['tax_rates_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $rates['tax_priority']; ?></td>
                <td class="dataTableContent"><?php echo $rates['tax_class_title']; ?></td>
                <td class="dataTableContent"><?php echo $rates['geo_zone_name']; ?></td>
                <td class="dataTableContent"><?php echo xtc_display_tax_value($rates['tax_rate']); ?>%</td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($trInfo)) && ($rates['tax_rates_id'] == $trInfo->tax_rates_id) ) { echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_arrow_right.gif'), ''); } else { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $rates['tax_rates_id']) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_info.gif'), IMAGE_ICON_INFO) . '</a>'; } ?>&#160;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $rates_split->display_count($rates_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAX_RATES); ?></td>
                    <td class="smallText" align="right"><?php echo $rates_split->display_links($rates_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td colspan="5" align="right"><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&action=new') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_new_tax_rate.gif'),'alt' => IMAGE_NEW_TAX_RATE)                                                </a>'; ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_TAX_RATE . '</b>');

      $contents = array('form' => xtc_draw_form('rates', FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_CLASS_TITLE . '<br>' . xtc_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_ZONE_NAME . '<br>' . xtc_geo_zones_pull_down('name="tax_zone_id" style="font-size:10px"'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_TAX_RATE . '<br>' . xtc_draw_input_field('tax_rate'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_RATE_DESCRIPTION . '<br>' . xtc_draw_input_field('tax_description'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_TAX_RATE_PRIORITY . '<br>' . xtc_draw_input_field('tax_priority'));
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_insert.gif')#" border="0" alt=IMAGE_INSERT> . '&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_RATES, 'page=' . $_GET['page']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_TAX_RATE . '</b>');

      $contents = array('form' => xtc_draw_form('rates', FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_CLASS_TITLE . '<br>' . xtc_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"', $trInfo->tax_class_id));
      $contents[] = array('text' => '<br>' . TEXT_INFO_ZONE_NAME . '<br>' . xtc_geo_zones_pull_down('name="tax_zone_id" style="font-size:10px"', $trInfo->geo_zone_id));
      $contents[] = array('text' => '<br>' . TEXT_INFO_TAX_RATE . '<br>' . xtc_draw_input_field('tax_rate', $trInfo->tax_rate));
      $contents[] = array('text' => '<br>' . TEXT_INFO_RATE_DESCRIPTION . '<br>' . xtc_draw_input_field('tax_description', $trInfo->tax_description));
      $contents[] = array('text' => '<br>' . TEXT_INFO_TAX_RATE_PRIORITY . '<br>' . xtc_draw_input_field('tax_priority', $trInfo->tax_priority));
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_UPDATE> . '&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_TAX_RATE . '</b>');

      $contents = array('form' => xtc_draw_form('rates', FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $trInfo->tax_class_title . ' ' . number_format($trInfo->tax_rate, TAX_DECIMAL_PLACES) . '%</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif')#" border="0" alt=IMAGE_DELETE> . '&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    default:
      if (is_object($trInfo)) {
        $heading[] = array('text' => '<b>' . $trInfo->tax_class_title . '</b>');
        $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=edit') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_TAX_RATES, 'page=' . $_GET['page'] . '&tID=' . $trInfo->tax_rates_id . '&action=delete') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE); . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_DATE_ADDED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$trInfo->date_added)));
        $contents[] = array('text' => '' . TEXT_INFO_LAST_MODIFIED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$trInfo->last_modified)));
        $contents[] = array('text' => '<br>' . TEXT_INFO_RATE_DESCRIPTION . '<br>' . $trInfo->tax_description);
      }
      break;
  }

  if ( (xarModAPIFunc('commerce','user','not_null',array('arg' => $heading))) && (xarModAPIFunc('commerce','user','not_null',array('arg' => $contents))) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
}
?>