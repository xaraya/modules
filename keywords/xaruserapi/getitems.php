<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author mikespub
*/

/**
 * get module items for a word
 *
 * @param $args['id'] id(s) of the keywords entry(ies), or
 * @param $args['keyword'] keyword
 * @param $args['modid'] modid
 * @param $args['itemtype'] itemtype
 * @param $args['numitems'] number of entries to retrieve (optional)
 * @param $args['startnum'] starting number (optional)
 * @returns array
 * @return array of module id, item type and item id
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_userapi_getitems($args)
{
    if (!xarSecurityCheck('ReadKeywords')) return;

    extract($args);

    if (!empty($id)) {
        if (!is_numeric($id) && !is_array($id)) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                         'keywords id', 'user', 'getitem', 'keywords');
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                           new SystemException($msg));
            return;
        }
    } else {
        if (!isset($keyword)) {
            $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                        'keyword', 'user', 'getitem', 'keywords');
            xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                           new SystemException($msg));
            return;
        }
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $keywordstable = $xartable['keywords'];

    // Get module item for this id
    $query = "SELECT xar_id,
                     xar_itemid,
                     xar_keyword,
                     xar_moduleid,
                     xar_itemtype
              FROM $keywordstable";
    if (!empty($id)) {
        if (is_array($id)) {
            $query .= " WHERE xar_id IN (" . join(', ',$id) . ")";
        } else {
            $query .= " WHERE xar_id = '" . xarVarPrepForStore($id) . "'";
        }
    } else {
        $query .= " WHERE xar_keyword = '" . xarVarPrepForStore($keyword) . "'";
    }
    if (!empty($itemid) && is_numeric($itemid) ) {
        $query .= " AND xar_itemid = '".xarVarPrepForStore($itemid) ."'";
    }
     if (!empty($itemtype) && is_numeric($itemtype) ) {
        $query .= " AND xar_itemtype = '".xarVarPrepForStore($itemtype) ."'";
    }
    if (!empty($modid) && is_numeric($modid) ) {
        $query .= " AND xar_moduleid = '".xarVarPrepForStore($modid) ."'";
    }
    $query .= " ORDER BY xar_moduleid ASC, xar_itemtype ASC, xar_itemid DESC";

    if (isset($numitems) && is_numeric($numitems)) {
        if (empty($startnum)) {
            $startnum = 1;
        }
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    } else {
        $result =& $dbconn->Execute($query);
    }
    if (!$result) return;

    $items = array();
    if ($result->EOF) {
        $result->Close();
        return $items;
    }
    while (!$result->EOF) {
        $item = array();
        list($item['id'],
             $item['itemid'],
             $item['keyword'],
             $item['moduleid'],
             $item['itemtype']) = $result->fields;
        $items[$item['id']] = $item;
        $result->MoveNext();
    }
    $result->Close();
    return $items;
}

?>
