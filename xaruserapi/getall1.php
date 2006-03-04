<?php
/*
 * Censor Module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
*/

/**
 * get all links
 * @returns array
 * @return array of links, or false on failure
 */
function censor_userapi_getall1($args)
{
    extract($args);
    $censors = array();
    if(!xarSecurityCheck('ReadCensor')){
        return $censors;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $censortable = $xartable['censor'];
    $query = "SELECT xar_cid,
                   xar_keyword,
                   xar_case_sensitive,
                   xar_match_case
            FROM $censortable
            WHERE xar_locale LIKE ?
            OR xar_locale LIKE ?";

    $bindvars = array("%".$local."%", "%ALL%");
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($cid, $keyword,$case_sensitive,$match_case) = $result->fields;
        if (xarSecurityCheck('ReadCensor',0,'All',"$keyword:$cid")) {
        $censors[] = array('cid' => $cid,
                           'keyword' => $keyword,
                           'case_sensitive' => $case_sensitive,
                           'match_case' => $match_case);
        }
    }
    $result->Close();
    return $censors;
}
?>
