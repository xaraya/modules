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

function mag_relatedblock_modify($blockinfo)
{
    // Get current content
    $vars = unserialize($blockinfo['content']);

    // Vars for template use
    $vars['bid'] = $blockinfo['bid'];

    // Available magazines
    $allmags = xarModAPIFunc('mag', 'user', 'getmags');
    $vars['mag_types'] = $allmags;
    
    // Related by options
    $vars['relatedby_types'] = array(
        'magazine' => xarML('Magazine'), 
        'issue' => xarML('Issue'),
        'series' => xarML('Series'),
        'author' => xarML('Author'),
    );
    
    // Display options
    $vars['display_types'] = array(
        0 => xarML('Always hide if empty'),
        1 => xarML('Use if available'),
    );

    // Sorting options
    $vars['sortby_types'] = array(
        'latest' => xarML('Most Recent'),
        'popular' => xarML('Most Viewed'),
    );

    if (empty($vars['pid'])) $vars['pid'] = 0;
    if (empty($vars['auto_titles'])) $vars['auto_titles'] = false;

    return $vars;
}

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function mag_relatedblock_update($blockinfo)
{
    // Ensure content is an array.
    // TODO: remove this once all blocks can accept content arrays.
    if (!is_array($blockinfo['content'])) {
        $blockinfo['content'] = unserialize($blockinfo['content']);
    }

    // Reference to content array.
    $vars =& $blockinfo['content'];

    // Set the parameters
    if (xarVarFetch('magazine', 'int:0', $magazine, 0, XARVAR_NOT_REQUIRED)) {
        $vars['magazine'] = $magazine;
    }
    if (xarVarFetch('relatedby', 'str', $relatedby, '', XARVAR_NOT_REQUIRED)) {
        $vars['relatedby'] = $relatedby;
    }
    if (xarVarFetch('display', 'int:0', $display, 0, XARVAR_NOT_REQUIRED)) {
        $vars['display'] = $display;
    }
    if (xarVarFetch('numitems', 'int:1:100', $numitems, 5, XARVAR_NOT_REQUIRED)) {
        $vars['numitems'] = $numitems;
    }
    if (xarVarFetch('sortby', 'str', $sortby, '', XARVAR_NOT_REQUIRED)) {
        $vars['sortby'] = $sortby;
    }
    if (xarVarFetch('pid', 'int:0', $pid, 0, XARVAR_NOT_REQUIRED)) {
        $vars['pid'] = $pid;
    }
    if (xarVarFetch('auto_titles', 'checkbox', $auto_titles, false, XARVAR_NOT_REQUIRED)) {
        $vars['auto_titles'] = $auto_titles;
    }

    return $blockinfo;
}

?>