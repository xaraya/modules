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
 * get entries for a module item
 *
 * @param $args['modid'] module id
 * @param $args['itemtype'] item type
 * @param $args['objectids'] item id
 * @returns array
 * @return array of keywords
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_userapi_getmultiplewords($args)
{
    if (!xarSecurityCheck('ReadKeywords')) return;

    extract($args);

    if (!isset($modid) || !is_numeric($modid)) {
        $msg = xarML('Invalid Parameters');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (!is_array($objectids)) {
        $msg = xarML('Invalid Parameters');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    $keywords = array();
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $keywordstable = $xartable['keywords'];

    foreach ($objectids as $item) {
        $query = "SELECT xar_id,
                         xar_keyword
                  FROM $keywordstable
                  WHERE xar_moduleid = ?
                  AND xar_itemid = ?";

        if (isset($itemtype) && is_numeric($itemtype)) {
            $query .= " AND xar_itemtype = $itemtype";
        }
        $bindvars = array($modid, $item);
        $result =& $dbconn->Execute($query,$bindvars);
        if (!$result) return;

        for (; !$result->EOF; $result->MoveNext()) {
        list($id, $keyword) = $result->fields;
            $keywords[$item][] = array('id'      => $id,
                                       'keyword' => $keyword);
        }
    }
    return $keywords;
}
?>