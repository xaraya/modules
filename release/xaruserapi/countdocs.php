<?php

/**
 * count the number of docs per item
 * @returns integer
 * @returns number of docs for rid
 */
function release_userapi_countdocs($args)
{
    extract ($args);

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_docs'];

    $query = "SELECT COUNT(1)
            FROM $releasetable
            WHERE xar_rid = " . xarVarPrepForStore($rid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>