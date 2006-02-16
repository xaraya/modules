<?php
/**
 * Change Log Module version information
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage changelog
 * @link http://xaraya.com/index.php/release/185.html
 * @author mikespub
 */
/**
 * get the list of modules where we're tracking item changes
 *
 * @returns array
 * @return $array[$modid][$itemtype] = array('items' => $numitems,'changes' => $numchanges);
 */
function changelog_userapi_getmodules($args)
{
// Security Check
   if (!xarSecurityCheck('ReadChangeLog')) return;

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $changelogtable = $xartable['changelog'];

    // Get items
    $query = "SELECT xar_moduleid, xar_itemtype, COUNT(DISTINCT xar_itemid), COUNT(*)
            FROM $changelogtable
            GROUP BY xar_moduleid, xar_itemtype";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numitems,$numchanges) = $result->fields;
        $modlist[$modid][$itemtype] = array('items' => $numitems, 'changes' => $numchanges);
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>
