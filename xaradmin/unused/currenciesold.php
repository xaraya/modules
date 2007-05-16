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

function commerce_admin_currencies()
{


  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

  if ($_GET['action']) {
    switch ($_GET['action']) {
      case 'insert':
      case 'save':
        $currency_id = xtc_db_prepare_input($_GET['cID']);
        $title = xtc_db_prepare_input($_POST['title']);
        $code = xtc_db_prepare_input($_POST['code']);
        $symbol_left = xtc_db_prepare_input($_POST['symbol_left']);
        $symbol_right = xtc_db_prepare_input($_POST['symbol_right']);
        $decimal_point = xtc_db_prepare_input($_POST['decimal_point']);
        $thousands_point = xtc_db_prepare_input($_POST['thousands_point']);
        $decimal_places = xtc_db_prepare_input($_POST['decimal_places']);
        $value = xtc_db_prepare_input($_POST['value']);

        $q->addfield('title',$title,
                                $q->addfield('code',$code);
                                $q->addfield('symbol_left',$symbol_left);
                                $q->addfield('symbol_right',$symbol_right);
                                $q->addfield('decimal_point',$decimal_point);
                                $q->addfield('thousands_point',$thousands_point);
                                $q->addfield('decimal_places',$decimal_places);
                                $q->addfield('value',$value);

        if ($_GET['action'] == 'insert') {
          xtc_db_perform(TABLE_CURRENCIES, $sql_data_array);
          $currency_id = xtc_db_insert_id();
        } elseif ($_GET['action'] == 'save') {
          xtc_db_perform(TABLE_CURRENCIES, $sql_data_array, 'update', "currencies_id = '" . xtc_db_input($currency_id) . "'");
        }

        if ($_POST['default'] == 'on') {
            $q = new xenQuery('UPDATE', $xartables['commerce_configuration]);
            $q->addfield('configuration_value',xtc_db_input($code));
            $q->eq('configuration_key','DEFAULT_CURRENCY');
            if(!$q->run()) return;
        }
        xarRedirectResponse(xarModURL('commerce','admin','currencies',array('page' => $_GET['page'] ,'cID' => $currency_id)));
        break;

      case 'deleteconfirm':
        $currencies_id = xtc_db_prepare_input($_GET['cID']);
        $q = new xenQuery('SELECT', $xartables['commerce_configuration],array('currencies_id'));
        $q->eq('code','DEFAULT_CURRENCY');
        if(!$q->run()) return;
        $currency = $q->output();
        if ($currency['currencies_id'] == $currencies_id) {
            $q = new xenQuery('UPDATE', $xartables['commerce_configuration]);
            $q->addfield('configuration_value','');
            $q->eq('configuration_key','DEFAULT_CURRENCY');
            if(!$q->run()) return;
        }

        $q = new xenQuery('DELETE', $xartables['commerce_configuration]);
        $q->eq('currencies_id',xtc_db_input($currencies_id));
        if(!$q->run()) return;

        xarRedirectResponse(xarModURL('commerce','admin','currencies',array('page' => $_GET['page'])));
        break;

      case 'update':
        $q = new xenQuery('SELECT', $xartables['commerce_configuration],array('currencies_id','code', 'title'));
        if(!$q->run()) return;

        while ($currency = $q->output()) {
          $quote_function = 'quote_' . CURRENCY_SERVER_PRIMARY . '_currency';
          $rate = $quote_function($currency['code']);
          if ( (!$rate) && (CURRENCY_SERVER_BACKUP != '') ) {
            $quote_function = 'quote_' . CURRENCY_SERVER_BACKUP . '_currency';
            $rate = $quote_function($currency['code']);
          }
          if ($rate) {
            $q = new xenQuery('UPDATE', $xartables['commerce_configuration]);
            $q->addfield('value',$rate);
            $q->addfield('last_updated',now());
            $q->eq('currencies_id',$currency['currencies_id']);
            if(!$q->run()) return;
            $messageStack->add_session(sprintf(TEXT_INFO_CURRENCY_UPDATED, $currency['title'], $currency['code']), 'success');
          } else {
            $messageStack->add_session(sprintf(ERROR_CURRENCY_INVALID, $currency['title'], $currency['code']), 'error');
          }
        }
        xarRedirectResponse(xarModURL('commerce','admin','currencies',array('page' => $_GET['page'] ,'cID' => $_GET['cID'])));
        break;

      case 'delete':
        $currencies_id = xtc_db_prepare_input($_GET['cID']);

        $q = new xenQuery('SELECT', $xartables['commerce_configuration],array('code'));
        $q->eq('currencies_id',xtc_db_input($currencies_id));
        if(!$q->run()) return;
        $currency = $q->output();

        $remove_currency = true;
        if ($currency['code'] == DEFAULT_CURRENCY) {
          $remove_currency = false;
          $messageStack->add(ERROR_REMOVE_DEFAULT_CURRENCY, 'error');
        }
        break;
    }
  }

  $currency_query_raw = "select currencies_id, title, code, symbol_left, symbol_right, decimal_point, thousands_point, decimal_places, last_updated, value from " . TABLE_CURRENCIES . " order by title";
  $currency_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $currency_query_raw, $currency_query_numrows);
  $currency_query = new xenQuery($currency_query_raw);
      $q = new xenQuery();
      if(!$q->run()) return;
  while ($currency = $q->output()) {
    if (((!$_GET['cID']) || (@$_GET['cID'] == $currency['currencies_id'])) && (!$cInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($currency);
    }

    if ( (is_object($cInfo)) && ($currency['currencies_id'] == $cInfo->currencies_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $currency['currencies_id']) . '\'">' . "\n";
    }

    if (DEFAULT_CURRENCY == $currency['code']) {
      echo '                <td class="dataTableContent"><b>' . $currency['title'] . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
    } else {
      echo '                <td class="dataTableContent">' . $currency['title'] . '</td>' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $currency['code']; ?></td>
                <td class="dataTableContent" align="right"><?php echo number_format($currency['value'], 8); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($currency['currencies_id'] == $cInfo->currencies_id) ) { echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $currency['currencies_id']) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&#160;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $currency_split->display_count($currency_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CURRENCIES); ?></td>
                    <td class="smallText" align="right"><?php echo $currency_split->display_links($currency_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td><?php if (CURRENCY_SERVER_PRIMARY) { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=update') . '">' .
'</a>'; } ?>
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_update_currencies.gif'),'alt' => IMAGE_UPDATE_CURRENCIES);
</td>
                    <td align="right"><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=new') . '">' .
xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_new_currency.gif'),'alt' => IMAGE_NEW_CURRENCY);
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
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CURRENCY . '</b>');

      $contents = array('form' => xtc_draw_form('currencies', FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_TITLE . '<br>' . xtc_draw_input_field('title'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_CODE . '<br>' . xtc_draw_input_field('code'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . '<br>' . xtc_draw_input_field('symbol_left'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '<br>' . xtc_draw_input_field('symbol_right'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_DECIMAL_POINT . '<br>' . xtc_draw_input_field('decimal_point'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_THOUSANDS_POINT . '<br>' . xtc_draw_input_field('thousands_point'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_DECIMAL_PLACES . '<br>' . xtc_draw_input_field('decimal_places'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_VALUE . '<br>' . xtc_draw_input_field('value'));
      $contents[] = array('text' => '<br>' . xtc_draw_checkbox_field('default') . ' ' . TEXT_INFO_SET_AS_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_insert.gif')#" border="0" alt=IMAGE_INSERT> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $_GET['cID']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CURRENCY . '</b>');

      $contents = array('form' => xtc_draw_form('currencies', FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_TITLE . '<br>' . xtc_draw_input_field('title', $cInfo->title));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_CODE . '<br>' . xtc_draw_input_field('code', $cInfo->code));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . '<br>' . xtc_draw_input_field('symbol_left', $cInfo->symbol_left));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_SYMBOL_RIGHT . '<br>' . xtc_draw_input_field('symbol_right', $cInfo->symbol_right));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_DECIMAL_POINT . '<br>' . xtc_draw_input_field('decimal_point', $cInfo->decimal_point));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_THOUSANDS_POINT . '<br>' . xtc_draw_input_field('thousands_point', $cInfo->thousands_point));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_DECIMAL_PLACES . '<br>' . xtc_draw_input_field('decimal_places', $cInfo->decimal_places));
      $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_VALUE . '<br>' . xtc_draw_input_field('value', $cInfo->value));
      if (DEFAULT_CURRENCY != $cInfo->code) $contents[] = array('text' => '<br>' . xtc_draw_checkbox_field('default') . ' ' . TEXT_INFO_SET_AS_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_UPDATE> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CURRENCY . '</b>');

      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $cInfo->title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . (($remove_currency) ? '<a href="' . xarModURL('commerce','admin',(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=deleteconfirm') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE); . '</a>' : '') . ' <a href="' . xarModURL('commerce','admin',(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    default:
      if (is_object($cInfo)) {
        $heading[] = array('text' => '<b>' . $cInfo->title . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=edit') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_CURRENCIES, 'page=' . $_GET['page'] . '&cID=' . $cInfo->currencies_id . '&action=delete') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE); . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_TITLE . ' ' . $cInfo->title);
        $contents[] = array('text' => TEXT_INFO_CURRENCY_CODE . ' ' . $cInfo->code);
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_SYMBOL_LEFT . ' ' . $cInfo->symbol_left);
        $contents[] = array('text' => TEXT_INFO_CURRENCY_SYMBOL_RIGHT . ' ' . $cInfo->symbol_right);
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_DECIMAL_POINT . ' ' . $cInfo->decimal_point);
        $contents[] = array('text' => TEXT_INFO_CURRENCY_THOUSANDS_POINT . ' ' . $cInfo->thousands_point);
        $contents[] = array('text' => TEXT_INFO_CURRENCY_DECIMAL_PLACES . ' ' . $cInfo->decimal_places);
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_LAST_UPDATED . ' ' . xarModAPIFunc('commerce','user','date_short',array('raw_date' =>$cInfo->last_updated)));
        $contents[] = array('text' => TEXT_INFO_CURRENCY_VALUE . ' ' . number_format($cInfo->value, 8));
        $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENCY_EXAMPLE . '<br>' . $currencies->format('30', false, DEFAULT_CURRENCY) . ' = ' . $currencies->format('30', true, $cInfo->code));
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