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

/**
 * Initialise the block
 */
function commerce_currenciesblock_init()
{
    return array(
        'content_text' => '',
        'content_type' => 'text',
        'expire' => 0,
        'hide_empty' => true,
        'custom_format' => '',
        'hide_errors' => true,
        'start_date' => '',
        'end_date' => ''
    );
}

/**
 * Get information on the block ($blockinfo array)
 */
function commerce_currenciesblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => '',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true,
        'notes' => "content_type can be 'text', 'html', 'php' or 'data'"
    );
}

/**
 * Display function
 * @param $blockinfo array
 * @returns $blockinfo array
 */
function commerce_currenciesblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}

    require_once 'modules/commerce/xarclasses/currencies.php';
    $currencies = new currencies();

  // include functions
//  require_once(DIR_FS_INC . 'xtc_hide_session_id.inc.php');
//  if (isset($currencies) && is_object($currencies)) {

    $count_cur='';
    reset($currencies->currencies);
    $currencies_array = array();
    while (list($key, $value) = each($currencies->currencies)) {
    $count_cur++;
      $currencies_array[] = array('id' => $key, 'text' => $value['title']);
    }

    $hidden_get_variables = array();
    reset($_GET);
    while (list($key, $value) = each($_GET)) {
      if ( ($key != 'currency') && ($key != xarSessionGetVar('name')) && ($key != 'x') && ($key != 'y') ) {
        $hidden_get_variables[] = array('name' => $key, 'value' =>$value);
      }
    }


//  }
  // dont show box if there's only 1 currency
  if (!isset($count_cur) || $count_cur < 1 ) return '';
//  $box_content=xtc_draw_form('currencies', xarModURL('commerce','user',(basename($PHP_SELF), '', $request_type, false), 'get').
  $dropdown = xarModAPIFunc('commerce','user','draw_pull_down_menu',array(
        'name' =>'currency',
        'values' => $currencies_array,
        'default' => xarSessionGetVar('currency'),
        'parameters' => 'onChange="this.form.submit();" style="width: 100%"'
        )
    );
//    $hidden_get_variables . xtc_hide_session_id().'</form>';

  $data['dropdown'] = $dropdown;
//  $data['language'] = $_SESSION['language'];
/*          // set cache ID
  if (USE_CACHE=='false') {
  $box_smarty->caching = 0;
  $box_currencies= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_currencies.html');
  } else {
  $box_smarty->caching = 1;
  $box_smarty->cache_lifetime=CACHE_LIFETIME;
  $box_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$_SESSION['currency'];
  $box_currencies= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_currencies.html',$cache_id);
  }
*/
    $blockinfo['content'] = $data;
    return $blockinfo;
}
 ?>