<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    if (!xarSecurityCheck('ReadKeywords')) {
        return;
    }

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
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $keywordstable = $xartable['keywords'];

    foreach ($objectids as $item) {
        $query = "SELECT id,
                         keyword
                  FROM $keywordstable
                  WHERE module_id = ?
                  AND itemid = ?";

        if (isset($itemtype) && is_numeric($itemtype)) {
            $query .= " AND itemtype = $itemtype";
        }
        $bindvars = array($modid, $item);
        $result =& $dbconn->Execute($query, $bindvars);
        if (!$result) {
            return;
        }

        for (; !$result->EOF; $result->MoveNext()) {
            list($id, $keyword) = $result->fields;
            $keywords[$item][] = array('id'      => $id,
                                       'keyword' => $keyword);
        }
    }
    return $keywords;
}
