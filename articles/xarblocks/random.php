<?php
// File: random.php
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Author: Roger Keays <r.keays@ninthave.net>
//   based on featureitems.php
//
// Purpose of file: Random Articles Block
// ----------------------------------------------------------------------

/**
 * initialise block
 */
function articles_randomblock_init()
{
    // Default values to initialize the block.
    return array(
        'pubtypeid' => 0,
        'catfilter' => 0,
        'status' => '3,2',
        'numitems' => 1,
        'alttitle' => '',
        'altsummary' => '',
        'showtitle' => true,
        'showsummary' => true,
        'showpubdate' => false,
        'showsubmit' => false,
        'showdynamic' => false,
// TODO: use (something like) this for the default block caching configuration ?
        'nocache' => 0, // Cache this block
        'pageshared' => 1, // Share across all pages
        'usershared' => 1, // Share between group members
        'cacheexpire' => null // Default cache expiration time
    );
}

/**
 * get information on block
 */
function articles_randomblock_info()
{
    // Return details about this block.
    return array(
        'text_type' => 'Random article',
        'module' => 'articles',
        'text_type_long' => 'Show a single random article',
        'allow_multiple' => true,
        'form_content' => false, // Deprecated
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * display block
 */
function articles_randomblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadArticlesBlock', 0, 'Block', $blockinfo['title'])) {return;}

    // Get variables from block content.
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }
    
    // frontpage or approved status
    if (empty($vars['status'])) {
            $statusarray = array(2,3);
    } elseif (!is_array($vars['status'])) {
            $statusarray = split(',', $vars['status']);
    } else {
            $statusarray = $vars['status'];
    }
    
    // get cids for security check in getall
    $fields = array('aid', 'title', 'body', 'notes', 'pubtypeid', 'cids', 'authorid');

    if (!empty($vars['showpubdate'])) {
        array_push($fields, 'pubdate');
    }
    if (!empty($vars['showsummary'])) {
        array_push($fields, 'summary');
    }
    if (!empty($vars['showauthor'])) {
        array_push($fields, 'author');
    }
    if (!empty($vars['alttitle'])) {
        $blockinfo['title'] = $vars['alttitle'];
    }
    if (empty($vars['pubtypeid'])) {
        $vars['pubtypeid'] = 0;
    }
    
    if (!empty($vars['catfilter'])) {
        // use admin defined category 
        $cidsarray = array($vars['catfilter']);
        $cid = $vars['catfilter'];
    } else {
        $cid = 0;
        $cidsarray = array();
    }
    
    if (!empty($vars['showdynamic']) && xarModIsHooked('dynamicdata', 'articles')) {
        array_push($fields, 'dynamicdata');
    }

    if (empty($vars['numitems'])) $vars['numitems'] = 1;
    
    $articles = xarModAPIFunc('articles','user','getrandom',
                              array('ptid' => $vars['pubtypeid'],
                                    'cids' => $cidsarray,
                                    'andcids' => false,
                                    'status' => $statusarray,
                                    'numitems' => $vars['numitems'],
                                    'fields' => $fields));
    if (!isset($articles) || !is_array($articles) || count($articles) == 0) {
        return;
    } else {
        foreach (array_keys($articles) as $key) {
            // for template compatibility :-(
            if (!empty($articles[$key]['author']) && !empty($vars['showauthor'])) {
                $articles[$key]['authorname'] = $articles[$key]['author'];
            }
            $vars['items'][] = $articles[$key];
        }
    }

    // Pass details back for rendering.
    if (count($vars['items']) > 0) {
        $blockinfo['content'] = $vars;
        return $blockinfo;
    }

    // Nothing to render.
    return;
}
/**
 * built-in block help/information system.
 */

function articles_randomblock_help()
{
    // No information yet.
    return '';
}

?>
