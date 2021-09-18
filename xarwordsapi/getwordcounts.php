<?php
/**
 * getwordcounts
 * Get a list of words in the db, with a count of the number of occurences for each word
**/
/**
 * @access public
 * @param array   $args
 * @param string  $args[module]
 * @param integer $args[module_id]
 * @param integer $args[itemtype]
 * @param integer $args[itemid]
 * @param bool    $args[skip_restricted]
 * @param integer $args[startnum]
 * @param integer $args[numitems]
 * @param mixed   $args[keyword]
 * @param mixed   $args[sort]
 * @param string  $args[index_key]
 * @return array
 * @throws BadParameterException, SQLException
**/
function keywords_wordsapi_getwordcounts(array $args=[])
{
    extract($args);

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
        $vars = [implode(', ', $invalid), 'keywords', 'wordsapi', 'getwordcounts'];
        throw new BadParameterException($vars, $msg);
    }

    // list of unique keywords, with density count
    // optionally by module/itemtype
    // sort on name or count

    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $wordstable = $tables['keywords'];
    $idxtable = $tables['keywords_index'];

    $select = [];
    $from = [];
    $join = [];
    $where = [];
    $groupby = [];
    $orderby = [];
    $bindvars = [];

    $select['keyword'] = "words.keyword";
    $select['count'] = "COUNT(words.keyword) AS wordcount";

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
        $where[] = 'words.id = idx.keyword_id';
    }

    $groupby['keyword'] = 'words.keyword';

    if (empty($orderby)) {
        $orderby['keyword'] = 'words.keyword ASC';
    }
    //$orderby['count'] = 'wordcount DESC';


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
