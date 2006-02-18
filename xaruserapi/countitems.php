<?php
/**
 * Utility function counts number of items held by this module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */

/**
 * utility function to count the number of items for one owner
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @return integer number of items held by this module
 * @raise DATABASE_ERROR
 */
function maxercalls_userapi_countitems()
{
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $maxercallstable = $xartable['maxercalls'];
      $owner = xarUserGetVar('uid');
    $query = "SELECT COUNT(*)
            FROM $maxercallstable
            WHERE xar_owner = $owner";
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
