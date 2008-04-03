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
        'currentissue' => 0,
        'relatedby' => 'magazine',
        'display' => 0,
        'numitems' => 5,
        'sortby' => 'latest',
        'pid' => 0,
        'auto_titles' => true,
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
    switch ($vars['sortby']) {
        case 'popular':
            $sort = 'hitcount DESC';
            break;
        case 'latest':
        default:
            $sort = 'pubdate DESC';
            break;
    }

    // Set magazine
    if ($vars['magazine'] == 0) {
        $mid = xarVarGetCached('mag', 'mid');
    } else {
        $mid = $vars['magazine'];
    }

    // If no magazine ID, then end the block.
    if (empty($mid)) return;

    // Check privileges.
    if (!xarSecurityCheck('OverviewMag', 0, 'Mag', "$mid")) return;

    // Get the mag record (from cache, if it's available).
    if (xarVarIsCached($module, 'mag')) $mag = xarVarGetCached($module, 'mag');
    if (empty($mag)) {
        $mags = xarModAPIfunc($module, 'user', 'getmags', array('mid' => $mid));
        if (empty($mags)) return;
        $mag = reset($mags);
    }

    // Initilise the params
    $params = array(
        'mid' => $mid,
        'status' => 'PUBLISHED',
        'numitems' => $vars['numitems'],
        'sort' => $sort,
    );

    $display = $vars['display'];

    // Check if the articles are limited to the current issue
    if ($vars['currentissue'] == 1) {
        $iid = xarVarGetCached('mag', 'iid');
        if (empty($iid) && ($display == 0)) return;
        $params['iid'] = $iid;

        // Pass the issue details into the template for automated titles (e.g. "Other articles in issue X").
        $issue = xarVarGetCached($module, 'issue');
        if (empty($issue)) {
            $issues = xarModAPIfunc($module, 'user', 'getissues', array('iid' =>$iid));
            if (!empty($issues)) $issue = reset($issues);
        }
        if (!empty($issue)) $vars['issue'] = $issue;
    }

    // Article Relationships
    // Display Options:
    //    0: Hide if empty
    //    1: Use if available, i.e. if we have context
    //    2: Force use, regardless of current page (TODO)

    switch($vars['relatedby']) {
        /*
        case 'issue':
            // Articles in the same issue
            $iid = xarVarGetCached('mag', 'iid');
            if (empty($iid) && ($display == 0)) return;

            if (!empty($iid)) {
                $params['iid'] = $iid;

                // Pass the issue details into the template for automated titles (e.g. "Other articles in issue X").
                $issue = xarVarGetCached($module, 'issue');
                if (empty($issue)) {
                    $issues = xarModAPIfunc($module, 'user', 'getissues', array('iid' =>$iid));
                    if (!empty($issues)) $issue = reset($issues);
                }
                if (!empty($issue)) $vars['issue'] = $issue;
            }
            break;
        */
        case 'series':
            // Articles from the same series
            $sid = xarVarGetCached($module, 'sid');
            if (empty($sid) && ($display == 0)) return;

            if (!empty($sid)) {
                $params['sid'] = $sid;

                // Pass the series details into the template for automated titles (e.g. "Other articles in series X").
                $series = xarVarGetCached($module, 'series');
                if (empty($series)) {
                    $serieses = xarModAPIfunc($module, 'user', 'getseries', array('sid' =>$sid));
                    if (!empty($issueses)) $series = reset($issueses);
                }
                if (!empty($series)) $vars['series'] = $series;
            }
            break;

        case 'author':
            // Articles by the same authors
            $authors = xarVarGetCached($module, 'article_authors');
            $auids = array_keys($authors);
            if (empty($auids) && ($display == 0)) return;

            if (!empty($auids)) {
                $params['auids'] = $auids;

                // Pass the author details into the template for automated titles (e.g. "Other articles by X and Y").
                $article_authors = xarVarGetCached($module, 'article_authors');
                if (!empty($article_authors)) $vars['article_authors'] = $article_authors;
            }
            break;

        case 'magazine':
        default:
            break;
    }
    
    $articles = xarModAPIFunc($module, 'user', 'relatedarticles', $params);

    // End the block if there are no articles to display.
    if (empty($articles)) return;

    $vars['articles'] = $articles;
    $vars['mag'] = $mag;

    if (empty($vars['pid'])) {
        // No forced page ID. Check for a cached value instead.
        if (xarVarIsCached('mag', 'pid')) {
            $vars['pid'] = xarVarGetCached('mag', 'pid');
        } else {
            $vars['pid'] = 0;
        }
    }

    if (empty($vars['auto_titles'])) $vars['auto_titles'] = false;

    return $blockinfo;
}

?>