<?php

/* test function for DMOZ-style short URLs in xaruser.php */

function categories_userapi_cid2name ($args)
{
    extract($args);
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categoriestable = $xartable['categories'];

    if (empty($cid) || !is_numeric($cid)) {
        $cid = 1;
    }
    // for DMOZ-like URLs where the description contains the full path
    if (!empty($usedescr)) {
        $query = "SELECT xar_parent, xar_description FROM $categoriestable WHERE xar_cid = '"
                 . xarVarPrepForStore($cid) . "'";
    } else {
        $query = "SELECT xar_parent, xar_name FROM $categoriestable WHERE xar_cid = '"
                 . xarVarPrepForStore($cid) . "'";
    }
    $result = $dbconn->Execute($query);
    if (!$result) return;

    list($parent,$name) = $result->fields;
    $result->Close();

    $name = rawurlencode($name);
    $name = preg_replace('/%2F/','/',$name);
    return $name;
}

?>
