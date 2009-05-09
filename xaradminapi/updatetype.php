<?php

/**
 * Update a page type.
 *
 *  -- INPUT --
 * @param $args['id'] the ID of the page type
 * @param $args['name'] the modified name of the page
 * @param $args['description'] the modified description of the page
 *
 */
function xarpages_adminapi_updatetype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($id)) {
        $msg = xarML('Bad Parameters');
        throw new BadParameterException(null,$msg);
    }

    // Get current information on the page type
    $type = xarModAPIfunc('xarpages', 'user', 'get_type', array('id' => $id));

    if (empty($type)) {
        $msg = xarML('The page type "#(1)" does not exist', $id);
        throw new BadParameterException(null,$msg);
    }

    // Security: allowed to create page types?
    if (!xarSecurityCheck('EditXarpagesPagetype', 1, 'Pagetype', $type['name'])) {
        return;
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $tablename = $xartable['xarpages_types'];

    // Data for the query.
    // Allow columns to be optional.
    $bind = array();
    $cols = array();

    // Include the optional parameters.
    foreach(array('name', 'description', 'info') as $colname) {
        if (isset($$colname) && is_string($$colname)) {
            $bind[] = $$colname;
            $cols[] = $colname . ' = ?';
        }
    }
    $bind[] = serialize($info);
    $cols[] = 'info = ?';

    $bind[] = (int)$id;

    // Update name and description etc.
    $query = 'UPDATE ' . $tablename
        . ' SET ' . implode(', ', $cols)
        . ' WHERE id = ?';

    $result = $dbconn->execute($query, $bind);
    if (!$result) return;

    $type_itemtype = xarModAPIfunc('xarpages', 'user', 'gettypeitemtype');

    // Call update hooks (for page type as a type).
    xarModCallHooks(
        'item', 'update', $id,
        array('module' => 'xarpages', 'itemtype' => $type_itemtype)
    );

    // Call config hooks (for page type as an itemtype)
    xarModCallHooks(
        'module', 'updateconfig', 'xarpages',
        array('itemtype' => $id, 'module' => 'xarpages')
    );


    return true;
}

?>