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
function commerce_manufacturersblock_init()
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
function commerce_manufacturersblock_info()
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
function commerce_manufacturersblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}

//$box_content='';

  $manufacturers = array('manufacturers_id', 'manufacturers_name');
  $q = new xenQuery("SELECT",$xartables['commerce_manufacturers'],$manufacturers);
//FIXME  if ($q->getrows() <= MAX_DISPLAY_MANUFACTURERS_IN_A_LIST) {
  if ($q->getrows() <= 5) {
    $q->setorder('manufacturers_name');
    // Display a list
    $manufacturers_list = '';
      $q->run();
    while ($manufacturers = $q->output()) {
      $manufacturers_name = ((strlen($manufacturers['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr($manufacturers['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..' : $manufacturers['manufacturers_name']);
      if (isset($_GET['manufacturers_id']) && ($_GET['manufacturers_id'] == $manufacturers['manufacturers_id'])) $manufacturers_name = '<b>' . $manufacturers_name .'</b>';
      $manufacturers_list .= '<a href="' . xarModURL('commerce','user','default',array('manufacturers_id'  => $manufacturers['manufacturers_id'])) . '">' . $manufacturers_name . '</a><br>';
    }

  } else {
    // Display a drop-down
    $manufacturers_array = array();
    if (MAX_MANUFACTURERS_LIST < 2) {
      $manufacturers_array[] = array('id' => '', 'text' => PULL_DOWN_DEFAULT);
    }

      $q = new xenQuery();
      $q->run();
    while ($manufacturers = $q->output()) {
      $manufacturers_name = ((strlen($manufacturers['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? substr($manufacturers['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..' : $manufacturers['manufacturers_name']);
      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
                                     'text' => $manufacturers_name);
    }

  }




if ($manufacturers['manufacturers_name']=='') return;

$box_content=xtc_draw_form('manufacturers', xarModURL('commerce','user','default', '', 'NONSSL', false), 'get').commerce_userapi_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $_GET['manufacturers_id'], 'onChange="this.form.submit();" size="' . MAX_MANUFACTURERS_LIST . '" style="width: 100%"') .
xtc_hide_session_id().'</form>';


    $box_smarty->assign('BOX_TITLE', BOX_HEADING_MANUFACTURERS);
    $box_smarty->assign('BOX_CONTENT', $box_content);

    $box_smarty->assign('language', $_SESSION['language']);
/*          // set cache ID
  if (USE_CACHE=='false') {
  $box_smarty->caching = 0;
  $box_manufacturers= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_manufacturers.html');
  } else {
  $box_smarty->caching = 1;
  $box_smarty->cache_lifetime=CACHE_LIFETIME;
  $box_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'].$_GET['manufacturers_id'];
  $box_manufacturers= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_manufacturers.html',$cache_id);
  }
*/
    $blockinfo['content'] = $data;
    return $blockinfo;
}
?>