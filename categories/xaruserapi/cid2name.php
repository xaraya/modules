<?php

/* test function for DMOZ-style short URLs in xaruser.php */

function categories_userapi_cid2name ($args)
{
    extract($args);
    list($dbconn) = xarDBGetConn();
    if (empty($cid) || !is_numeric($cid)) {
        $cid = 1;
    }
    $query = "SELECT xar_parent, xar_description FROM xar_categories WHERE xar_cid = '"
             . xarVarPrepForStore($cid) . "'";
    $result = $dbconn->Execute($query);
    if (!$result) return;

    list($parent,$name) = $result->fields;
    $result->Close();

    $name = rawurlencode($name);
    $name = preg_replace('/%2F/','/',$name);
    return $name;
}

?>
