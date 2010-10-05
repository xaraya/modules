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
    
    if (!empty($invalid)) {
        $vars = array(join(', ', $invalid), 'user api', 'getitems', 'Fulltext');
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in #(4) module');
            throw new BadParameterException($vars, $msg);
    }

    // Get database information
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $ftable = $tables['fulltext'];
    $where = array();
    $bindvars = array();

    $query = "SELECT ft.id, ft.module_id, ft.itemtype, ft.item_id, ft.text
              FROM $ftable ft";
    
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
    if (!empty($where))
        $query .= " WHERE " . join(" AND ", $where);

    $stmt = $dbconn->prepareStatement($query);
    $result = $stmt->executeQuery($bindvars);
    if (!$result) return;
    
    $items = array();
    while($result->next()) {
        list($id, $module_id, $itemtype, $itemid, $text) = $result->fields;
        $items[] = array(
            'id' => $id,
            'module_id' => $module_id,
            'itemtype' => $itemtype,
            'itemid' => $itemid,
            'text' => $text,
        );
    }
    $result->close();
    return $items;
    
}
?>