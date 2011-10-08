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
 * Delete all shouts
 *
 * @return bool
 */
function shouter_adminapi_deleteall()
{
    if (!xarSecurityCheck('AdminShouter')) return;

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
