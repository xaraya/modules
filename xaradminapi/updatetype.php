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
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Get current information on the page type
    $type = xarMod::apiFunc('xarpages', 'user', 'gettype', ['ptid' => $ptid]);

    if (empty($type)) {
        $msg = xarML('The page type "#(1)" does not exist', $ptid);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security: allowed to create page types?
    if (!xarSecurity::check('EditXarpagesPagetype', 1, 'Pagetype', $type['name'])) {
        return;
    }

    // Get database setup
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $tablename = $xartable['xarpages_types'];

    // Data for the query.
    // Allow columns to be optional.
    $bind = [];
    $cols = [];

    // Include the optional parameters.
    foreach (['name', 'desc'] as $colname) {
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
    if (!$result) {
        return;
    }

    $type_itemtype = xarMod::apiFunc('xarpages', 'user', 'gettypeitemtype');

    // Call update hooks (for page type as a type).
    xarModHooks::call(
        'item',
        'update',
        $ptid,
        ['module' => 'xarpages', 'itemtype' => $type_itemtype]
    );

    // Call config hooks (for page type as an itemtype)
    xarModHooks::call(
        'module',
        'updateconfig',
        'xarpages',
        ['itemtype' => $ptid, 'module' => 'xarpages']
    );


    return true;
}
