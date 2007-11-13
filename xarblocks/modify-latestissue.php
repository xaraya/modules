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
    $mags = xarModAPIFunc('mag', 'user', 'getmags');
    $mags = array_merge(array(0 => array('mid' => 0, 'title' => xarML('-- Current Magazine --'))), $mags);

    $vars['allmags'] = $mags;
    if (empty($vars['pid'])) $vars['pid'] = 0;

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

    if (xarVarFetch('select_magazine', 'int:0', $magazine, 0, XARVAR_NOT_REQUIRED)) {
        $vars['magazine'] = $magazine;
    }

    if (xarVarFetch('pid', 'int:0', $pid, 0, XARVAR_NOT_REQUIRED)) {
        $vars['pid'] = $pid;
    }

    return $blockinfo;
}

?>