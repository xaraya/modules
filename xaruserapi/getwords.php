<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
*/
/**
 * get entries for a module item
 *
 * @param int $args['modid'] module id
 * @param int $args['itemtype'] item type
 * @param int $args['itemid'] item id
 * @param int $args['numitems'] number of entries to retrieve (optional)
 * @param int $args['startnum'] starting number (optional)
 * @return array of keywords
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * @todo This is so similar to getitems, that they could be merged. It is only the format of the results that differs.
 */
function keywords_userapi_getwords($args)
{
    if (!xarSecurityCheck('ReadKeywords')) return;

    extract($args);

    if (!isset($modid) || !is_numeric($modid)) {
        $msg = xarML('Invalid #(1)', 'module id');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (!isset($itemid) || !is_numeric($itemid)) {
        $msg = xarML('Invalid #(1)', 'item id');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $keywordstable = $xartable['keywords'];
    $bindvars = array();
    $bindvars[] = $modid;

    // Get words for this module item
    $query = "SELECT xar_id, xar_keyword
              FROM $keywordstable
              WHERE xar_moduleid = ?";

    if (!empty($itemtype)) {
        if (is_array($itemtype)) {
            $query .= ' AND xar_itemtype IN (?' . str_repeat(',?', count($itemtype)-1) . ')';
            $bindvars = array_merge($bindvars, $itemtype);
        } else {
            $query .= ' AND xar_itemtype = ?';
            $bindvars[] = (int)$itemtype;
        }
    }
    $query .= " AND xar_itemid = ?";
    $bindvars[] = $itemid;

    $query .= " ORDER BY xar_keyword ASC";

    if (isset($numitems) && is_numeric($numitems)) {
        if (empty($startnum)) {
            $startnum = 1;
        }
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1, $bindvars);
    } else {
         $result =& $dbconn->Execute($query,$bindvars);
    }
    if (!$result) return;

    $words = array();
    while (!$result->EOF) {
        list($id, $word) = $result->fields;
        $words[$id] = $word;
        $result->MoveNext();
    }
    $result->Close();

    return $words;
}

?>