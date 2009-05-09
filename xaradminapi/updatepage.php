<?php

/**
 * Update a page.
 *
 *  -- INPUT --
 * @param $args['pid'] the ID of the page
 * @param $args['name'] the modified name of the page
 * @param $args['desc'] the modified description of the page
 * @param $args['moving'] = 1 means the page can move to a new position
 *
 * If $args['moving'] != 1 then these shouldn´t be set:
 * @param $args['insertpoint'] the ID of the reference page
 *
 * This parameter is set in relationship with the reference page:
 * @param $args['offset'] The position in relation to the reference page
 *
 * @return true on success, false on failure
 *
 * @todo: changing itemtype is not supported by xarpages updatepage yet! (cfr. DD migrate)
 */

function xarpages_adminapi_updatepage($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($pid) || ($moving == 1 && (!isset($insertpoint) || !isset($offset)))
    ) {
        $msg = xarML('Bad Parameters');
        throw new BadParameterException(null,$msg);
    }

    // Get current information on the page
    $page = xarModAPIfunc('xarpages', 'user', 'getpage', array('pid' => $pid));

    if (empty($page)) {
        $msg = xarML('The page does not exist');
        throw new BadParameterException(null,$msg);
    }

    // Check we have minimum privs to edit this page.
    if (!xarSecurityCheck('EditXarpagesPage', 1, 'Page', $page['name'] . ':' . $page['pagetype']['name'])) {
        return;
    }

    // Certain changes can only be made if we have delete privilege on the page.
    // Null those arguments out if we do not have the privs.
    // TODO: determine if there are other changes that should be disabled.
    if (!xarSecurityCheck('DeleteXarpagesPage', 0, 'Page', $page['name'] . ':' . $page['pagetype']['name'])) {
        // We do not allow the page to be renamed or moved if we only have edit priv.
        // TODO: perhaps there are other [arbitrary] attibutes that we would like to
        // prevent the user from changing?
        unset($name);
        $moving = 0;
    }

    // Set the module alias if necessary.
    // If the name has changed, then remove any alias to the old page name.
    if (isset($name)) {
        // Only delete the alias if it belongs to this module.
        if (($page['name'] != $name || empty($alias)) && xarModGetAlias($page['name']) == 'xarpages') {
            xarModDelAlias($page['name'], 'xarpages');
        }
    }

    // If the alias flag is set, then set the alias.
    if (!empty($alias)) {
        // Use the current name if passed in, else use the existing name.
        // Only set if the alias is not currently being used by this or any other module.
        if (xarModGetAlias(isset($name) ? $name : $page['name']) != '') {
            xarModSetAlias((isset($name) ? $name : $page['name']), 'xarpages');
        }
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $tablename = $xartable['xarpages_pages'];

    // Move the item in the hierarchy/tree, if required.
    if (!empty($moving) && $moving == 1) {
        if (!xarModAPIfunc(
            'xarpages', 'tree', 'moveitem',
            array(
                'tablename' => $tablename,
                'idname' => 'xar_pid',
                'refid' => $insertpoint,
                'itemid' => $pid,
                'offset' => $offset
            )
        )) {return;}
    }

    // Data for the query.
    // Allow columns to be optional.
    $bind = array();
    $cols = array();

    // Include the optional parameters.
    foreach(array('name', 'desc', 'page_template', 'template', 'theme', 'encode_url', 'decode_url', 'function', 'status') as $colname) {
        if (isset($$colname) && is_string($$colname)) {
            $bind[] = (string)$$colname;
            $cols[] = 'xar_' . $colname . ' = ?';
        }
    }

    $bind[] = serialize($info);
    $cols[] = 'info = ?';

    $bind[] = (int)$pid;

    // Update name and description etc.
    $query = 'UPDATE ' . $tablename
        . ' SET ' . implode(', ', $cols)
        . ' WHERE xar_pid = ?';

    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    // If the status should be recursed to descendants, then do so.
    if (!empty($status_recurse) && !empty($status) && ($status == 'ACTIVE' || $status == 'INACTIVE')) {
        $query = 'UPDATE ' . $tablename
            . ' SET xar_status = ?'
            . ' WHERE xar_status <> ?'
            . ' AND (xar_status = \'ACTIVE\' OR xar_status = \'INACTIVE\')'
            . ' AND xar_left BETWEEN ? AND ?';
        $result = $dbconn->execute(
            $query,
            array(
                (string)$status, (string)$status,
                (int)$page['left'], (int)$page['right']
            )
        );
        if (!$result) {return;}
    }

    // Update hooks - by passing the original $args list, any DD fields will also be passed
    $args['module'] = 'xarpages';
    $args['itemtype'] = $page['itemtype'];
    $args['itemid'] = $pid;
    xarModCallHooks('item', 'update', $pid, $args);

    return true;
}

?>