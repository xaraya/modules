<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
 * This function gets the restricted keywords for one module
 *
 * @param int $args['moduleid'] module id
 * @return array of keywords, sorted ASC
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_userapi_getwordslimited($args)
{
    if (!xarSecurityCheck('ReadKeywords')) return;

    extract($args);

    if (!isset($moduleid) || !is_numeric($moduleid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module id', 'user', 'getwordslimited', 'keywords');

        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }


    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $keywordstable = $xartable['keywords_restr'];
    $bindvars = array();

    // Get restricted keywords for this module item

    $useitemtype = xarModGetVar('keywords','useitemtype');

    $query = "SELECT xar_id,
                     xar_keyword
             FROM $keywordstable ";
    if (!empty($useitemtype) && isset($itemtype)) {
          $query .= " WHERE xar_moduleid = '0' OR ( xar_moduleid= ? AND  xar_itemtype = ? ) ORDER BY xar_keyword ASC";
          $bindvars[] = $moduleid;
          $bindvars[] = $itemtype;
       } else {
          $query .= " WHERE xar_moduleid = '0' OR  xar_moduleid= ? ORDER BY xar_keyword ASC";
          $bindvars[] = $moduleid;
    }


    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    if ($result->EOF) {
        $result->Close();
    }

    $keywords = array();

    while (!$result->EOF) {
        list($id,
             $word) = $result->fields;
        $keywords[$id] = $word;
        $result->MoveNext();
    }
    $result->Close();

    return $keywords;
}


?>
