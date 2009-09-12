<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */

/**
 * get the list of items with top N hits for a module
 *
 * @param $args['modname'] name of the module you want items from
 * @param $args['itemtype'] item type of the items (only 1 type supported per call)
 * @param $args['numitems'] number of items to return
 * @param $args['startnum'] start at this number (1-based)
 * @return array Array('itemid' => $itemid, 'hits' => $hits)
 */
function hitcount_userapi_topitems($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($modname)) {
        xarSession::setVar('errormsg', _MODARGSERROR);
        return;
    }
    $modid = xarMod::getRegId($modname);
    if (empty($modid)) {
        xarSession::setVar('errormsg', _MODARGSERROR);
        return;
    }
    if (empty($itemtype)) {
        $itemtype = 0;
    }

    // Security check
    if(!xarSecurityCheck('ViewHitcountItems',1,'Item',"$modname:$itemtype:All")) return;

    // Database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $hitcounttable = $xartable['hitcount'];

    // Get items
    $query = "SELECT itemid, hits
            FROM $hitcounttable
            WHERE module_id = ?
              AND itemtype = ?
            ORDER BY hits DESC";
    $bindvars = array((int)$modid, (int)$itemtype);

    if (!isset($numitems) || !is_numeric($numitems)) {
        $numitems = 10;
    }
    if (!isset($startnum) || !is_numeric($startnum)) {
        $startnum = 1;
    }

    //$result =& $dbconn->Execute($query);
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum - 1, $bindvars);
    if (!$result) return;

    $topitems = array();
    while (!$result->EOF) {
        list($id,$hits) = $result->fields;
        $topitems[] = array('itemid' => $id, 'hits' => $hits);
        $result->MoveNext();
    }
    $result->close();

    return $topitems;
}

?>
