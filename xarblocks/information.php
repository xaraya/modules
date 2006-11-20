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
function commerce_informationblock_init()
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
function commerce_informationblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Generic Content Block',
        'module' => 'commerce',
        'func_update' => 'commerce_information_update',
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
function commerce_informationblock_display($blockinfo)
{
    // Security Check
    if (!xarSecurityCheck('ViewCommerceBlocks', 0, 'Block', "content:$blockinfo[title]:All")) {return;}

    sys::import('modules.xen.xarclasses.xenquery');
    $xartables = xarDBGetTables();

    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    $currentlang = xarModAPIFunc('commerce','user','get_language',array('locale' => $data['language']));

    $content_string='';
    $q = new xenQuery('SELECT',$xartables['commerce_content_manager']);
    $q->addfields(array('content_id',
                        'categories_id',
                        'parent_id',
                        'content_title',
                        'content_group'));
    $q->eq('languages_id', $currentlang['id']);
    $q->eq('file_flag', 0);
    $q->eq('content_status', 1);
    $q->ne('content_group', 4);
    if(!$q->run()) return;

    foreach ($q->output() as $row) {
        $content_string .= '<a href="' . xarModURL('commerce','user','shop_content',array('coID' => $row['content_group'])) . '">' . $row['content_title'] . '</a><br>';
    }


    $data['contacturl'] = xarModURL('commerce','user','filename');
    $data['content_string'] = $content_string;

    $blockinfo['content'] = $data;
    return $blockinfo;
}
 ?>