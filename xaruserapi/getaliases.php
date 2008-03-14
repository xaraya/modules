<?php

/**
 * Get a list of pages that are also module a alias.
 * Returns an array of page names that are also aliases.
 * @param mincount integer Minumum page count for each name, default=1
 */

function xarpages_userapi_getaliases($args)
{
    extract($args);

    $xartable = xarDB::getTables();
    $dbconn = xarDB::getConn();

    if (empty($mincount) || !is_numeric($mincount)) $mincount = 1;

    $query = 'SELECT xar_name, COUNT(xar_name)'
        . ' FROM ' . $xartable['xarpages_pages']
        . ' GROUP BY xar_name';

    $bind = array();

    if ($mincount > 1) {
        $query .= ' HAVING COUNT(xar_name) >= ?';
        $bind[] = (int)$mincount;
    }

    $result = $dbconn->execute($query, $bind);
    if (!$result) return;

    $return = array();

    while(!$result->EOF) {
        list($name, $name_count) = $result->fields;

        if (xarModGetAlias($name) == 'xarpages') {
            $return[$name] = $name_count;
        }

        $result->MoveNext();
    }

    return $return;
}

?>