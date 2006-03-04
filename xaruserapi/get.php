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
 * get a specific link
 * @poaram $args['cid'] id of link to get
 * @returns array
 * @return link array, or false on failure
 */
function censor_userapi_get($args)
{
    extract($args);

    if (!isset($cid)) {
        $msg = xarML('Invalid Parameter Count in #(3)_#(1)_#(2).php', 'userapi', 'get', 'censor');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    if(!xarSecurityCheck('ReadCensor')) return;
    $censortable = $xartable['censor'];

    // Get link
    $query = "SELECT xar_cid,
                   xar_keyword,
                   xar_case_sensitive,
                   xar_match_case,
                   xar_locale
            FROM $censortable
            WHERE xar_cid = ?";
    $bindvars = array($cid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($cid, $keyword,$case_sensitive,$match_case,$locale) = $result->fields;
    $censor = array('cid'               => $cid,
                    'keyword'           => $keyword,
                    'case_sensitive'    => $case_sensitive,
                    'match_case'        => $match_case,
                    'locale'            => $locale);
    $result->Close();
    return $censor;
}
?>