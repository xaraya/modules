<?php
/*
 * Create a survey question group.
 * It is added to the group hierarchy.
 * Mandatory columns:
 *  name: group name
 *  desc: group description
 *  insertpoint: ID of group inserting relative to
 *  offset: relationship to insertpoint ('after', 'before', 'firstchild', 'lastchild')
 * TODO: allow explicit DD fields to be passed into this API
 * TODO: check the page type is valid
 * TODO: default most values and raise an error in missing mandatory values
 */

function xarpages_adminapi_createtype($args) {
    extract($args);

    // TODO: validate name (mandatory and unique)

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $tablename = $xartable['xarpages_types'];

    // Data for the query.
    $bind = array();
    $cols = array();

    // Include the optional parameters.
    foreach(array('desc') as $colname) {
        if (isset($$colname) && is_string($$colname)) {
            $bind[] = $$colname;
            $cols[] = 'xar_' . $colname;
        }
    }

    $bind[] = $name;
    $cols[] = 'xar_name';

    // Insert the page
    $nextID = $dbconn->GenId($tablename);
    $bind[] = $nextID;
    $cols[] = 'xar_ptid';

    $query = 'INSERT INTO ' . $tablename
        . '(' .implode(', ', $cols). ')'
        . ' VALUES(?' . str_repeat(',?', count($cols)-1) . ')';

    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    $ptid = $dbconn->PO_Insert_ID($tablename, 'xar_ptid');

    // Hooks: we have created an instance of the 'page type' type.

    // Get the itemtype of the page type.
    $type_itemtype = xarModAPIfunc('xarpages', 'user', 'gettypeitemtype');

    // Create hooks.
    xarModCallHooks(
        'item', 'create', $ptid,
        array(
            'itemtype' => $type_itemtype,
            'module' => 'xarpages',
            'urlparam' => 'ptid'
        )
    );

    return $ptid;
}

?>