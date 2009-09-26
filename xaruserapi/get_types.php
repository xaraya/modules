<?php

// Get page types.
// include_system: return system page types (those whose name starts with '@')
// name: select just 'name' page type
// ptid: select just 'id' page type
// no_cache: do not retrieve from the cache; still writes to the cache though
// dd_flag: boolean indicates whether dd should be retrived (default true)
// DD is always fetched if hooked
// Results are cached if all page types are selected

function xarpages_userapi_get_types($args)
{
    static $static_all_pagetypes = NULL;

    extract($args);

    // Possible values for the array key. Defaults to 'index' (count incrementing from zero)
    if (!xarVarValidate('enum:id:index:name', $key, true)) {
        $key = 'index';
    }

    $xartable = xarDB::getTables();
    $dbconn = xarDB::getConn();

    $where = array();
    $bind = array();

    // Default dynamic data retrieval to true.
    if (!isset($dd_flag)) {
        $dd_flag = true;
    }

    if (isset($name)) {
        $where[] = 'name = ?';
        $bind[] = (string)$name;
    }

    if (isset($id)) {
        $where[] = 'id = ?';
        $bind[] = (int)$id;
    }

    // Check the cache if suitable. Returned cached details if we can.
    // TODO: if cached, it should be possible to return individual values from
    // the cached array.
    if (empty($where) && isset($static_all_pagetypes[$key]) && empty($no_cache)) {
        return $static_all_pagetypes[$key];
    }

    // Always select the system page types - those starting with '@'.
    $query = 'SELECT id, name, description, info'
        . ' FROM ' . $xartable['xarpages_types']
        . (!empty($where) ? ' WHERE (' . implode(' AND ', $where) . ') OR name LIKE \'@%\'' : '')
        . ' ORDER BY name ASC';

    $result = $dbconn->execute($query, $bind);
    if (!$result) return;

    $types = array();
    $itemtype = 0;
    $index = 0;

    while (!$result->EOF) {
        list($id, $name, $description, $info) = $result->fields;

        // Only return the system page types if specifically requested.
        if ($name[0] != '@' || !empty($include_system)) {
            $types[$$key] = array(
                'id' => (int)$id,
                'name' => $name,
                'description' => $description,
                'info' => unserialize($info)
            );
        }

        // The '@pagetype' page type is the itemtype for page types.
        // This allows DD to be added to all page types for extending
        // their functionality.
        if ($name == '@pagetype') {
            $itemtype = $id;
        }

        // Get the next page type.
        $result->MoveNext();
        $index += 1;
    }

    // If we have an itemtype and are hooked to DD, then
    // fetch some DD data for each user-defined page type.
    if ($dd_flag && !empty($itemtype) && xarModIsHooked('dynamicdata', 'xarpages', $itemtype)) {
        // Collect the item IDs together
        $item_ids = array();
        foreach($types as $type_key => $type) {
            if ($type['name'][0] != '@') {
                $item_ids[$type['id']] = $type_key;
            }
        }

        // Fetch the DD fields for all page types in one go.
        if (!empty($item_ids)) {
            $dd_data = xarMod::apiFunc(
                'dynamicdata', 'user', 'getitems',
                array('module' => 'xarpages', 'itemtype' => $itemtype, 'itemids' => array_keys($item_ids))
            );

            // Move the DD fields to the types array.
            if (is_array($dd_data)) {
                foreach($dd_data as $dd_key => $dd_items) {
                    $types[$item_ids[$dd_key]]['dd'] = $dd_items;
                }
            }
        }
    }

    // Save in the cache if required.
    if (!empty($where)) {
        $static_all_pagetypes[$key] = $types;
    }

    return($types);
}

?>
