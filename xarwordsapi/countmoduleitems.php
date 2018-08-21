<?php
function keywords_wordsapi_countmoduleitems(Array $args=array())
{
    extract($args);

    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $wordstable = $tables['keywords'];
    $idxtable = $tables['keywords_index'];
    $modstable = $tables['modules'];

    $select = array();
    $from = array();
    $join = array();
    $where = array();
    $groupby = array();
    $bindvars = array();

    $select['count'] = "COUNT(DISTINCT idx.module_id, idx.itemtype)";
    $from['idx'] = "$idxtable idx";

    if (!empty($skip_restricted)) {
        $where[] = 'idx.itemid != 0';
    }

    $query = "SELECT " . implode(',', $select);
    $query .= " FROM " . implode(',', $from);
    if (!empty($join))
        $query .= " " . implode(' ', $join);
    if (!empty($where))
        $query .= " WHERE " . implode(' AND ', $where);
    if (!empty($groupby))
        $query .= " GROUP BY " . implode(',', $groupby);

    // return the count
    $result = $dbconn->Execute($query,$bindvars);
    list($numitems) = $result->fields;
    $result->Close();
    return $numitems;
}
?>