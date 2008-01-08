<?php
/**
 * Polls Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * utility function to count the number of items held by this module
 * @returns integer
 * @return number of items held by this module
 */
function polls_userapi_countitems()
{
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $pollstable = $xartable['polls'];

    // Get number of items
    $sql = "SELECT COUNT(1)
            FROM $pollstable
            WHERE itemid = 0
            AND open >= 0";
    $result = $dbconn->Execute($sql);

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
