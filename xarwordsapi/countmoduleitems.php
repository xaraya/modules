<?php

function keywords_wordsapi_countmoduleitems(array $args=[])
{
    extract($args);

    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $wordstable = $tables['keywords'];
    $idxtable = $tables['keywords_index'];
    $modstable = $tables['modules'];

    $select = [];
    $from = [];
    $join = [];
    $where = [];
    $groupby = [];
    $bindvars = [];

    $select['count'] = "COUNT(DISTINCT idx.module_id, idx.itemtype)";
    $from['idx'] = "$idxtable idx";

    if (!empty($skip_restricted)) {
        $where[] = 'idx.itemid != 0';
    }

    $query = "SELECT " . implode(',', $select);
    $query .= " FROM " . implode(',', $from);
    if (!empty($join)) {
        $query .= " " . implode(' ', $join);
    }
    if (!empty($where)) {
        $query .= " WHERE " . implode(' AND ', $where);
    }
    if (!empty($groupby)) {
        $query .= " GROUP BY " . implode(',', $groupby);
    }

    // return the count
    $result = $dbconn->Execute($query, $bindvars);
    [$numitems] = $result->fields;
    $result->Close();
    return $numitems;
}
