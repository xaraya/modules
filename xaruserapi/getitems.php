<?php
function fulltext_userapi_getitems($args)
{
    extract($args);

    $invalid = array();
    if (isset($module_id) && !is_numeric($module_id))
        $invalid[] = 'module_id';
    if (isset($itemtype) && !is_numeric($itemtype))
        $invalid[] = 'itemtype';
    if (isset($itemid) && !is_numeric($itemid))
        $invalid[] = 'itemid';
    if (isset($id) && !is_numeric($id))
        $invalid[] = 'id';
    if (!isset($q) && !is_string($q))
        $invalid[] = 'query';
    
    if (!empty($invalid)) {
        $vars = array(join(', ', $invalid), 'userapi', 'getitems', 'Fulltext');
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in #(4) module');
            throw new BadParameterException($vars, $msg);
    }

    // Get database information
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $ftable = $tables['fulltext'];
    $select = array();
    $where = array();
    $orderby = array();
    $bindvars = array();

    $select['id'] = 'ft.id';
    $select['module_id'] = 'ft.module_id';
    $select['itemtype'] = 'ft.itemtype';
    $select['itemid'] = 'ft.item_id';
    $select['text'] = 'ft.text';

    if (!empty($q)) {
        $select['score'] = 'MATCH(ft.text) AGAINST (?) AS score';
        $bindvars[] = $q;
    }   
    
    if (!empty($module_id)) {
        $where[] = 'ft.module_id = ?';
        $bindvars[] = $module_id;
    }
    if (!empty($itemtype)) {
        $where[] = 'ft.itemtype = ?';
        $bindvars[] = $itemtype;
    }
    if (!empty($itemid)) {
        $where[] = 'ft.item_id = ?';    
        $bindvars[] = $itemid;
    }
    if (!empty($id)) {
        $where[] = 'ft.id = ?';
        $bindvars[] = $id;
    }

    if (!empty($q)) {
        $where[] = 'MATCH(ft.text) AGAINST (?)';
        $bindvars[] = $q;
        if (empty($orderby['score']))
            $orderby['score'] = 'score DESC';
    }       
 
    $query = "SELECT " . join(',',$select);
    $query .= " FROM $ftable ft";   
    if (!empty($where))
        $query .= " WHERE " . join(" AND ", $where);
    if (!empty($orderby)) 
        $query .= ' ORDER BY ' . join(',', $orderby);  

    $stmt = $dbconn->prepareStatement($query);
    if (!empty($numitems)) {
        $stmt->setLimit($numitems);
        if (empty($startnum))
            $startnum = 1;
        $stmt->setOffset($startnum - 1);
    }
    $result = $stmt->executeQuery($bindvars);
    if (!$result) return;
    
    $items = array();
    while($result->next()) {
        $item = array();
        foreach (array_keys($select) as $field) 
            $item[$field] = array_shift($result->fields);
        $items[] = $item;
    }
    $result->close();

    return $items;    
}
?>