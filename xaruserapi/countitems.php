<?php
/**
 * File: $Id:
 * 
 * Utility function counts number of items held by this module
 *
 * File: $Id:
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */

/**
 * utility function to count the number of shopsheld by this module
 *
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function xarcpshop_userapi_countitems()
{

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $cpstorestable = $xartable['cpstores'];
    $query = "SELECT COUNT(1)
            FROM $cpstorestable";

    $result = &$dbconn->Execute($query,array());
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Obtain the number of items
    list($numitems) = $result->fields;
    $result->Close();
    // Return the number of items
    return $numitems;
} 

?>
