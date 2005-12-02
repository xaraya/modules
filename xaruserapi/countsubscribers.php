<?php
/**
* Count the number of subscribers
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * utility function to count the number of subscribers
 *
 * @author the ebulletin module development team
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function ebulletin_userapi_countsubscribers()
{
    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $substable = $xartable['ebulletin_subscriptions'];

    // get count
    $query = "SELECT COUNT(1) FROM $substable";
    $result = $dbconn->Execute($query,array());
    if (!$result) return;
    list($numitems) = $result->fields;
    $result->Close();

    // success
    return $numitems;
}

?>
