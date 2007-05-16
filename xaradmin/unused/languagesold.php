<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003-4 Xaraya
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

function commerce_admin_languages($args)
{
//

  switch ($_GET['action']) {
    case 'insert':
      $name = xtc_db_prepare_input($_POST['name']);
      $code = xtc_db_prepare_input($_POST['code']);
      $image = xtc_db_prepare_input($_POST['image']);
      $directory = xtc_db_prepare_input($_POST['directory']);
      $sort_order = xtc_db_prepare_input($_POST['sort_order']);
      $charset = xtc_db_prepare_input($_POST['charset']);

      new xenQuery("insert into " . TABLE_LANGUAGES . " (name, code, image, directory, sort_order,language_charset) values ('" . xtc_db_input($name) . "', '" . xtc_db_input($code) . "', '" . xtc_db_input($image) . "', '" . xtc_db_input($directory) . "', '" . xtc_db_input($sort_order) . "', '" . xtc_db_input($charset) . "')");
      $insert_id = xtc_db_insert_id();

      // create additional categories_description records
      $categories_query = new xenQuery("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c left join " . TABLE_CATEGORIES_DESCRIPTION . " cd on c.categories_id = cd.categories_id where cd.language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($categories = $q->output()) {
        new xenQuery("insert into " . TABLE_CATEGORIES_DESCRIPTION . " (categories_id, language_id, categories_name) values ('" . $categories['categories_id'] . "', '" . $insert_id . "', '" . xtc_db_input($categories['categories_name']) . "')");
      }

      // create additional products_description records
      $products_query = new xenQuery("select p.products_id, pd.products_name, pd.products_description, pd.products_url from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id where pd.language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($products = $q->output()) {
        new xenQuery("insert into " . TABLE_PRODUCTS_DESCRIPTION . " (products_id, language_id, products_name, products_description, products_url) values ('" . $products['products_id'] . "', '" . $insert_id . "', '" . xtc_db_input($products['products_name']) . "', '" . xtc_db_input($products['products_description']) . "', '" . xtc_db_input($products['products_url']) . "')");
      }

      // create additional products_options records
      $products_options_query = new xenQuery("select products_options_id, products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($products_options = $q->output()) {
        new xenQuery("insert into " . TABLE_PRODUCTS_OPTIONS . " (products_options_id, language_id, products_options_name) values ('" . $products_options['products_options_id'] . "', '" . $insert_id . "', '" . xtc_db_input($products_options['products_options_name']) . "')");
      }

      // create additional products_options_values records
      $products_options_values_query = new xenQuery("select products_options_values_id, products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($products_options_values = $q->output()) {
        new xenQuery("insert into " . TABLE_PRODUCTS_OPTIONS_VALUES . " (products_options_values_id, language_id, products_options_values_name) values ('" . $products_options_values['products_options_values_id'] . "', '" . $insert_id . "', '" . xtc_db_input($products_options_values['products_options_values_name']) . "')");
      }

      // create additional manufacturers_info records
      $manufacturers_query = new xenQuery("select m.manufacturers_id, mi.manufacturers_url from " . TABLE_MANUFACTURERS . " m left join " . TABLE_MANUFACTURERS_INFO . " mi on m.manufacturers_id = mi.manufacturers_id where mi.languages_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($manufacturers = $q->output()) {
        new xenQuery("insert into " . TABLE_MANUFACTURERS_INFO . " (manufacturers_id, languages_id, manufacturers_url) values ('" . $manufacturers['manufacturers_id'] . "', '" . $insert_id . "', '" . xtc_db_input($manufacturers['manufacturers_url']) . "')");
      }

      // create additional orders_status records
      $orders_status_query = new xenQuery("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $_SESSION['languages_id'] . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($orders_status = $q->output()) {
        new xenQuery("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('" . $orders_status['orders_status_id'] . "', '" . $insert_id . "', '" . xtc_db_input($orders_status['orders_status_name']) . "')");
      }

      // create additional customers status
            $customers_status_query=new xenQuery("SELECT DISTINCT customers_status_id
                            FROM ".TABLE_CUSTOMERS_STATUS);
      $q = new xenQuery();
      if(!$q->run()) return;
      while ($data=$q->output()) {

      $customers_status_data_query=new xenQuery("SELECT *
                            FROM ".TABLE_CUSTOMERS_STATUS."
                            WHERE customers_status_id='".$data['customers_status_id']."'");

      $q = new xenQuery();
      if(!$q->run()) return;
      $group_data=$q->output();
        $q->addfield('customers_status_id',$data['customers_status_id']);
        $q->addfield('language_id',$insert_id);
        $q->addfield('customers_status_name',$group_data['customers_status_name']);
        $q->addfield('customers_status_public',$group_data['customers_status_public']);
        $q->addfield('customers_status_image',$group_data['customers_status_image']);
        $q->addfield('customers_status_discount',$group_data['customers_status_discount']);
        $q->addfield('customers_status_ot_discount_flag',$group_data['customers_status_ot_discount_flag']);
        $q->addfield('customers_status_ot_discount',$group_data['customers_status_ot_discount']);
        $q->addfield('customers_status_graduated_prices',$group_data['customers_status_graduated_prices']);
        $q->addfield('customers_status_show_price',$group_data['customers_status_show_price']);
        $q->addfield('customers_status_show_price_tax',$group_data['customers_status_show_price_tax']);
        $q->addfield('customers_status_add_tax_ot',$group_data['customers_status_add_tax_ot']);
        $q->addfield('customers_status_payment_unallowed',$group_data['customers_status_payment_unallowed']);
        $q->addfield('customers_status_shipping_unallowed',$group_data['customers_status_shipping_unallowed']);
        $q->addfield('customers_status_discount_attributes',$group_data['customers_status_discount_attributes']);

    xtc_db_perform(TABLE_CUSTOMERS_STATUS, $c_data);

    }

      if ($_POST['default'] == 'on') {
        new xenQuery("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($code) . "' where configuration_key = 'DEFAULT_LANGUAGE'");
      }

      xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $insert_id));
      break;

    case 'save':
      $lID = xtc_db_prepare_input($_GET['lID']);
      $name = xtc_db_prepare_input($_POST['name']);
      $code = xtc_db_prepare_input($_POST['code']);
      $image = xtc_db_prepare_input($_POST['image']);
      $directory = xtc_db_prepare_input($_POST['directory']);
      $sort_order = xtc_db_prepare_input($_POST['sort_order']);
     $charset = xtc_db_prepare_input($_POST['charset']);

      new xenQuery("update " . TABLE_LANGUAGES . " set name = '" . xtc_db_input($name) . "', code = '" . xtc_db_input($code) . "', image = '" . xtc_db_input($image) . "', directory = '" . xtc_db_input($directory) . "', sort_order = '" . xtc_db_input($sort_order) . "', language_charset = '" . xtc_db_input($charset) . "' where languages_id = '" . xtc_db_input($lID) . "'");

      if ($_POST['default'] == 'on') {
        new xenQuery("update " . TABLE_CONFIGURATION . " set configuration_value = '" . xtc_db_input($code) . "' where configuration_key = 'DEFAULT_LANGUAGE'");
      }

      xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $_GET['lID']));
      break;

    case 'deleteconfirm':
      $lID = xtc_db_prepare_input($_GET['lID']);

      $lng_query = new xenQuery("select languages_id from " . TABLE_LANGUAGES . " where code = '" . DEFAULT_CURRENCY . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $lng = $q->output();
      if ($lng['languages_id'] == $lID) {
        new xenQuery("update " . TABLE_CONFIGURATION . " set configuration_value = '' where configuration_key = 'DEFAULT_CURRENCY'");
      }

      new xenQuery("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where language_id = '" . xtc_db_input($lID) . "'");
      new xenQuery("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where language_id = '" . xtc_db_input($lID) . "'");
      new xenQuery("delete from " . TABLE_PRODUCTS_OPTIONS . " where language_id = '" . xtc_db_input($lID) . "'");
      new xenQuery("delete from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where language_id = '" . xtc_db_input($lID) . "'");
      new xenQuery("delete from " . TABLE_MANUFACTURERS_INFO . " where languages_id = '" . xtc_db_input($lID) . "'");
      new xenQuery("delete from " . TABLE_ORDERS_STATUS . " where language_id = '" . xtc_db_input($lID) . "'");
      new xenQuery("delete from " . TABLE_LANGUAGES . " where languages_id = '" . xtc_db_input($lID) . "'");
      new xenQuery("delete from " . TABLE_CONTENT_MANAGER . " where languages_id = '" . xtc_db_input($lID) . "'");
      new xenQuery("delete from " . TABLE_PRODUCTS_CONTENT . " where languages_id = '" . xtc_db_input($lID) . "'");
      new xenQuery("delete from " . TABLE_CUSTOMERS_STATUS . " where language_id = '" . xtc_db_input($lID) . "'");

      xarRedirectResponse(xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page']));
      break;

    case 'delete':
      $lID = xtc_db_prepare_input($_GET['lID']);

      $lng_query = new xenQuery("select code from " . TABLE_LANGUAGES . " where languages_id = '" . xtc_db_input($lID) . "'");
      $q = new xenQuery();
      if(!$q->run()) return;
      $lng = $q->output();

      $remove_language = true;
      if ($lng['code'] == DEFAULT_LANGUAGE) {
        $remove_language = false;
        $messageStack->add(ERROR_REMOVE_DEFAULT_LANGUAGE, 'error');
      }
      break;
  }

  $languages_query_raw = "select languages_id, name, code, image, directory, sort_order,language_charset from " . TABLE_LANGUAGES . " order by sort_order";
  $languages_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $languages_query_raw, $languages_query_numrows);
  $languages_query = new xenQuery($languages_query_raw);

      $q = new xenQuery();
      if(!$q->run()) return;
  while ($languages = $q->output()) {
    if (((!$_GET['lID']) || (@$_GET['lID'] == $languages['languages_id'])) && (!$lInfo) && (substr($_GET['action'], 0, 3) != 'new')) {
      $lInfo = new objectInfo($languages);
    }

    if ( (is_object($lInfo)) && ($languages['languages_id'] == $lInfo->languages_id) ) {
      echo '                  <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=edit') . '\'">' . "\n";
    } else {
      echo '                  <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $languages['languages_id']) . '\'">' . "\n";
    }

    if (DEFAULT_LANGUAGE == $languages['code']) {
      echo '                <td class="dataTableContent"><b>' . $languages['name'] . ' (' . TEXT_DEFAULT . ')</b></td>' . "\n";
    } else {
      echo '                <td class="dataTableContent">' . $languages['name'] . '</td>' . "\n";
    }
?>
                <td class="dataTableContent"><?php echo $languages['code']; ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($lInfo)) && ($languages['languages_id'] == $lInfo->languages_id) ) { echo xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $languages['languages_id']) . '">' . xtc_image(xarTplGetImage(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&#160;</td>
              </tr>
<?php
  }
?>
              <tr>
                <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $languages_split->display_count($languages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LANGUAGES); ?></td>
                    <td class="smallText" align="right"><?php echo $languages_split->display_links($languages_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                  </tr>
<?php
  if (!$_GET['action']) {
?>
                  <tr>
                    <td align="right" colspan="2"><?php echo '<a href="' . xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=new') . '">' .
  xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_new_language.gif'),'alt' => IMAGE_NEW_LANGUAGE);
                    </a>'; ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $direction_options = array( array('id' => '', 'text' => TEXT_INFO_LANGUAGE_DIRECTION_DEFAULT),
                              array('id' => 'ltr', 'text' => TEXT_INFO_LANGUAGE_DIRECTION_LEFT_TO_RIGHT),
                              array('id' => 'rtl', 'text' => TEXT_INFO_LANGUAGE_DIRECTION_RIGHT_TO_LEFT));

  $heading = array();
  $contents = array();
  switch ($_GET['action']) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_LANGUAGE . '</b>');

      $contents = array('form' => xtc_draw_form('languages', FILENAME_LANGUAGES, 'action=insert'));
      $contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_NAME . '<br>' . xtc_draw_input_field('name'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_CODE . '<br>' . xtc_draw_input_field('code'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_CHARSET . '<br>' . xtc_draw_input_field('charset'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_IMAGE . '<br>' . xtc_draw_input_field('image', 'icon.gif'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_DIRECTORY . '<br>' . xtc_draw_input_field('directory'));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_SORT_ORDER . '<br>' . xtc_draw_input_field('sort_order'));
      $contents[] = array('text' => '<br>' . xtc_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_insert.gif')#" border="0" alt=IMAGE_INSERT> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $_GET['lID']) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_LANGUAGE . '</b>');

      $contents = array('form' => xtc_draw_form('languages', FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=save'));
      $contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_NAME . '<br>' . xtc_draw_input_field('name', $lInfo->name));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_CODE . '<br>' . xtc_draw_input_field('code', $lInfo->code));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_CHARSET . '<br>' . xtc_draw_input_field('charset', $lInfo->language_charset));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_IMAGE . '<br>' . xtc_draw_input_field('image', $lInfo->image));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_DIRECTORY . '<br>' . xtc_draw_input_field('directory', $lInfo->directory));
      $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_SORT_ORDER . '<br>' . xtc_draw_input_field('sort_order', $lInfo->sort_order));
      if (DEFAULT_LANGUAGE != $lInfo->code) $contents[] = array('text' => '<br>' . xtc_draw_checkbox_field('default') . ' ' . TEXT_SET_DEFAULT);
      $contents[] = array('align' => 'center', 'text' => '<br>' . <input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_update.gif')#" border="0" alt=IMAGE_UPDATE> . ' <a href="' . xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_LANGUAGE . '</b>');

      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $lInfo->name . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' . (($remove_language) ? '<a href="' . xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=deleteconfirm') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE); . '</a>' : '') . ' <a href="' . xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id) . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_cancel.gif'),'alt' => IMAGE_CANCEL); . '</a>');
      break;

    default:
      if (is_object($lInfo)) {
        $heading[] = array('text' => '<b>' . $lInfo->name . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=edit') . '">' . xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_edit.gif'),'alt' => IMAGE_EDIT); . '</a> <a href="' . xarModURL('commerce','admin',(FILENAME_LANGUAGES, 'page=' . $_GET['page'] . '&lID=' . $lInfo->languages_id . '&action=delete') . '">' .
        xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_delete.gif'),'alt' => IMAGE_DELETE);
        </a> <a href="' . xarModURL('commerce','admin',(FILENAME_DEFINE_LANGUAGE, 'lngdir=' . $lInfo->directory) . '">' .
        xarModAPIFunc('commerce','user','image',array('src' => xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_details.gif'),'alt' => IMAGE_DETAILS);
        </a>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_NAME . ' ' . $lInfo->name);
        $contents[] = array('text' => TEXT_INFO_LANGUAGE_CODE . ' ' . $lInfo->code);
        $contents[] = array('text' => TEXT_INFO_LANGUAGE_CHARSET_INFO . ' ' . $lInfo->language_charset);

        $contents[] = array('text' => '<br>' . xtc_image(xarTplGetImage(DIR_WS_LANGUAGES . $lInfo->directory . '/' . $lInfo->image), $lInfo->name));
        $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_DIRECTORY . '<br>' . DIR_WS_LANGUAGES . '<b>' . $lInfo->directory . '</b>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_LANGUAGE_SORT_ORDER . ' ' . $lInfo->sort_order);
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