<?php
function keywords_wordsapi_getitems(Array $args=array())
{
    extract($args);

    if (isset($id) && (empty($id) || !is_numeric($id)))
        $invalid[] = 'id';

    if (isset($index_id) && (empty($index_id) || !is_numeric($index_id)))
        $invalid[] = 'index_id';

    if (isset($keyword)) {
        // we may have been given a string list
        if (!empty($keyword) && !is_array($keyword)) {
            $keyword = xarModAPIFunc('keywords','admin','separekeywords',
                array(
                    'keywords' => $keyword,
                ));
        }
        if (is_array($keyword)) {
            foreach ($keyword as $dt) {
                if (!is_string($dt)) {
                    $invalid[] = 'keyword';
                    break;
                }
            }
        } else {
            $invalid[] = 'keyword';
        }
    }

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
        $vars = array(implode(', ', $invalid), 'keywords', 'wordsapi', 'getitems');
        throw new BadParameterException($vars, $msg);
    }

    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();
    $wordstable = $tables['keywords'];
    $idxtable = $tables['keywords_index'];
    $modstable = $tables['modules'];

    $select = array();
    $from = array();
    $join = array();
    $where = array();
    $orderby = array();
    $groupby = array();
    $bindvars = array();

    $select['id'] = 'words.id';
    $select['index_id'] = 'words.index_id';
    $select['keyword'] = 'words.keyword';

    $from['words'] = "$wordstable words";

    if (!empty($id)) {
        $where[] = 'words.id = ?';
        $bindvars[] = $id;
    }

    if (!empty($index_id)) {
        $where[] = 'words.index_id = ?';
        $bindvars[] = $index_id;
    }

    if (!empty($keyword)) {
        if (count($keyword) == 1) {
            $where[] = 'words.keyword = ?';
            $bindvars[] = $keyword[0];
        } else {
            $where[] = 'words.keyword IN (' . implode(',', array_fill(0, count($keyword), '?')) . ')';
            $bindvars = array_merge($bindvars, $keyword);
        }
    }

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
        $where[] = 'idx.itemid != 0';
    }

    if (!empty($from['idx'])) {
        $select['module_id'] = 'idx.module_id';
        $select['itemtype'] = 'idx.itemtype';
        $select['itemid'] = 'idx.itemid';
        $where[] = 'words.index_id = idx.id';
        $select['module'] = 'mods.name';
        $from['mods'] = "$modstable mods";
        $where[] = 'mods.regid = idx.module_id';
    }

    if (empty($orderby))
        $orderby[] = 'words.keyword ASC';

    $query = "SELECT " . implode(',', $select);
    $query .= " FROM " . implode(',', $from);
    if (!empty($join))
        $query .= " " . implode(' ', $join);
    if (!empty($where))
        $query .= " WHERE " . implode(' AND ', $where);
    if (!empty($orderby))
        $query .= " ORDER BY " . implode(',', $orderby);
    if (!empty($groupby))
        $query .= " GROUP BY " . implode(',', $groupby);

    $stmt = $dbconn->prepareStatement($query);
    if (!empty($numitems)) {
        $stmt->setLimit($numitems);
        if (empty($startnum))
            $startnum = 1;
        $stmt->setOffset($startnum - 1);
    }
    $result = $stmt->executeQuery($bindvars);

    $items = array();
    while ($result->next()) {
        $item = array();
        foreach (array_keys($select) as $field)
            $item[$field] = array_shift($result->fields);
        $items[] = $item;
    }
    $result->close();

    return $items;
}
?>