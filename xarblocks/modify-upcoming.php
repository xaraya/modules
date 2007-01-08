<?php
/**
 * Upcoming courses block initialisation
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses module
 * @author Courses module development team
 */

/**
 * modify block settings
 */
function courses_upcomingblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);
    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    // Defaults
    if (!isset($vars['BlockDays'])) {
        $vars['BlockDays'] = 7;
    }
    if (!isset($vars['DateFormat'])) {
        $vars['DateFormat'] = 'short';
    }
    // Send content to template
    $output = xarTplBlock('courses', 'modify-upcoming', array('numitems' => $vars['numitems'], 'blockid' => $blockinfo['bid'], 'BlockDays' => $vars['BlockDays'], 'DateFormat' => $vars['DateFormat']));
    // Return output
    return $output;
}

/**
 * update block settings
 */
function courses_upcomingblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'isset', $vars['numitems'], NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('BlockDays', 'int', $vars['BlockDays'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('DateFormat', 'str', $vars['DateFormat'], 'short', XARVAR_NOT_REQUIRED)) return;
    // Define a default block title
    if (empty($blockinfo['title'])) {
        $blockinfo['title'] = xarML('Current and upcoming courses');
    }

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

?>