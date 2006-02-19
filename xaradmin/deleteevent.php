<?php
/**
 * Delete an event
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Delete an event
 * This function takes the delete command, checks the privilege of the current user and,
   if passed, passes the delete command to the API.
 *
 * @param  id 'event_id' the id of the event to be deleted, or
 * @param  int objectid
 * @param  string cal_date
 * @return bool and URL redirect
 */
function julian_admin_deleteevent($args)
{
    extract($args);

    if (!xarVarFetch('event_id', 'id',    $event_id)) return;
    if (!xarVarFetch('objectid', 'id',    $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('cal_date', 'isset', $cal_date)) return;

    if (!empty($objectid)) {
        $event_id = $objectid;
    }

    // Get item
    $item = xarModAPIFunc('julian',
        'user',
        'get',
        array('event_id' => $event_id));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('DeleteJulian', 1, 'Item', "$event_id:$item[organizer]:$item[calendar_id]:All")) {
        return;
    }
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('julian',
            'admin',
            'deleteevent',
            array('event_id' => $event_id))) {
        return; // throw back
    }
    xarResponseRedirect(xarModURL('julian', 'user', 'month',array('cal_date'=>$cal_date)));
    // Return
    return true;
}
?>