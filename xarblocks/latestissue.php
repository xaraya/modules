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

/**
 * init func
 */

function mag_latestissueblock_init()
{
    return array(
        'magazine' => 0,
    );
}

/**
 * Block info array
 */

function mag_latestissueblock_info()
{
    return array(
        'text_type' => 'Content',
        'text_type_long' => 'Magazines latest issue block',
        'module' => 'mag',
        'func_update' => 'mag_latestissueblock_update',
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
function mag_latestissueblock_display($blockinfo)
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

    // Fetch the latest issues for the magazine
    $mid = $vars['magazine'];
    
    // If the block is looking for the current (magazine being viewed), get the cached magazine id
    if ($mid == 0) {
        $current_mid = xarVarGetCached($module, 'mid');
        if (empty($current_mid)) return;
        $mid = $current_mid;
    } else {
        $current_mid = 0;
    }
    
    // Force the id as an integer
    $mid = (integer)$mid;

    // If no overview access to this magazine, then don't display anything.
    if (!xarSecurityCheck('OverviewMag', 0, 'Mag', "$mid")) return;

    // Fetch data for the selected magazine.
    // Use the cached data if it is available.
    if ($current_mid == $mid && xarVarIsCached($module, 'mag')) $mag = xarVarGetCached($module, 'mag');

    if (empty($mag)) {
        $mags = xarModAPIFunc($module, 'user', 'getmags', array('mid' => $mid, 'numitems' => 1));
        if (empty($mags)) return;
        $mag = reset($mags);
    }

    $vars['mag'] = $mag;

    // Fetch data for the latest issue of the magazine
    // Parameters for fetching the latest issue.
    $issue_select = array(
        'mid' => $mid,
        'numitems' => 1,
        'sort' => 'number DESC',
        'status' => 'PUBLISHED',
    );

    // Get the issue.
    $issues = xarModAPIfunc($module, 'user', 'getissues', $issue_select);
    if (empty($issues)) return;

    $vars['issue'] = reset($issues);
    
    return $blockinfo;
}

?>
