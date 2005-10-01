<?php
/**
 * Utility function to count the number of items held by this module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Todolist Module
 */

/**
 * Utility function to count the number of items held by this module
 * 
 * @author the Todolist module development team 
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function todolist_userapi_countgroups()
{ 

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $groupstable = $xartable['todolist_groups'];
    $query = "SELECT COUNT(1)
            FROM $groupstable";
    $result = &$dbconn->Execute($query,array());
    if (!$result) return;
    /* Obtain the number of items */
    list($numitems) = $result->fields;
    $result->Close();
    /* Return the number of items */
    return $numitems;
}
?>