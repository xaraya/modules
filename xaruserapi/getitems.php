<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * get module items for a keyword
 *
 * @param $args['id'] id(s) of the keywords entry(ies), or
 * @param $args['keyword'] keyword
 * @param $args['modid'] modid
 * @param $args['itemtype'] itemtype
 * @param $args['numitems'] number of entries to retrieve (optional)
 * @param $args['startnum'] starting number (optional)
 * @return array of module id, item type and item id
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_userapi_getitems($args)
{
    if (!xarSecurityCheck('ReadKeywords')) return;

    extract($args);

    if (!empty($id)) {
        if (!is_numeric($id) && !is_array($id)) {
            $msg = xarML('Invalid #(1)', 'keywords id');
            throw new Exception($msg);
        }
    } else {
        if (!isset($keyword)) {
            $msg = xarML('Invalid #(1)', 'keyword');
            throw new Exception($msg);
        }
    }

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $keywordstable = $xartable['keywords'];
    $bindvars = array();

    // Get module item for this id
    $query = "SELECT id,
                     itemid,
                     keyword,
                     module_id,
                     itemtype
              FROM $keywordstable";
    if (!empty($id)) {
        if (is_array($id)) {
            $query .= " WHERE id IN (" . join(', ', $id) . ")";
        } else {
            $query .= " WHERE id = ?";
            $bindvars[] = $id;
        }
    } else {
        $query .= " WHERE keyword = ?";
        $bindvars[] = $keyword;
    }
    if (!empty($itemid) && is_numeric($itemid) ) {
        $query .= " AND itemid = ?";
        $bindvars[] = $itemid;
    }
    if (!empty($itemtype)) {
        if (is_array($itemtype)) {
            $query .= ' AND itemtype IN (?' . str_repeat(',?', count($itemtype)-1) . ')';
            $bindvars = array_merge($bindvars, $itemtype);
        } else {
            $query .= ' AND itemtype = ?';
            $bindvars[] = (int)$itemtype;
        }
    }
    if (!empty($modid) && is_numeric($modid) ) {
        $query .= " AND module_id = ?";
        $bindvars[] = $modid;
    }
    $query .= " ORDER BY module_id ASC, itemtype ASC, itemid DESC";

    if (isset($numitems) && is_numeric($numitems)) {
        if (empty($startnum)) {
            $startnum = 1;
        }
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    } else {
        $result =& $dbconn->Execute($query,$bindvars);
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
             $item['module_id'],
             $item['itemtype']) = $result->fields;
        $items[$item['id']] = $item;
        $result->MoveNext();
    }
    $result->Close();
    return $items;
}

?>