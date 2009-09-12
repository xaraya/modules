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
 * get a hitcount for a list of items
 * @param $args['modname'] name of the module you want items from, or
 * @param $args['modid'] module id you want items from
 * @param $args['itemtype'] item type of the items (only 1 type supported per call)
 * @param $args['itemids'] array of item IDs
 * @param $args['sort'] string sort by itemid (default) or numhits
 * @param $args['sortorder'] string sort order DESC (default) or ASC
 * @param $args['numitems'] number of items to return
 * @param $args['startnum'] start at this number (1-based)
 * @return array $array[$itemid] = $hits;
 */
function hitcount_userapi_getitems($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($modname) && !isset($modid)) {
        xarSession::setVar('errormsg', _MODARGSERROR);
        return;
    }
    if (!empty($modname)) {
        $modid = xarMod::getRegId($modname);
    }
    if (empty($modid)) {
        xarSession::setVar('errormsg', _MODARGSERROR);
        return;
    } elseif (empty($modname)) {
        $modinfo = xarModGetInfo($modid);
        $modname = $modinfo['name'];
    }
    if (empty($itemtype)) {
        $itemtype = 0;
    }
    if (empty($sort)) {
        $sort = 'itemid';
    }
    if (empty($sortorder)) {
        $sortorder = 'DESC';
    }
    if (empty($startnum)) {
        $startnum = 1;
    }

    if (!isset($itemids)) {
        $itemids = array();
    }

    // Security check
    if (count($itemids) > 0) {
        foreach ($itemids as $itemid) {
            if(!xarSecurityCheck('ViewHitcountItems',1,'Item',"$modname:$itemtype:$itemid")) return;
        }
    } else {
        if(!xarSecurityCheck('ViewHitcountItems',1,'Item',"$modname:$itemtype:All")) return;
    }

    // Database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $hitcounttable = $xartable['hitcount'];

    // Get items
    $bindvars = array();
    $query = "SELECT itemid, hits, lasthit 
            FROM $hitcounttable
            WHERE module_id = ?
              AND itemtype = ?";
    $bindvars[] = (int) $modid;
    $bindvars[] = (int) $itemtype;
    if (count($itemids) > 0) {
        $bindmarkers = '?' . str_repeat(',?',count($itemids)-1);
        $query .= " AND itemid IN ($bindmarkers)";
        foreach ($itemids as $itemid) {
            $bindvars[] = (int) $itemid;
        }
    }
    if ($sort == 'numhits') {
        $query .= " ORDER BY hits $sortorder, itemid DESC";
    } else {
        $query .= " ORDER BY itemid $sortorder";
    }

    if (!empty($numitems) && !empty($startnum)) {
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum - 1,$bindvars);
    } else {
        $result =& $dbconn->Execute($query,$bindvars);
    }
    if (!$result) return;

    $hitlist = array();
    while (!$result->EOF) {
        list($id,$hits) = $result->fields;
        $hitlist[$id] = $hits;
        $result->MoveNext();
    }
    $result->close();

    return $hitlist;
}

?>
