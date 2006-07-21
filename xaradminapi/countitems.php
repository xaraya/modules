<?php
/**
 * Utility function counts number of items held by this module
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * utility function to count the number of items held by this module
 *
 * @author the Maxercalls module development team
 * @returns integer
 * @return number of items held by this module
 * @throws DATABASE_ERROR
 * @TODO Deprecate?
 */
function maxercalls_adminapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $maxercallstable = $xartable['maxercalls'];
    // Get item - the formatting here is not mandatory, but it does make the
    // SQL statement relatively easy to read.  Also, separating out the sql
    // statement from the Execute() command allows for simpler debug operation
    // if it is ever needed
    $query = "SELECT COUNT(1)
            FROM $maxercallstable";
    // If there are no variables you can pass in an empty array for bind variables
    // or no parameter.
    $result = &$dbconn->Execute($query,array());
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
