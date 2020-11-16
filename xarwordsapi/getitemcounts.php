<?php
function keywords_wordsapi_getitemcounts(array $args=array())
{
    extract($args);

    if (isset($id) && (empty($id) || !is_numeric($id))) {
        $invalid[] = 'id';
    }

    if (isset($keyword)) {
        // we may have been given a string list
        if (!empty($keyword) && !is_array($keyword)) {
            $keyword = xarMod::apiFunc(
                'keywords',
                'admin',
                'separatekeywords',
                array(
                    'keywords' => $keyword,
                )
            );
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
        $vars = array(implode(', ', $invalid), 'keywords', 'wordsapi', 'getitemcounts');
        throw new BadParameterException($vars, $msg);
    }

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
    $orderby = array();
    $bindvars = array();

    $select['module_id'] = 'idx.module_id';
    $select['itemtype'] = 'idx.itemtype';
    $select['itemid'] = 'idx.itemid';
    $select['module'] = 'mods.name';
    $select['numwords'] = 'COUNT(DISTINCT words.keyword) as numwords';

    $from['idx'] = "$idxtable idx";
    $from['mods'] = "$modstable mods";
    $from['words'] = "$wordstable words";

    $where[] = 'mods.regid = idx.module_id';
    $where[] = 'idx.id = words.id';


    if (!empty($id)) {
        $where[] = 'words.id = ?';
        $bindvars[] = $id;
    }

    if (!empty($id)) {
        $where[] = 'words.id = ?';
        $bindvars[] = $id;
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

    if (!empty($skip_restricted)) {
        $where[] = 'idx.itemid != 0';
    }

    $groupby[] = 'idx.module_id';
    $groupby[] = 'idx.itemtype';
    $groupby[] = 'idx.itemid';

    $orderby[] = 'mods.name';
    $orderby[] = 'idx.itemtype';
    $orderby[] = 'idx.itemid';

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
    if (!empty($orderby)) {
        $query .= " ORDER BY " . implode(',', $orderby);
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

    $items = array();
    while ($result->next()) {
        $item = array();
        foreach (array_keys($select) as $field) {
            $item[$field] = array_shift($result->fields);
        }

        if (!empty($index_key) && isset($item[$index_key])) {
            $items[$item[$index_key]] = $item;
        } else {
            $items[] = $item;
        }
    }
    $result->close();

    return $items;
}
