<?php
/**
 * Ephemerids
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * utility function to count the number of items held by this module
 *
 * @author the Ephemerid
 * @return int number of items held by this module
 * @raise DATABASE_ERROR
 */
function ephemerids_userapi_countitems()
{
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // Security Check
    if(!xarSecurityCheck('OverviewEphemerids')) return;
    $ephemtable = $xartable['ephem'];
    // Get item
    $query = "SELECT COUNT(1)
            FROM $ephemtable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    // Obtain the number of items
    list($numitems) = $result->fields;
    $result->Close();
    // Return the number of items
    return $numitems;
}
?>