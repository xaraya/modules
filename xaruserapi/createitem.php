<?php
function fulltext_userapi_createitem($args)
{
    extract($args);

    if (empty($itemtype))
        $itemtype = 0;
    if (empty($text))
        $text = "";
    
    $invalid = array();
    if (empty($module_id) || !is_numeric($module_id))
        $invalid[] = 'module_id';
    if (!empty($itemtype) && !is_numeric($itemtype))
        $invalid[] = 'itemtype';
    if (empty($itemid) || !is_numeric($itemid))
        $invalid[] = 'itemid';
    if (!empty($text) && !is_string($text))
        $invalid[] = 'text';
    if (!empty($invalid)) {
        $vars = array(join(', ', $invalid), 'user api', 'createitem', 'Fulltext');
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in #(4) module');
            throw new BadParameterException($vars, $msg);
    }
    // see if item already exists
    // @checkme: necessary to throw an exception here if duplicate found ?
    // @checkme: could skip this and just let the db blow up on duplicate insert
    $exists = xarMod::apiFunc('fulltext', 'user', 'getitem',
        array(
            'module_id' => $module_id,
            'itemtype' => $itemtype,
            'itemid' => $itemid,
        )); 
    // return the existing item (for now, see checkme)
    if (!empty($exists)) return $exists;
    
    // Get database information
    $dbconn = xarDB::getConn();
    $tables =& xarDB::getTables();
    $ftable = $tables['fulltext'];
    
    // Insert item
    try {
        $dbconn->begin();
        $nextId = $dbconn->GenId($ftable);
        $query = "INSERT INTO $ftable
                  (id, module_id, itemtype, item_id, text)
                  VALUES (?,?,?,?,?)";
        $bindvars = array($nextId, $module_id, $itemtype, $itemid, $text);
        $stmt = $dbconn->prepareStatement($query);
        $result = $stmt->executeUpdate($bindvars);
        $dbconn->commit();
    } catch (SQLException $e) {
        $dbconn->rollback();
        throw $e;
    }
    $id = $dbconn->PO_Insert_ID($ftable, 'id');
    if (empty($id)) return;
    // return item to caller (saves a further getitem call in itemupdate hook function)   
    $item = array(
        'id' => $id,
        'module_id' => $module_id,
        'itemtype' => $itemtype,
        'itemid' => $itemid,
        'text' => $text,
    );
    return $item;                             
}
?>