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
function commerce_languagesblockblock_init()
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
function commerce_languagesblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => 'commerce_languages_update',
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
function commerce_languagesblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}

  // include needed functions
  require_once(DIR_FS_INC . 'xtc_get_all_get_params.inc.php');



  if (!isset($lng) && !is_object($lng)) {
    include(DIR_WS_CLASSES . 'language.php');
    $lng = new language;
  }

  $languages_string = '';
  $count_lng='';
  reset($lng->catalog_languages);
  while (list($key, $value) = each($lng->catalog_languages)) {
  $count_lng++;
    $languages_string .= ' <a href="' . xarModURL('commerce','user',(basename($PHP_SELF), xtc_get_all_get_params(array('language', 'currency')) . 'language=' . $key, $request_type) . '">' . xtc_image(xarTplGetImage('lang/' .  $value['directory'] .'/' . $value['image']), $value['name']) . '</a> ';
  }

  // dont show box if there's only 1 language
  if ($count_lng < 2) return;

// $box_smarty = new smarty;

 $box_content='';
 $box_smarty->assign('BOX_TITLE', BOX_HEADING_LANGUAGES);
 $box_smarty->assign('BOX_CONTENT', $languages_string);
 $box_smarty->assign('language', $_SESSION['language']);


/*          // set cache ID
  if (USE_CACHE=='false') {
      $box_smarty->caching = 0;
      $box_languages= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_languages.html');
  } else {
      $box_smarty->caching = 1;
      $box_smarty->cache_lifetime=CACHE_LIFETIME;
      $box_smarty->cache_modified_check=CACHE_CHECK;
      $cache_id = $_SESSION['language'];
      $box_languages= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_languages.html',$cache_id);
  }
*/
    $blockinfo['content'] = $data;
    return $blockinfo;
}
   ?>