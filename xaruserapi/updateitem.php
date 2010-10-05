<?php
function fulltext_userapi_updateitem($args)
{
    extract($args);

    $invalid = array();
    if (isset($id)) {
        if (!is_numeric($id))
            $invalid[] = 'id';
    } else {
        if (!isset($module_id) || !is_numeric($module_id))
            $invalid[] = 'module_id';
        if (isset($itemtype) && !is_numeric($itemtype))
            $invalid[] = 'itemtype';
        if (!isset($itemid) || !is_numeric($itemid))
            $invalid[] = 'itemid';
    }
    if (!isset($text) || !is_string($text))
        $invalid[] = 'text';

    if (!empty($invalid)) {
        $vars = array(join(', ', $invalid), 'user api', 'updateitem', 'Fulltext');
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in #(4) module');
            throw new BadParameterException($vars, $msg);
    }

    // Get database information
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $ftable = $tables['fulltext'];
    $where = array();
    $bindvars = array();
    try {
        $dbconn->begin();
        $query = "UPDATE $ftable ft
                  SET ft.text = ?";
        $bindvars[] = $text;
        if (!empty($id)) {
            $where[] = 'ft.id = ?';
            $bindvars[] = $id;
        } else {
            $where[] = 'ft.module_id = ?';
            $bindvars[] = $module_id;
            $where[] = 'ft.itemtype = ?';
            $bindvars[] = empty($itemtype) ? 0 : $itemtype;
            $where[] = 'ft.item_id = ?';
            $bindvars[] = $itemid;
        }
        $query .= " WHERE " . join(" AND ", $where);        
        $stmt = $dbconn->prepareStatement($query);
        $stmt->executeUpdate($bindvars);
        $dbconn->commit();
    } catch (SQLException $e) {
        $dbconn->rollback();
        throw $e;
    }

    return true;    
}
?>