<?php
/**
 * File: $Id:
 * 
 * Utility function counts number of items held by this module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release
 * @author jojodee
 */
/**
 * utility function to count the number of items held by this module
 *
 * @author jojodee
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function release_userapi_countitems($args)
{
	extract($args);

    if (!isset($idtypes)) {
        $idtypes = 1;
    }
    if ($idtypes == 3){
        $whereclause= "WHERE xar_type = '0'";
    }elseif ($idtypes==2) {
        $whereclause= "WHERE xar_type = '1'";
    }else {
        $whereclause= "WHERE xar_type = '1' or xar_type = '0'";
    }


    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $releasetable = $xartable['release_id'];
     $query = "SELECT COUNT(1)
             FROM $releasetable "
             .$whereclause;

    $result = &$dbconn->Execute($query);

    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return; 
    // Obtain the number of items
    list($numitems) = $result->fields; 
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close(); 
    // Return the number of items
    return $numitems;
} 

?>