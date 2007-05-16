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
function commerce_tell_a_friendblock_init()
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
function commerce_tell_a_friendblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => 'commerce_tell_a_friend_update',
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
function commerce_tell_a_friendblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}


    $box_content='';
    // include needed functions
    require_once(DIR_FS_INC . 'xtc_draw_input_field.inc.php');

    $box_content =
        xtc_draw_form('tell_a_friend', xarModURL('commerce','user','tell_a_friend', '', 'NONSSL', false), 'get') .
        xtc_draw_input_field('send_to', '', 'size="10"') . '&#160;' .
        '<input type="image" src="#xarTplGetImage(\'buttons/\' . xarSession::getVar(\'language\') . \'/\'.\'button_tell_a_friend.gif\')#" border="0" alt=BOX_HEADING_TELL_A_FRIEND>' .
        xtc_draw_hidden_field('products_id', $_GET['products_id']) .
        xtc_hide_session_id() . '<br>' . BOX_TELL_A_FRIEND_TEXT.'</form>';

    $box_smarty->assign('BOX_CONTENT', $box_content);
    $box_smarty->assign('language', $_SESSION['language']);

    /*          // set cache ID
    if (USE_CACHE=='false') {
        $box_smarty->caching = 0;
        $box_tell_a_friend= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_tell_friend.html');
    } else {
        $box_smarty->caching = 1;
        $box_smarty->cache_lifetime=CACHE_LIFETIME;
        $box_smarty->cache_modified_check=CACHE_CHECK;
        $cache_id = $_SESSION['language'].$_GET['products_id'];
        $box_tell_a_friend= $box_smarty->fetch(CURRENT_TEMPLATE.'/boxes/box_tell_friend.html',$cache_id);
    }
    */

    $blockinfo['content'] = $data;
    return $blockinfo;
}

?>