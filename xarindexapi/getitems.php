<?php

function keywords_indexapi_getitems(array $args=[])
{
    extract($args);

    if (isset($id) && !is_numeric($id)) {
        $invalid[] = 'id';
    }

    if (!empty($module)) {
        $module_id = xarMod::getRegId($module);
    }
    if (isset($module_id) && (empty($module_id) || !is_numeric($module_id))) {
        $invalid[] = 'module_id';
    }

    if (isset($itemtype) && !is_numeric($itemtype)) {
        $invalid[] = 'itemtype';
    }

    if (isset($itemid) && !is_numeric($itemid)) {
        $invalid[] = 'itemid';
    }

    if (!empty($invalid)) {
        $msg = 'Invalid #(1) for #(2) module #(3) function #(4)()';
        $vars = [implode(', ', $invalid), 'keywords', 'indexapi', 'getitems'];
        throw new BadParameterException($vars, $msg);
    }

    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $idxtable = $tables['keywords_index'];

    $select = [];
    $from = [];
    $join = [];
    $where = [];
    $orderby = [];
    $groupby = [];
    $bindvars = [];

    $select['id'] = 'idx.id';
    $select['module_id'] = 'idx.module_id';
    $select['itemtype'] = 'idx.itemtype';
    $select['itemid'] = 'idx.itemid';

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
    if (!empty($join)) {
        $query .= " " . implode(' ', $join);
    }
    if (!empty($where)) {
        $query .= " WHERE " . implode(' AND ', $where);
    }
    if (!empty($orderby)) {
        $query .= " ORDER BY " . implode(',', $orderby);
    }
    if (!empty($groupby)) {
        $query .= " GROUP BY " . implode(',', $groupby);
    }

    $stmt = $dbconn->prepareStatement($query);
    if (!empty($numitems)) {
        $stmt->setLimit($numitems);
        if (empty($startnum)) {
            $startnum = 1;
        }
        $stmt->setOffset($startnum - 1);
    }
    $result = $stmt->executeQuery($bindvars);

    $items = [];
    while ($result->next()) {
        $item = [];
        foreach (array_keys($select) as $field) {
            $item[$field] = array_shift($result->fields);
        }


        $items[] = $item;
    }
    $result->close();

    return $items;
}
