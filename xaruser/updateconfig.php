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
function calendar_user_updateconfig()
{
    xarVar::fetch('cal_sdow', 'int:0:6', $cal_sdow, xarModUserVars::get('calendar', 'cal_sdow'));
    xarModUserVars::set('calendar', 'cal_sdow', $cal_sdow);

    xarVar::fetch('default_view', 'str::', $default_view, xarModUserVars::get('calendar', 'default_view'));
    xarModUserVars::set('calendar', 'default_view', $default_view);

    xarController::redirect(xarController::URL('calendar', 'user', 'modifyconfig'));
}
