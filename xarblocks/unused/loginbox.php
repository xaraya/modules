<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003-4 Xaraya
//  (c) 2003 XT-Commerce
//   Third Party contributions:
//   Loginbox V1.0            Aubrey Kilian <aubrey@mycon.co.za>
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

/**
 * Initialise the block
 */
function commerce_loginboxblock_init()
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
function commerce_loginboxblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => 'commerce_loginbox_update',
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
function commerce_loginboxblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}

    //$box_content='';

    if (xtc_session_is_registered('customer_id')) return;

    $loginboxcontent = '
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <form name="login" method="post" action="' . xarModURL('commerce','user','login', 'action=process', 'SSL') . '">
      <tr>
        <td align="left" class="main">' . BOX_LOGINBOX_EMAIL . '</td>
      </tr>
      <tr>
        <td align="left" class="main"><input type="text" name="email_address" maxlength="96" size="20" value=""></td>
      </tr>
      <tr>
        <td align="left" class="main">' . BOX_LOGINBOX_PASSWORD . '</td>
      </tr>
      <tr>
        <td align="left" class="main"><input type="password" name="password" maxlength="40" size="20" value=""></td>
      </tr>
      <tr>
        <td class="main" align="center">
        <input type="image" src="'. xarTplGetImage('buttons/' . xarSession::getVar('language') . '/' . 'button_login.gif') . '" border="0" alt=IMAGE_BUTTON_LOGIN>
        </td>
      </tr>
    </form></table>';

    $box_smarty->assign('BOX_TITLE', BOX_LOGINBOX_HEADING);
    $box_smarty->assign('BOX_CONTENT', $loginboxcontent);

    $box_smarty->caching = 0;
    $box_smarty->assign('language', $_SESSION['language']);
    $blockinfo['content'] = $data;
    return $blockinfo;
}

?>