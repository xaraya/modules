<?php
/**
 * Shouter Module
 *
 * @package modules
 * @subpackage shouter module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
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
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
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
