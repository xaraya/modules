<?php
/**
 * File: $Id:
 * 
 * Get a specific item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage paypalipn
 * @author John Cox <niceguyeddie@xaraya.com> 
 */
/**
 * get a specific item
 * 
 * @author John Cox <niceguyeddie@xaraya.com> 
 * @param  $args ['id'] id of example item to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function paypalipn_userapi_get($args)
{ 
    extract($args); 
    // Argument check
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'item ID', 'user', 'get', 'paypalipn');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    } 
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['ipnlog']; 
    // Get item
    // Query from Example Module
    $query = "SELECT xar_id,
                   xar_log
            FROM $table
            WHERE xar_id = " . xarVarPrepForStore($id);
    $result = &$dbconn->Execute($query); 
    if (!$result) return; 

    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exists');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST', new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    } 

    // Obtain the item information from the result set
    list($id, $log) = $result->fields; 
    $result->Close(); 

    // Create the item array
    $item = array('id' => $id,
        'log' => $log); 
    // Return the item array
    return $item;
} 
?>