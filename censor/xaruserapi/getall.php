<?php
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
                   xar_keyword
            FROM $censortable
            ORDER BY xar_keyword";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($cid, $keyword) = $result->fields;
        if (xarSecurityCheck('ReadCensor',0,'All',"$keyword:$cid")) {
        $censors[] = array('cid' => $cid,
                           'keyword' => $keyword);
        }
    }

    $result->Close();

    return $censors;
}

?>