<?php

/* test function for DMOZ-style short URLs in xaruser.php */

function categories_userapi_name2cid ($args)
{
    extract($args);
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categoriestable = $xartable['categories'];

    if (empty($name) || !is_string($name)) {
        $name = 'Top';
    }
    // for DMOZ-like URLs where the description contains the full path
    if (!empty($usedescr)) {
        $query = "SELECT xar_parent, xar_cid FROM $categoriestable WHERE xar_description = '"
                 . xarVarPrepForStore($name) . "'";
    } else {
        $query = "SELECT xar_parent, xar_cid FROM $categoriestable WHERE xar_name = '"
                 . xarVarPrepForStore($name) . "'";
    }
    $result = $dbconn->Execute($query);
    if (!$result) return;
    list($parent,$cid) = $result->fields;
    $result->Close();

    return $cid;
}

?>
