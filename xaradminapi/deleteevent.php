<?php

/**
* File: $Id:$
*
* Deletes an event.
*
* @package Xaraya eXtensible Management System
* @copyright (C) 2004 by Metrostat Technologies, Inc.
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.metrostat.net
*
* @subpackage julian
* initial template: Roger Raymond
* @author Jodie Razdrh/John Kevlin/David St.Clair
*/
/**
 * Delete an event
 *
 * Delete an item from the events table
 *
 * @author  Julian Development Team, MichelV. <michelv@xarayahosting.nl>
 * @access  private 
 * @param   $event_id ID of the event
 * @return  array
 * @todo    MichelV. <#> 
 */
function julian_adminapi_deleteevent($args)
{  
    //This prevents users from viewing something they are not suppose to.
    if (!xarSecurityCheck('Editjulian')) return;
    
    extract ($args);
    if (!xarVarFetch('event_id','isset',$event_id)) return;
    
    // establish db connection  
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
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
