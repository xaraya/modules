<?php
function keywords_indexapi_countitems(Array $args=array())
{
    extract($args);

    if (isset($id) && !is_numeric($id))
        $invalid[] = 'id';

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
        $vars = array(implode(', ', $invalid), 'keywords', 'indexapi', 'countitems');
        throw new BadParameterException($vars, $msg);
    }

    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();
    $idxtable = $tables['keywords_index'];

    $select = array();
    $from = array();
    $join = array();
    $where = array();
    //$orderby = array();
    $groupby = array();
    $bindvars = array();

    $select['count'] = "COUNT(idx.id) as count";

    $from['idx'] = "$idxtable idx";

    if (isset($id)) {
        $where[] = 'idx.id = ?';
        $bindvars[] = $id;
    }

    if (!empty($module_id)) {
        $where[] = 'idx.module_id = ?';
        $bindvars[] = (int) $module_id;
    }

    if (isset($itemtype)) {
        $where[] = 'idx.itemtype = ?';
        $bindvars[] = $itemtype;
    }

    if (isset($itemid)) {
        $where[] = 'idx.itemid = ?';
        $bindvars[] = $itemid;
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
    $result = &$dbconn->Execute($query,$bindvars);
    list($numitems) = $result->fields;
    $result->Close();

    return $numitems;
}
?>