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
        'showdynamic' => false
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
    if (!xarSecurityCheck('ReadArticlesBlock', 1, 'Block', $blockinfo['title'])) {return;}

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
    $fields = array('aid', 'title', 'pubtypeid', 'cids', 'authorid');

    if (!empty($vars['showpubdate'])) {
            array_push($fields, 'pubdate');
    }
    
    if (!empty($vars['showsummary'])) {
            array_push($fields, 'summary');
    }
    if (!empty($vars['alttitle'])) {
            $blockinfo['title'] = $vars['alttitle'];
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
    
    $articles = xarModAPIFunc(
    'articles','user','getall',
    array(
            'ptid' => $vars['pubtypeid'],
            'cids' => $cidsarray,
            'andcids' => 'false',
            'status' => $statusarray,
            'fields' => $fields	)
    );
    $nbarticles = count($articles);
    if (empty($vars['numitems'])) $vars['numitems'] = $nbarticles;
    if (!isset($articles) || !is_array($articles) || $nbarticles == 0) {
            return;
    } else {
            if ($nbarticles <= $vars['numitems']) $randomarticle = array_rand($articles, $nbarticles);
            else $randomarticle = array_rand($articles, $vars['numitems']);
            if(!is_array($randomarticle)) $randomarticle = array($randomarticle);

            foreach ($randomarticle as $randomaid) {
                if (!empty($articles[$randomaid]['authorid']) && !empty($vars['showauthor'])) {
                    $articles[$randomaid]['authorname'] = xarUserGetVar('name', $articles[$randomaid]['authorid']);
                    if (empty($articles[$randomaid]['authorname'])) {
                        xarExceptionHandled();
                        $articles[$randomaid]['authorname'] = xarML('Unknown');
                    }
                }
                $vars['items'][] = $articles[$randomaid];
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