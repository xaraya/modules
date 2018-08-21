<?php
function keywords_wordsapi_countwords(Array $args=array())
{
    extract($args);

    if (!empty($module))
        $module_id = xarMod::getRegId($module);
    if (isset($module_id) && (empty($module_id) || !is_numeric($module_id)))
        $invalid[] = 'module_id';

    if (isset($itemtype) && !is_numeric($itemtype))
        $invalid[] = 'itemtype';

    if (isset($itemid) && !is_numeric($itemid))
        $invalid[] = 'itemid';


    if (!empty($invalid)) {
        $msg = 'Invalid #(1) for #(2) module #(3) function #(4)()';
        $vars = array(implode(', ', $invalid), 'keywords', '', '');
        throw new BadParameterException($vars, $msg);
    }

    // count of unique keywords
    // optionally by module/itemtype

    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $wordstable = $tables['keywords'];
    $idxtable = $tables['keywords_index'];

    $select = array();
    $from = array();
    $join = array();
    $where = array();
    $groupby = array();
    $bindvars = array();

    $select['count'] = "COUNT(DISTINCT words.keyword) AS wordcount";

    $from['words'] = "$wordstable words";

    if (!empty($module_id)) {
        $from['idx'] = "$idxtable idx";
        $where[] = 'idx.module_id = ?';
        $bindvars[] = (int) $module_id;
    }

    if (isset($itemtype)) {
        $from['idx'] = "$idxtable idx";
        $where[] = 'idx.itemtype = ?';
        $bindvars[] = $itemtype;
    }

    if (isset($itemid)) {
        $from['idx'] = "$idxtable idx";
        $where[] = 'idx.itemid = ?';
        $bindvars[] = $itemid;
    }

    if (!empty($skip_restricted)) {
        $from['idx'] = "$idxtable idx";
        $where[] = '(idx.module_id != ? OR idx.itemid != 0)';
        $bindvars[] = xarMod::getRegId('keywords');
    }

    if (!empty($from['idx'])) {
        $where[] = 'words.index_id = idx.id';
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