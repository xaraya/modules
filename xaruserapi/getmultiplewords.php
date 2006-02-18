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
 * @param int $args['objectids'] item id
 * @return array of keywords
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
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