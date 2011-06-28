<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/1015.html
 * @author potion <potion@xaraya.com>
 */ 
function content_utilapi_upgradepre092() {

    // First get the settings
    $default_ctype = xarModVars::get('content','default_ctype');
    $default_itemid = xarModVars::get('content','default_itemid');
    $default_main_page_tpl = xarModVars::get('content','default_main_page_tpl');
    $default_display_page_tpl = xarModVars::get('content','default_display_page_tpl');
    $default_view_page_tpl = xarModVars::get('content','default_view_page_tpl');
    $enable_filters = xarModVars::get('content','enable_filters');  
    $filters_min_ct_count = xarModVars::get('content','filters_min_ct_count');    
    $filters_min_item_count = xarModVars::get('content','filters_min_item_count');
    
    if (!xarMod::apiFunc('dynamicdata','util','import', array('file' =>  sys::code() . 'modules/content/xardata/content_module_settings-def.xml', 'overwrite' => true))) return;

    // Set the vars
    if ($default_ctype) xarModVars::set('content','default_ctype',$default_ctype);
    if ($default_itemid) xarModVars::set('content','default_itemid',$default_itemid);
    if ($default_main_page_tpl) xarModVars::set('content','default_main_page_tpl',$default_main_page_tpl);
    if ($default_display_page_tpl) xarModVars::set('content','default_display_page_tpl',$default_display_page_tpl);
    if ($default_view_page_tpl) xarModVars::set('content','default_view_page_tpl',$default_view_page_tpl);
    if ($enable_filters) xarModVars::set('content','enable_filters',$enable_filters);  
    if ($filters_min_ct_count) xarModVars::set('content','filters_min_ct_count',$filters_min_ct_count);    
    if ($filters_min_item_count) xarModVars::set('content','filters_min_item_count',$filters_min_item_count);

    //the new one
    xarModVars::set('content','sitemap_exclude', serialize(array()));

    return true;
    
}
?>