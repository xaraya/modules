<?php
/**
 * Hitcount Module
 *
 * @package modules
 * @subpackage hitcount module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    if(!xarSecurity::check('ViewHitcountItems')) return;

    // Database information
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $hitcounttable = $xartable['hitcount'];
    $modulestable = $xartable['modules'];

    // Get items
    $query = "SELECT m.regid, h.itemtype, COUNT(h.itemid), SUM(h.hits)
            FROM $hitcounttable h INNER JOIN $modulestable m ON m.regid = h.module_id
            GROUP BY m.regid, h.itemtype";

    $result = $dbconn->Execute($query);
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
