<?php

/**
 * File: $Id$
 *
 * Displays a block with the latest magazine issue
 * Magazine can be set manually, or to the issue being viewed
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage Mag Module
 * @author Phill Brown
*/

function mag_latestissueblock_modify($blockinfo)
{
    // Get current content
    $vars = unserialize($blockinfo['content']);

    // Get a list of all magazines
    $allmags = xarModAPIFunc('mag', 'user', 'getmags');
    $vars['allmags'] = $allmags;

    $vars['bid'] = $blockinfo['bid'];

    return $vars;
}

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function mag_latestissueblock_update($blockinfo)
{
    // Ensure content is an array.
    // TODO: remove this once all blocks can accept content arrays.
    if (!is_array($blockinfo['content'])) {
        $blockinfo['content'] = unserialize($blockinfo['content']);
    }

    // Reference to content array.
    $vars =& $blockinfo['content'];

    if (xarVarFetch('select_magazine', 'int:0', $magazine, false, XARVAR_NOT_REQUIRED)) {
        $vars['magazine'] = $magazine;
    }

    return $blockinfo;
}

?>