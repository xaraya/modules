<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Count Shouts
 *
 * @return int number of items
 */
function shouter_userapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $shoutertable = $xartable['shouter'];

    $query = "SELECT COUNT(1)
            FROM $shoutertable";
    $result = &$dbconn->Execute($query,array());
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}
?>
