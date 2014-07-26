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

function calendar_admin_main()
{
    if(!xarSecurityCheck('AdminCalendar')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
       xarController::redirect(xarModURL('calendar','admin', 'view'));
    }

    return true;
}
?>

