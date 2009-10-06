<?php
/**
 * Change Log Module version information
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage changelog
 * @link http://xaraya.com/index.php/release/185.html
 * @author mikespub
 */
/**
 * get the number of changes for a list of items
 * @param $args['modname'] name of the module you want items from, or
 * @param $args['modid'] module id you want items from
 * @param $args['itemtype'] item type of the items (only 1 type supported per call)
 * @param $args['itemids'] array of item IDs
 * @param $args['editor'] optional editor of the changelog entries
 * @param $args['sort'] string sort by itemid (default) or numhits
 * @param $args['numitems'] number of items to return
 * @param $args['startnum'] start at this number (1-based)
 * @returns array
 * @return $array[$itemid] = $changes;
 */
function changelog_userapi_getitems($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($modname) && !isset($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module', 'user', 'getitems', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!empty($modname)) {
        $modid = xarMod::getRegId($modname);
    }
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module', 'user', 'getitems', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
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
    if (empty($startnum)) {
        $startnum = 1;
    }

    if (!isset($itemids)) {
        $itemids = array();
    }

// Security Check
   if (!xarSecurityCheck('ReadChangeLog',1,"$modid:$itemtype:All")) return;

    // Database information
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $changelogtable = $xartable['changelog'];

    // Get items
    $query = "SELECT xar_itemid,
                     COUNT(*) as numchanges
                FROM $changelogtable
               WHERE xar_moduleid = ?
                 AND xar_itemtype = ?";
    $bindvars = array((int) $modid, (int) $itemtype);

    if (!empty($editor) && is_numeric($editor)) {
        $query .= " AND xar_editor = ? ";
        $bindvars[] = (int) $editor;
    }
    if (count($itemids) > 0) {
        $allids = join(', ',$itemids);
        $query .= " AND xar_itemid IN ( ? ) ";
        $bindvars[] = (string) $allids;
    }
    $query .= " GROUP BY xar_itemid";
    if ($sort == 'numchanges') {
        $query .= " ORDER BY numchanges DESC, xar_itemid ASC";
    } else {
        $query .= " ORDER BY xar_itemid ASC";
    }

    if (!empty($numitems) && !empty($startnum)) {
        $result = $dbconn->SelectLimit($query, $numitems, $startnum - 1, $bindvars);
    } else {
        $result =& $dbconn->Execute($query, $bindvars);
    }
    if (!$result) return;

    $hitlist = array();
    while (!$result->EOF) {
        list($id,$changes) = $result->fields;
        $hitlist[$id] = $changes;
        $result->MoveNext();
    }
    $result->close();

    return $hitlist;
}

?>
