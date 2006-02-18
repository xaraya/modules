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
 * @param int $args['itemtype'] itemtype
 * @return array of keywords
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_adminapi_getwordslimited($args)
{
    extract($args);

    if (!isset($moduleid) || !is_numeric($moduleid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module id', 'user', 'getwordslimited', 'keywords');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $keywordstable = $xartable['keywords_restr'];
    $bindvars = array();

    // Get restricted keywords for this module item
    $query = "SELECT xar_id,
                     xar_keyword
              FROM $keywordstable
              WHERE xar_moduleid = ?";

              $bindvars[] = $moduleid;

    if (isset($itemtype)) {
          $query .= " AND xar_itemtype = ?";
          $bindvars[] = $itemtype;
    }
    $query .= " ORDER BY xar_keyword ASC";
    $result =& $dbconn->Execute($query,$bindvars);

    if (!$result) return;
    $keywords = array();
    $keywords = '';
    if ($result->EOF) {
        $result->Close();
        return $keywords;
    }
    while (!$result->EOF) {
        list($id,
             $word) = $result->fields;
        $keywords[$id] = $word;
        $result->MoveNext();
    }
    $result->Close();

    $delimiters = xarModGetVar('keywords','delimiters');
    $delimiter = substr($delimiters,0,1)." ";
    $keywords = implode($delimiter, $keywords);

    return $keywords;
}
?>
