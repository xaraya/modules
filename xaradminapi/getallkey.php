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
 * @param $args['modid'] module id
 * @returns array
 * @return array of keywords
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_adminapi_getallkey($args)
{
    extract($args);

    if (!isset($moduleid) || !is_numeric($moduleid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module id', 'user', 'getwordslimited', 'keywords');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurity::check('AdminKeywords')) return;
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $keywordstable = $xartable['keywords_restr'];
    // Get restricted keywords for this module item
    $query = "SELECT id,
                     keyword
              FROM $keywordstable
              WHERE module_id = ?
              OR module_id = '0'
              ORDER BY keyword ASC";
    $result =& $dbconn->Execute($query,array($moduleid));
    if (!$result) return;

    $keywords = array();

    //$keywords[''] = '';
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
    return $keywords;
}
?>