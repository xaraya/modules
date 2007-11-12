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
        'magazine' => 'Magazine', 
        'issue' => 'Issue',
        'series' => 'Series',
        'author' => 'Author',
    );
    
    // Display options
    $vars['display_types'] = array(
        0 => 'Always hide if empty',
        1 => 'Use if available',
    );

    // Sorting options
    $vars['sortby_types'] = array(
        'latest' => 'Most Recent',
    );

    //print_r($vars);

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
    if (xarVarFetch('magazine', 'int:0', $magazine, false, XARVAR_NOT_REQUIRED)) {
        $vars['magazine'] = $magazine;
    }
    if (xarVarFetch('relatedby', 'str', $relatedby, false, XARVAR_NOT_REQUIRED)) {
        $vars['relatedby'] = $relatedby;
    }
    if (xarVarFetch('display', 'int:0', $display, false, XARVAR_NOT_REQUIRED)) {
        $vars['display'] = $display;
    }
    if (xarVarFetch('numitems', 'int:1:100', $numitems, false, XARVAR_NOT_REQUIRED)) {
        $vars['numitems'] = $numitems;
    }
    if (xarVarFetch('sortby', 'str', $sortby, false, XARVAR_NOT_REQUIRED)) {
        $vars['sortby'] = $sortby;
    }

    return $blockinfo;
}

?>