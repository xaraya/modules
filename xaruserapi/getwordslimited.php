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
 * This function gets the restricted keywords for one module
 *
 * @param int $args['moduleid'] module id
 * @return array of keywords, sorted ASC
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function keywords_userapi_getwordslimited($args)
{
    if (!xarSecurity::check('ReadKeywords')) {
        return;
    }

    extract($args);

    if (!isset($moduleid) || !is_numeric($moduleid)) {
        $msg = xarML(
            'Invalid #(1) for #(2) function #(3)() in module #(4)',
            'module id',
            'user',
            'getwordslimited',
            'keywords'
        );

        xarErrorSet(
            XAR_USER_EXCEPTION,
            'BAD_PARAM',
            new SystemException($msg)
        );
        return;
    }


    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $keywordstable = $xartable['keywords_restr'];
    $bindvars = [];

    // Get restricted keywords for this module item

    $useitemtype = xarModVars::get('keywords', 'useitemtype');

    $query = "SELECT id,
                     keyword
             FROM $keywordstable ";
    if (!empty($useitemtype) && isset($itemtype)) {
        $query .= " WHERE module_id = '0' OR ( module_id= ? AND  itemtype = ? ) ORDER BY keyword ASC";
        $bindvars[] = $moduleid;
        $bindvars[] = $itemtype;
    } else {
        $query .= " WHERE module_id = '0' OR  module_id= ? ORDER BY keyword ASC";
        $bindvars[] = $moduleid;
    }


    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) {
        return;
    }
    if ($result->EOF) {
        $result->Close();
    }

    $keywords = [];

    while (!$result->EOF) {
        [$id,
             $word] = $result->fields;
        $keywords[$id] = $word;
        $result->MoveNext();
    }
    $result->Close();

    return $keywords;
}
