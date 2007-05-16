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

   // create smarty elements
//  $smarty = new Smarty;
  // include boxes
  require(DIR_WS_INCLUDES.'boxes.php');
  // include needed functions
  require_once(DIR_FS_INC . 'xtc_draw_checkbox_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_selection_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_categories.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_manufacturers.inc.php');
  require_once(DIR_FS_INC . 'xtc_draw_checkbox_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_checkdate.inc.php');


  $breadcrumb->add(NAVBAR_TITLE_ADVANCED_SEARCH, xarModURL('commerce','user','advanced_search');

 require(DIR_WS_INCLUDES . 'header.php');

xtc_hide_session_id();


  $data['INPUT_KEYWORDS'] = xtc_draw_input_field('keywords', '', 'style="width: 100%"');
  $data['CHECKBOX_DESCRIPTION'] = xtc_draw_checkbox_field('search_in_description', '1');
  $data['HELP_LINK'] = 'javascript:popupWindow(\'' . xarModURL('commerce','user',(FILENAME_POPUP_SEARCH_HELP) . '\')';
  $data['BUTTON_SUBMIT'] =
<input type="image" src="#xarTplGetImage('buttons/' . xarSession::getVar('language') . '/'.'button_search.gif')#" border="0" alt=IMAGE_BUTTON_SEARCH>
  $options_box = '<table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n" .
                 '  <tr>' . "\n" .
                 '    <td class="fieldKey">' . ENTRY_CATEGORIES . '</td>' . "\n" .
                 '    <td class="fieldValue">' . commerce_userapi_draw_pull_down_menu('categories_id', xtc_get_categories(array(array('id' => '', 'text' => TEXT_ALL_CATEGORIES)))) . '<br></td>' . "\n" .
                 '  </tr>' . "\n" .
                 '  <tr>' . "\n" .
                 '    <td class="fieldKey">&#160;</td>' . "\n" .
                 '    <td class="smallText">' . xtc_draw_checkbox_field('inc_subcat', '1', true) . ' ' . ENTRY_INCLUDE_SUBCATEGORIES . '</td>' . "\n" .
                 '  </tr>' . "\n" .
                 '  <tr>' . "\n" .
                 '    <td colspan="2">' . xtc_draw_separator('pixel_trans.gif', '100%', '10') . '</td>' . "\n" .
                 '  </tr>' . "\n" .
                 '  <tr>' . "\n" .
                 '    <td class="fieldKey">' . ENTRY_MANUFACTURERS . '</td>' . "\n" .
                 '    <td class="fieldValue">' . commerce_userapi_draw_pull_down_menu('manufacturers_id', xtc_get_manufacturers(array(array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS)))) . '</td>' . "\n" .
                 '  </tr>' . "\n" .
                 '  <tr>' . "\n" .
                 '    <td colspan="2">' . xtc_draw_separator('pixel_trans.gif', '100%', '10') . '</td>' . "\n" .
                 '  </tr>' . "\n" .
                 '  <tr>' . "\n" .
                 '    <td class="fieldKey">' . ENTRY_PRICE_FROM . '</td>' . "\n" .
                 '    <td class="fieldValue">' . xtc_draw_input_field('pfrom') . '</td>' . "\n" .
                 '  </tr>' . "\n" .
                 '  <tr>' . "\n" .
                 '    <td class="fieldKey">' . ENTRY_PRICE_TO . '</td>' . "\n" .
                 '    <td class="fieldValue">' . xtc_draw_input_field('pto') . '</td>' . "\n" .
                 '  </tr>' . "\n" .
                 '  <tr>' . "\n" .
                 '</table>';
                 '    <td colspan="2">' . xtc_draw_separator('pixel_trans.gif', '100%', '10') . '</td>' . "\n" .
                 '  </tr>' . "\n" .
                 '  <tr>' . "\n" .
                 '    <td class="fieldKey">' . ENTRY_DATE_FROM . '</td>' . "\n" .
                 '    <td class="fieldValue">' . xtc_draw_input_field('dfrom', DOB_FORMAT_STRING, 'onFocus="RemoveFormatString(this, \'' . DOB_FORMAT_STRING . '\')"') . '</td>' . "\n" .
                 '  </tr>' . "\n" .
                 '  <tr>' . "\n" .
                 '    <td class="fieldKey">' . ENTRY_DATE_TO . '</td>' . "\n" .
                 '    <td class="fieldValue">' . xtc_draw_input_field('dto', DOB_FORMAT_STRING, 'onFocus="RemoveFormatString(this, \'' . DOB_FORMAT_STRING . '\')"') . '</td>' . "\n" .
                 '  </tr>' . "\n" .
                 '</table>';


$data['OPTIONS_BOX'] = $options_box;
$error='';
  if (isset($_GET['errorno'])) {
    if (($_GET['errorno'] & 1) == 1) {
      $error.= str_replace('\n', '<br>', JS_AT_LEAST_ONE_INPUT);
    }
    if (($_GET['errorno'] & 10) == 10) {
      $error.= str_replace('\n', '<br>', JS_INVALID_FROM_DATE);
    }
    if (($_GET['errorno'] & 100) == 100) {
      $error.= str_replace('\n', '<br>', JS_INVALID_TO_DATE);
    }
    if (($_GET['errorno'] & 1000) == 1000) {
      $error.= str_replace('\n', '<br>', JS_TO_DATE_LESS_THAN_FROM_DATE);
    }
    if (($_GET['errorno'] & 10000) == 10000) {
      $error.= str_replace('\n', '<br>', JS_PRICE_FROM_MUST_BE_NUM);
    }
    if (($_GET['errorno'] & 100000) == 100000) {
      $error.= str_replace('\n', '<br>', JS_PRICE_TO_MUST_BE_NUM);
    }
    if (($_GET['errorno'] & 1000000) == 1000000) {
      $error.= str_replace('\n', '<br>', JS_PRICE_TO_LESS_THAN_PRICE_FROM);
    }
    if (($_GET['errorno'] & 10000000) == 10000000) {
      $error.= str_replace('\n', '<br>', JS_INVALID_KEYWORDS);
    }
  }

  $data['error',$error;
  $data['language'] = $_SESSION['language'];

  $smarty->caching = 0;
  return data;
  ?>