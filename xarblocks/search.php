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
function commerce_searchblock_init()
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
function commerce_searchblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => 'commerce_search_update',
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
function commerce_searchblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}


$box_content='';

//    xtc_hide_session_id());
    $box_smarty->assign('INPUT_SEARCH',xtc_draw_input_field('keywords', '', 'size="10" maxlength="30" style="width: ' . (BOX_WIDTH-30) . 'px"'));
    $box_smarty->assign('BUTTON_SUBMIT',
    <input type="image" src="#xarTplGetImage('buttons/' . xarSessionGetVar('language') . '/button_quick_find.gif')#" border="0" alt=BOX_HEADING_SEARCH>.
);
    $box_smarty->assign('LINK_ADVANCED',xarModURL('commerce','user',(FILENAME_ADVANCED_SEARCH));
    $box_smarty->assign('BOX_CONTENT', $box_content);

    $box_smarty->assign('language', $_SESSION['language']);
/*
    // set cache ID
  if (USE_CACHE=='false') {
  $box_smarty->caching = 0;
  $box_search= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_search.html');
  } else {
  $box_smarty->caching = 1;
  $box_smarty->cache_lifetime=CACHE_LIFETIME;
  $box_smarty->cache_modified_check=CACHE_CHECK;
  $cache_id = $_SESSION['language'];
  $box_search= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_search.html',$cache_id);
  }
*/
    $blockinfo['content'] = $data;
    return $blockinfo;
}
?>