<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * get the list of modules and itemtypes for which we're categorising items
 *
 * @return array $array[$modid][$itemtype] = array('items' => $numitems,'cats' => $numcats,'links' => $numlinks);
 */
function categories_userapi_getmodules($args)
{
    // Get arguments from argument array
    extract($args);

    // Security check
    if(!xarSecurityCheck('ViewCategoryLink')) return;

    if (empty($cid) || !is_numeric($cid)) {
        $cid = 0;
    }

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categoriestable = $xartable['categories_linkage'];

    if($dbconn->databaseType == 'sqlite') {

    // TODO: see if we can't do this some other way in SQLite

        $bindvars = array();
        // Get links
        $sql = "SELECT xar_modid, xar_itemtype, COUNT(*)
                FROM $categoriestable";
        if (!empty($cid)) {
            $sql .= " WHERE xar_cid = ?";
            $bindvars[] = $cid;
        }
        $sql .= " GROUP BY xar_modid, xar_itemtype";

        $result = $dbconn->Execute($sql,$bindvars);
        if (!$result) return;

        $modlist = array();
        while (!$result->EOF) {
            list($modid,$itemtype,$numlinks) = $result->fields;
            if (!isset($modlist[$modid])) {
                $modlist[$modid] = array();
            }
            $modlist[$modid][$itemtype] = array('items' => 0, 'cats' => 0, 'links' => $numlinks);
            $result->MoveNext();
        }
        $result->close();

        // Get items
        $sql = "SELECT xar_modid, xar_itemtype, COUNT(*)
                FROM (SELECT DISTINCT xar_iid, xar_modid, xar_itemtype
                      FROM $categoriestable";
        if (!empty($cid)) {
            $sql .= " WHERE xar_cid = ?";
            $bindvars[] = $cid;
        }
        $sql .= ") GROUP BY xar_modid, xar_itemtype";

        $result = $dbconn->Execute($sql,$bindvars);
        if (!$result) return;

        while (!$result->EOF) {
            list($modid,$itemtype,$numitems) = $result->fields;
            $modlist[$modid][$itemtype]['items'] = $numitems;
            $result->MoveNext();
        }
        $result->close();

        // Get cats
        $sql = "SELECT xar_modid, xar_itemtype, COUNT(*)
                FROM (SELECT DISTINCT xar_cid, xar_modid, xar_itemtype
                      FROM $categoriestable";
        if (!empty($cid)) {
            $sql .= " WHERE xar_cid = ?";
            $bindvars[] = $cid;
        }
        $sql .= ") GROUP BY xar_modid, xar_itemtype";

        $result = $dbconn->Execute($sql,$bindvars);
        if (!$result) return;

        while (!$result->EOF) {
            list($modid,$itemtype,$numcats) = $result->fields;
            $modlist[$modid][$itemtype]['cats'] = $numcats;
            $result->MoveNext();
        }
        $result->close();

    } else {
        $bindvars = array();
        // Get items
        $sql = "SELECT xar_modid, xar_itemtype, COUNT(*), COUNT(DISTINCT xar_iid), COUNT(DISTINCT xar_cid)
                FROM $categoriestable";
        if (!empty($cid)) {
            $sql .= " WHERE xar_cid = ?";
            $bindvars[] = $cid;
        }
        $sql .= " GROUP BY xar_modid, xar_itemtype";

        $result = $dbconn->Execute($sql,$bindvars);
        if (!$result) return;

        $modlist = array();
        while (!$result->EOF) {
            list($modid,$itemtype,$numlinks,$numitems,$numcats) = $result->fields;
            if (!isset($modlist[$modid])) {
                $modlist[$modid] = array();
            }
            $modlist[$modid][$itemtype] = array('items' => $numitems, 'cats' => $numcats, 'links' => $numlinks);
            $result->MoveNext();
        }
        $result->close();
    }

    return $modlist;
}

?>
