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

function xarpages_adminapi_createpage($args) {
    extract($args);

    // TODO: validate name (mand and unique)

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $idname = 'xar_pid';
    $tablename = $xartable['xarpages_pages'];

    // Data for the query.
    $bind = array();
    $cols = array();

    // Include the optional parameters.
    foreach(array('desc', 'template', 'theme', 'encode_url', 'decode_url', 'function', 'status') as $colname) {
        if (isset($$colname) && is_string($$colname)) {
            $bind[] = $$colname;
            $cols[] = 'xar_' . $colname;
        }
    }

    // Open a space in the pages hierarchy.
    // Position in the hierarchy defined by args: insertpoint and offset
    $gap = xarModAPIfunc(
        'xarpages', 'tree', 'insertprep',
        array_merge(
            $args,
            array('tablename' => $tablename, 'idname' => 'xar_pid')
        )
    );

    $bind[] = (int)$gap['parent'];
    $cols[] = 'xar_parent';

    $bind[] = (int)$gap['left'];
    $cols[] = 'xar_left';

    $bind[] = (int)$gap['right'];
    $cols[] = 'xar_right';

    $bind[] = (int)$itemtype;
    $cols[] = 'xar_itemtype';

    $bind[] = $name;
    $cols[] = 'xar_name';

    if (!empty($gap)) {
        // Insert the page
        $nextID = $dbconn->GenId($tablename);
        $bind[] = $nextID;
        $cols[] = 'xar_pid';

        $query = 'INSERT INTO ' . $tablename
            . '(' .implode(', ', $cols). ')'
            . ' VALUES(?' . str_repeat(',?', count($cols)-1) . ')';

        $result = $dbconn->execute($query, $bind);
        if (!$result) {return;}

        $pid = $dbconn->PO_Insert_ID($tablename, $idname);
    }

    // Create hooks.
    xarModCallHooks(
        'item', 'create', $pid,
        array(
            'itemtype' => $itemtype,
            'module' => 'xarpages',
            'urlparam' => 'pid'
        )
    );

    // Set this page as a module alias if necessary.
    if (!empty($alias)) {
        xarModSetAlias($name, 'xarpages');
    }

    return $pid;
}

?>