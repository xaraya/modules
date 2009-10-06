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
 * get entries for a module item
 *
 * @param $args['modid'] module id
 * @param $args['itemtype'] item type
 * @param $args['itemid'] item id
 * @param $args['numitems'] number of entries to retrieve (optional)
 * @param $args['startnum'] starting number (optional)
 * @return array of changes
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function changelog_adminapi_getchanges($args)
{
    extract($args);

    if (!isset($modid) || !is_numeric($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module id', 'admin', 'getchanges', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $itemtype = 0;
    }
    if (!isset($itemid) || !is_numeric($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'getchanges', 'changelog');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $changelogtable = $xartable['changelog'];
    $rolestable = $xartable['roles'];

    // Get changes for this module item
    $query = "SELECT $changelogtable.xar_logid,
                     $changelogtable.xar_editor,
                     $changelogtable.xar_hostname,
                     $changelogtable.xar_date,
                     $changelogtable.xar_status,
                     $changelogtable.xar_remark,
                     $rolestable.name
                FROM $changelogtable
           LEFT JOIN $rolestable
                  ON $changelogtable.xar_editor = $rolestable.id
               WHERE $changelogtable.xar_moduleid = ?
                 AND $changelogtable.xar_itemtype = ?
                 AND $changelogtable.xar_itemid = ?
            ORDER BY $changelogtable.xar_logid DESC";

    $bindvars = array((int) $modid, (int) $itemtype, (int) $itemid);

    if (isset($numitems) && is_numeric($numitems)) {
        if (empty($startnum)) {
            $startnum = 1;
        }
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    } else {
        $result =& $dbconn->Execute($query, $bindvars);
    }
    if (!$result) return;

    $changes = array();
    while (!$result->EOF) {
        $change = array();
        list($change['logid'],
             $change['editor'],
             $change['hostname'],
             $change['date'],
             $change['status'],
             $change['remark'],
             $change['editorname']) = $result->fields;
        $changes[$change['logid']] = $change;
        $result->MoveNext();
    }
    $result->Close();

    return $changes;
}


?>
