<?php
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
                   xar_match_case,
                   xar_locale
            FROM $censortable
            WHERE xar_locale = 'ALL'  
            OR xar_locale ='" . xarVarPrepForStore($local) . "'";
            
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($cid, $keyword,$case_sensitive,$match_case,$locale) = $result->fields;
        if (xarSecurityCheck('ReadCensor',0,'All',"$keyword:$cid")) {
        $censors[] = array('cid' => $cid,
                           'keyword' => $keyword,
                           'case_sensitive' => $case_sensitive,
                           'match_case' => $match_case,
                           'locale' => $locale);
        }
    }
    $result->Close();
    return $censors;
}

?>