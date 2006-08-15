<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * get the list of modules and itemtypes for the items that we're commenting on
 *
 * @param   string  status optional status to count: ALL (minus root nodes), ACTIVE, INACTIVE
 * @param integer modid optional module id you want to count for
 * @param integer itemtype optional item type you want to count for
 * @returns array
 * @return $array[$modid][$itemtype] = array('items' => $numitems,'comments' => $numcomments);
 */
function comments_userapi_getmodules($args)
{
    // Get arguments from argument array
    extract($args);

    // Security check
    if (!xarSecurityCheck('Comments-Read')) return;

    if (empty($status)) {
        $status = 'all';
    }
    $status = strtolower($status);

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $commentstable = $xartable['comments'];
    $ctable = $xartable['comments_column'];

    switch ($status) {
        case 'active':
            $where_status = "$ctable[status] = ". _COM_STATUS_ON;
            break;
        case 'inactive':
            $where_status = "$ctable[status] = ". _COM_STATUS_OFF;
            break;
        default:
        case 'all':
            $where_status = "$ctable[status] != ". _COM_STATUS_ROOT_NODE;
    }
    if (!empty($modid)) {
        $where_mod = " AND $ctable[modid] = $modid";
        if (isset($itemtype)) {
            $where_mod .= " AND $ctable[itemtype] = $itemtype";
        }
    } else {
        $where_mod = '';
    }

    // Get items
    $sql = "SELECT $ctable[modid], $ctable[itemtype], COUNT(*), COUNT(DISTINCT $ctable[objectid])
            FROM $commentstable
            WHERE $where_status $where_mod
            GROUP BY $ctable[modid], $ctable[itemtype]";

    $result = $dbconn->Execute($sql);
    if (!$result) return;

    $modlist = array();
    while (!$result->EOF) {
        list($modid,$itemtype,$numcomments,$numitems) = $result->fields;
        if (!isset($modlist[$modid])) {
            $modlist[$modid] = array();
        }
        $modlist[$modid][$itemtype] = array('items' => $numitems, 'comments' => $numcomments);
        $result->MoveNext();
    }
    $result->close();

    return $modlist;
}

?>
