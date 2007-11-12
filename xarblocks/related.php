<?php
/**
 * File: $Id$
 *
 * Articles related by...
 * Magazine, Issue, Series, Author
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Mag Module
 * @author Phill Brown
*/

/**
 * init func
 */

function mag_relatedblock_init()
{
    return array(
        'magazine' => 0,
        'relatedby' => 'magazine',
        'display' => 0,
        'numitems' => 5,
        'sortby' => 'latest',
    );
}

/**
 * Block info array
 */

function mag_relatedblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Related Data block',
        'module' => 'mag',
        'func_update' => 'mag_relatedblock_update',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true,
        'notes' => 'no notes'
    );
}

/**
 * Display func.
 * @param $blockinfo array
 * @returns $blockinfo array
 * @todo Option to display the menu even when not on a relevant page
 */
function mag_relatedblock_display($blockinfo)
{
    
    // Get variables from content block.
    if (!is_array($blockinfo['content'])) {
        $blockinfo['content'] = unserialize($blockinfo['content']);
    }

    // Pointer to simplify referencing.
    $vars =& $blockinfo['content'];

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module'
        )
    ));
    
    // Sorting
    if ($vars['sortby'] == 'latest') {
        $sort = 'pubdate DESC';
    }
    // TODO: Popular articles
    else {
        $sort = 'pubdate DESC';
    }

    // Set magazine
    if ($vars['magazine'] == 0) {
        $mid = xarVarGetCached('mag', 'mid');
        if (empty($mid)) return;
    } else {
        $mid = $vars['magazine'];
    }

    // Initilise the params
    $params = array(
        'mid' => $mid,
        'status' => 'PUBLISHED',
        'numitems' => $vars['numitems'],
        'sort' => $sort,
    );

    // Article Relationships
    // Display Options:
    //    0: Hide if empty
    //    1: Use if available
    $display = $vars['display'];

    // Articles from the same magazine
    if ($vars['relatedby'] == 'magazine') {
        // No extra parameters - we're just fetching the articles from the magazine set
    }

    // Articles in the same issue
    elseif ($vars['relatedby'] == 'issue') {
        $iid = xarVarGetCached('mag', 'iid');
        if (empty($iid) && ($display == 0)) return;
        elseif (!empty($iid)) $params['iid'] = $iid;
    }

    // Articles from the same series
    elseif ($vars['relatedby'] == 'series') {
        $sid = xarVarGetCached('mag', 'sid');
        if (empty($sid) && ($display == 0)) return;
        elseif (!empty($sid)) $params['sid'] = $sid;
    }

    // Articles by the same authors
    elseif ($vars['relatedby'] == 'author') {
        $authors = xarVarGetCached('mag', 'article_authors');
        $auids = array_keys($authors);
        if (empty($auids) && ($display == 0)) return;
        elseif (!empty($auids)) $params['auids'] = $auids;
    }
    else return;

    
    $related_articles = xarModAPIFunc($module, 'user', 'relatedarticles', $params);
    
    //print_r($related_articles);
    $vars['articles'] = $related_articles;
    return $blockinfo;
}

?>
