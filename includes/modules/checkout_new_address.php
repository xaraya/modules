<?php
/* -----------------------------------------------------------------------------------------
   $Id: checkout_new_address.php,v 1.4 2003/12/30 09:02:31 fanta2k Exp $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_new_address.php,v 1.3 2003/05/19); www.oscommerce.com
   (c) 2003  nextcommerce (checkout_new_address.php,v 1.8 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
//$module_smarty=new Smarty;
$module_smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
  // include needed functions
  require_once(DIR_FS_INC . 'xtc_draw_radio_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_get_country_list.inc.php');

  if (!isset($process)) $process = false;

  if (ACCOUNT_GENDER == 'true') {
    $male = ($gender == 'm') ? true : false;
    $female = ($gender == 'f') ? true : false;
    $module_smarty->assign('gender','1');
    $module_smarty->assign('INPUT_MALE',xtc_draw_radio_field('gender', 'm', $male));
    $module_smarty->assign('INPUT_FEMALE',xtc_draw_radio_field('gender', 'f', $female)  . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_GENDER_TEXT)) ? '<span class="inputRequirement">' . ENTRY_GENDER_TEXT . '</span>': ''));

  }
  $module_smarty->assign('INPUT_FIRSTNAME',xtc_draw_input_field('firstname') . '&#160;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_FIRST_NAME_TEXT)) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>': ''));
  $module_smarty->assign('INPUT_LASTNAME',xtc_draw_input_field('lastname') . '&#160;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_LAST_NAME_TEXT)) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>': ''));

  if (ACCOUNT_COMPANY == 'true') {
  $module_smarty->assign('company','1');
  $module_smarty->assign('INPUT_COMPANY',xtc_draw_input_field('company') . '&#160;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_COMPANY_TEXT)) ? '<span class="inputRequirement">' . ENTRY_COMPANY_TEXT . '</span>': ''));

  }
  $module_smarty->assign('INPUT_STREET',xtc_draw_input_field('street_address') . '&#160;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_STREET_ADDRESS_TEXT)) ? '<span class="inputRequirement">' . ENTRY_STREET_ADDRESS_TEXT . '</span>': ''));

  if (ACCOUNT_SUBURB == 'true') {
  $module_smarty->assign('suburb','1');
  $module_smarty->assign('INPUT_SUBURB',xtc_draw_input_field('suburb') . '&#160;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_SUBURB_TEXT)) ? '<span class="inputRequirement">' . ENTRY_SUBURB_TEXT . '</span>': ''));

  }
  $module_smarty->assign('INPUT_CODE',xtc_draw_input_field('postcode') . '&#160;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_POST_CODE_TEXT)) ? '<span class="inputRequirement">' . ENTRY_POST_CODE_TEXT . '</span>': ''));
  $module_smarty->assign('INPUT_CITY',xtc_draw_input_field('city') . '&#160;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_CITY_TEXT)) ? '<span class="inputRequirement">' . ENTRY_CITY_TEXT . '</span>': ''));

  if (ACCOUNT_STATE == 'true') {
  $module_smarty->assign('state','1');

    if ($process == true) {
      if ($entry_state_has_zones == true) {
        $zones_array = array();
        $zones_query = new xenQuery("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . xtc_db_input($country) . "' order by zone_name");
        while ($zones_values = $q->output()      $q = new xenQuery();
      if(!$q->run()) return;
) {
          $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);
        }
        $entry_state = commerce_userapi_draw_pull_down_menu('state', $zones_array);
      } else {
        $entry_state =  xtc_draw_input_field('state');
      }
    } else {
      $entry_state =  xtc_draw_input_field('state');
    }

    if (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_STATE_TEXT))) $entry_state.=  '&#160;<span class="inputRequirement">' . ENTRY_STATE_TEXT;

$module_smarty->assign('INPUT_STATE',$entry_state);
  }
  $module_smarty->assign('SELECT_COUNTRY',xtc_get_country_list('country') . '&#160;' . (xarModAPIFunc('commerce','user','not_null',array('arg' => ENTRY_COUNTRY_TEXT))) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>': ''));

  $module_smarty->assign('language', $_SESSION['language']);

  $module_smarty->caching = 0;
  $module= $module_smarty->fetch(CURRENT_TEMPLATE.'/module/checkout_new_address.html');

  $smarty->assign('MODULE_new_address',$module);
?>