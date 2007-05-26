<?php

/**
 * initialise block
 */
function sitesearch_searchblock_init()
{
    return array(
        'database'   => null,
        'nocache'    => 0, // cache by default
        'pageshared' => 1, // share across pages (change if you use dynamic pubtypes et al.)
        'usershared' => 1, // share across group members
        'cacheexpire'=> null    
    );
}

/**
 * get information on block
 */
function sitesearch_searchblock_info()
{
    // Values
    return array(
        'text_type' => 'Search',
        'module' => 'sitesearch',
        'text_type_long' => 'Search Block',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => false
    );
}

/**
 * modify block settings
 */
function sitesearch_searchblock_modify($blockinfo)
{
    // Get current content
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    if (!isset($vars['numbanners'])) {
        $vars['numbanners'] = 1;
    }    
    
    $vars['blockid'] = $blockinfo['bid'];

    // Return output
    return $vars;
}

/**
 * update block settings
 */
function sitesearch_searchblock_update($blockinfo)
{
    if (!xarVarFetch('numbanners',  'int',      $vars['numbanners'],  NULL, XARVAR_NOT_REQUIRED)){ return; }
    
    $blockinfo['content'] = $vars;

    return $blockinfo;
}

/**
 * display block
 */
function sitesearch_searchblock_display($blockinfo)
{
    // Security Check
    if(!xarSecurityCheck('ViewBaseBlocks',0,'Block',"All:$blockinfo[title]:All")) return;

    if( !xarVarFetch('keywords', 'str', $keywords, '', XARVAR_NOT_REQUIRED) ){ return; }
    
    // Get variables from content block
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    if( !empty($vars) ){ extract($vars); }

    if( !isset($database) ){ $database = null; }
       
    $data = array();
    
    $data['database'] = $database;
    
    // Return data, not rendered content.
    $blockinfo['content'] = $data;
    if( is_array($blockinfo['content']) ) 
    {
        return $blockinfo;
    }

    return;
}
?>