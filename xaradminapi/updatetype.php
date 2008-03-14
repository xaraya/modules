<?php

/**
 * Update a page type.
 *
 *  -- INPUT --
 * @param $args['ptid'] the ID of the page type
 * @param $args['name'] the modified name of the page
 * @param $args['desc'] the modified description of the page
 *
 */
function xarpages_adminapi_updatetype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($ptid)) {
        $msg = xarML('Bad Parameters');
        throw new BadParemeterException(null,$msg);
    }

    // Get current information on the page type
    $type = xarModAPIfunc('xarpages', 'user', 'gettype', array('ptid' => $ptid));

    if (empty($type)) {
        $msg = xarML('The page type "#(1)" does not exist', $ptid);
        throw new BadParemeterException(null,$msg);
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
    foreach(array('name', 'desc') as $colname) {
        if (isset($$colname) && is_string($$colname)) {
            $bind[] = $$colname;
            $cols[] = 'xar_' . $colname . ' = ?';
        }
    }

    $bind[] = (int)$ptid;

    // Update name and description etc.
    $query = 'UPDATE ' . $tablename
        . ' SET ' . implode(', ', $cols)
        . ' WHERE xar_ptid = ?';

    $result = $dbconn->execute($query, $bind);
    if (!$result) return;

    $type_itemtype = xarModAPIfunc('xarpages', 'user', 'gettypeitemtype');

    // Call update hooks (for page type as a type).
    xarModCallHooks(
        'item', 'update', $ptid,
        array('module' => 'xarpages', 'itemtype' => $type_itemtype)
    );

    // Call config hooks (for page type as an itemtype)
    xarModCallHooks(
        'module', 'updateconfig', 'xarpages',
        array('itemtype' => $ptid, 'module' => 'xarpages')
    );


    return true;
}

?>