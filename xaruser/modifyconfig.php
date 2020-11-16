<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 *  Allows a user to modify their Calendar specific changes
 */
function calendar_user_modifyconfig()
{
    xarVar::fetch('cal_sdow', 'int:0:6', $cal_sdow, xarModUserVars::get('calendar', 'cal_sdow'));
    xarVar::fetch('default_view', 'int:0:6', $default_view, xarModUserVars::get('calendar', 'default_view'));
    return array(
        'cal_sdow'=>$cal_sdow,
        'default_view'=>$default_view
        );
}
