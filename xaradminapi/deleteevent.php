<?php
/**
 * Deletes an event.
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Delete an event
 *
 * Delete an item from the events table
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @author  Julian Development Team, MichelV. <michelv@xarayahosting.nl>
 * @access  private
 * @param   $event_id ID of the event
 * @return  array
 * @todo    MichelV. <#>
 */
function julian_adminapi_deleteevent($args)
{
    extract ($args);
    if (!xarVarFetch('event_id','isset',$event_id)) return;
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

    // establish db connection
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $event_table = $xartable['julian_events'];

    //delete the event
    $query = "DELETE
              FROM $event_table
              WHERE event_id = ?";
    if (!$result = $dbconn->Execute($query,array($event_id))) {
        return;
    }

    // Tell hooked modules that the event was deleted.

    $item['module'] = 'julian';
    $item['itemid'] = $event_id;
    $hooks = xarModCallHooks('item', 'delete', $event_id, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>
