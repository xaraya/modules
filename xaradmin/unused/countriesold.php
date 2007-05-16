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

function commerce_admin_countries()
{


  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'insert':
        $countries_name = xtc_db_prepare_input($_POST['countries_name']);
        $countries_iso_code_2 = xtc_db_prepare_input($_POST['countries_iso_code_2']);
        $countries_iso_code_3 = xtc_db_prepare_input($_POST['countries_iso_code_3']);
        $address_format_id = xtc_db_prepare_input($_POST['address_format_id']);

        new xenQuery("insert into " . TABLE_COUNTRIES . " (countries_name, countries_iso_code_2, countries_iso_code_3, address_format_id) values ('" . xtc_db_input($countries_name) . "', '" . xtc_db_input($countries_iso_code_2) . "', '" . xtc_db_input($countries_iso_code_3) . "', '" . xtc_db_input($address_format_id) . "')");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_COUNTRIES));
        break;
      case 'save':
        $countries_id = xtc_db_prepare_input($_GET['cID']);
        $countries_name = xtc_db_prepare_input($_POST['countries_name']);
        $countries_iso_code_2 = xtc_db_prepare_input($_POST['countries_iso_code_2']);
        $countries_iso_code_3 = xtc_db_prepare_input($_POST['countries_iso_code_3']);
        $address_format_id = xtc_db_prepare_input($_POST['address_format_id']);

        new xenQuery("update " . TABLE_COUNTRIES . " set countries_name = '" . xtc_db_input($countries_name) . "', countries_iso_code_2 = '" . xtc_db_input($countries_iso_code_2) . "', countries_iso_code_3 = '" . xtc_db_input($countries_iso_code_3) . "', address_format_id = '" . xtc_db_input($address_format_id) . "' where countries_id = '" . xtc_db_input($countries_id) . "'");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries_id));
        break;
      case 'deleteconfirm':
        $countries_id = xtc_db_prepare_input($_GET['cID']);

        new xenQuery("delete from " . TABLE_COUNTRIES . " where countries_id = '" . xtc_db_input($countries_id) . "'");
        xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_COUNTRIES, 'page=' . $_GET['page']));
        break;
    }
  }
  $countries_query_raw = "select countries_id, countries_name, countries_iso_code_2, countries_iso_code_3, address_format_id from " . TABLE_COUNTRIES . " order by countries_name";
  $countries_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $countries_query_raw, $countries_query_numrows);
  $countries_query = new xenQuery($countries_query_raw);
      $q = new xenQuery();
      if(!$q->run()) return;
  while ($countries = $q->output()) {
    if (((!$_GET['cID']) || (@$_GET['cID'] == $countries['countries_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($countries);
    }

    if ( (is_object($cInfo)) && ($countries['countries_id'] == $cInfo->countries_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries['countries_id']) . '\'">' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $countries['countries_name']; ?></td>
                <td class="dataTableContent" align="center" width="40"><?php echo $countries['countries_iso_code_2']; ?></td>
                <td class="dataTableContent" align="center" width="40"><?php echo $countries['countries_iso_code_3']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($countries['countries_id'] == $cInfo->countries_id) ) { echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_arrow_right.gif'), ''); } else { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $countries['countries_id']) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&#160;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $countries_split->display_count($countries_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_COUNTRIES); ?></td>
                    <td class="smallText" align="right"><?php echo $countries_split->display_links($countries_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td colspan="2" align="right"><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&action=new') . '">' .
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_new_country.gif'),'alt' => IMAGE_NEW_COUNTRY);
                    </a>'; ?></td>
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
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_COUNTRY . '</b>');

      $contents = array('form' => xtc_draw_form('countries', FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_NAME . '<br>' . xtc_draw_input_field('countries_name'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_CODE_2 . '<br>' . xtc_draw_input_field('countries_iso_code_2'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_CODE_3 . '<br>' . xtc_draw_input_field('countries_iso_code_3'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_ADDRESS_FORMAT . '<br>' . commerce_userapi_draw_pull_down_menu('address_format_id', xtc_get_address_formats()));
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_insert.gif')#" border="0" alt=IMAGE_INSERT> . '&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_COUNTRIES, 'page=' . $_GET['page']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_COUNTRY . '</b>');

      $contents = array('form' => xtc_draw_form('countries', FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_NAME . '<br>' . xtc_draw_input_field('countries_name', $cInfo->countries_name));
      $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_CODE_2 . '<br>' . xtc_draw_input_field('countries_iso_code_2', $cInfo->countries_iso_code_2));
      $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_CODE_3 . '<br>' . xtc_draw_input_field('countries_iso_code_3', $cInfo->countries_iso_code_3));
      $contents[] = array('text' => '<br>' . TEXT_INFO_ADDRESS_FORMAT . '<br>' . commerce_userapi_draw_pull_down_menu('address_format_id', xtc_get_address_formats(), $cInfo->address_format_id));
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_UPDATE> . '&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_COUNTRY . '</b>');

      $contents = array('form' => xtc_draw_form('countries', FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $cInfo->countries_name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' .
<input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif')#" border="0" alt=IMAGE_DELETE>
. '&#160;<a href="' . xarModURL('commerce','admin',(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;
    default:
      if (is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . $cInfo->countries_name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=edit') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_COUNTRIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->countries_id . '&action=delete') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE); . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_NAME . '<br>' . $cInfo->countries_name);
        $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_CODE_2 . ' ' . $cInfo->countries_iso_code_2);
        $contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_CODE_3 . ' ' . $cInfo->countries_iso_code_3);
        $contents[] = array('text' => '<br>' . TEXT_INFO_ADDRESS_FORMAT . ' ' . $cInfo->address_format_id);
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