<?php
/**
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.zwiggybo.com
 *
 * @subpackage shouter
 * @link http://xaraya.com/index.php/release/236.html
 * @author Neil Whittaker
 */

/**
 * Delete all shouts
 *
 * @return bool
 */
function shouter_adminapi_deleteall()
{
    if (!xarSecurityCheck('DeleteAllShouter')) return;

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $shoutertable = $xartable['shouter'];

    // Delete all shouts
    $query = "DELETE FROM $shoutertable";
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    return true;
}
?>
