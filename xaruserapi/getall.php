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
function censor_userapi_getall($args)
{
    extract($args);
    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    $censors = array();
    // Security Check
    if(!xarSecurityCheck('ReadCensor')){
        return $censors;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $censortable = $xartable['censor'];
    // Get links
    $query = "SELECT xar_cid,
                   xar_keyword,
                   xar_case_sensitive,
                   xar_match_case,
                   xar_locale
            FROM $censortable
            ORDER BY xar_keyword";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($cid, $keyword,$case_sensitive,$match_case,$locale) = $result->fields;
        if (xarSecurityCheck('ReadCensor',0,'All',"$keyword:$cid")) {


        $censors[] = array('cid' => $cid,
                           'keyword' => $keyword,
                            'case_sensitive' => $case_sensitive,
                            'match_case' => $match_case,
                            'locale' => unserialize($locale));
        }
    }
    $result->Close();
    return $censors;
}
?>
