<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */
/**
 * get the list of modules for which we're counting items
 *
 * @return array $array[$modid][$itemtype] = array('items' => $numitems,'hits' => $numhits);
 */
function hitcount_userapi_getmodules($args)
{
    // Security Check
    if(!xarSecurityCheck('ViewHitcountItems')) return;

    // Database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $hitcounttable = $xartable['hitcount'];

    // Get items
    $query = "SELECT module_id, itemtype, COUNT(itemid), SUM(hits)
            FROM $hitcounttable
            GROUP BY module_id, itemtype";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numitems,$numhits) = $result->fields;
        $modlist[$modid][$itemtype] = array('items' => $numitems, 'hits' => $numhits);
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}
?>
