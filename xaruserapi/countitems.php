<?php
function fulltext_userapi_countitems(Array $args=array())
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
        $vars = array(join(', ', $invalid), 'userapi', 'countitems', 'Fulltext');
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in #(4) module');
            throw new BadParameterException($vars, $msg);
    }
    
    // Get database information
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $ftable = $tables['fulltext'];
    $where = array();
    $bindvars = array();

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
        $where[] = "MATCH(ft.text) AGAINST(?)";
        $bindvars[] = $q;
    }

    $query = "SELECT COUNT(ft.id)
        FROM $ftable ft  
    ";
    if (!empty($where))
        $query .= ' WHERE ' . join(' AND ', $where);    

    $result = $dbconn->Execute($query,$bindvars);
    if (!$result) return;    
    list($count) = $result->fields;
    $result->Close();
    
    return $count;
}
?>