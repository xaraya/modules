<?php

/**
 * get the name and description of all publication types
 * @returns array
 * @return array(id => array('name' => name, 'descr' => descr)), or false on
 *         failure
 */
function articles_userapi_getpubtypes()
{
    static $pubtypes = array();

    if (count($pubtypes) > 0) {
        return $pubtypes;
    }

    // Get database setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubtypestable = $xartable['publication_types'];

    // Get item
    $query = "SELECT xar_pubtypeid,
                   xar_pubtypename,
                   xar_pubtypedescr,
                   xar_pubtypeconfig
            FROM $pubtypestable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->EOF) {
        return $pubtypes;
    }
    while (!$result->EOF) {
        list($id, $name, $descr, $config) = $result->fields;
        $pubtypes[$id] = array('name' => $name,
                               'descr' => $descr,
                               'config' => unserialize($config));
        $result->MoveNext();
    }

    return $pubtypes;
}

?>
