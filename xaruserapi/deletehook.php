<?php
/**
 * Delete and item via a hook
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian
 * @link  link to information for the subpackage
 * @author Julian development Team
 */

/**
 * Delete an item via a hook
 *
 * process deletion of item - hook for ('item','delete','API')
 *
 * @author  Jorn, MichelV. <michelv@xarayahosting.nl>
 * @access  public
 * @param   array $extrainfo Whatever you need
 * @param   ID objectid ID of the item to delete
 * @return  array ExtraInfo
 * @todo security checks in here
 */
function julian_userapi_deletehook($args)
{
    extract($args);

     // extra info as supplied by the hooking module.
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

     // Get the id of the object to delete (the id as used in the hooking module).
    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object ID', 'user', 'deletehook', 'julian');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // When called via hooks, the module name may be empty, so we get it from the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

     // Convert module name into module id.
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'user', 'deletehook', 'julian');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Get item type.
     if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    // Delete the links to the specified object + itemtype + module.
   $dbconn = xarDBGetConn();
   $xartable = xarDBGetTables();
   $event_linkage_table = $xartable['julian_events_linkage'];
   $query = "DELETE FROM $event_linkage_table WHERE ( hook_modid =$modid AND  hook_itemtype =$itemtype AND  hook_iid =$objectid)";
   $result = $dbconn->Execute($query);

   return $extrainfo;
}

?>
