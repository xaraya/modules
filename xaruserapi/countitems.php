<?php

/**
 * count the number of links in the database
 * @param $args['tid'] ID of the link type, to count links of just one type
 * @returns integer
 * @returns number of links in the database
 */
function autolinks_userapi_countitems($args)
{
    extract($args);

    if (isset($tid) and is_numeric($tid)) {
        $where = 'WHERE xar_type_tid = ?';
        $bind = array($tid);
    } else {
        $where = '';
        $bind = array();
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];

    $query = 'SELECT COUNT(1) FROM ' . $autolinkstable . ' ' . $where;
    $result =& $dbconn->Execute($query, $bind);
    if (!$result) {return;}

    list($numitems) = $result->fields;

    $result->Close();

    return (int)$numitems;
}

?>