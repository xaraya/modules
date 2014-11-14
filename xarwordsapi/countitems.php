<?php
function keywords_wordsapi_countitems(Array $args=array())
{
    extract($args);

    if (isset($id) && (empty($id) || !is_numeric($id)))
        $invalid[] = 'id';

    if (isset($keyword)) {
        // we may have been given a string list
        if (!empty($keyword) && !is_array($keyword)) {
            $keyword = xarMod::apiFunc('keywords','admin','separatekeywords',
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
        $vars = array(implode(', ', $invalid), 'keywords', 'wordsapi', 'countitems');
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

    $select['count'] = "COUNT(DISTINCT words.id) as count";

    $from['words'] = "$wordstable words";

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
        $where[] = 'words.id = idx.keyword_id';
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