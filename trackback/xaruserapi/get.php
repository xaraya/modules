<?php
/**
 * Get a trackback for a specific item
 *
 * @param string $args['modname'] name of the module this trackback is for
 * @param int $args['objectid'] ID of the item this trackback is for
 * @return int hits the corresponding hit count, or void if no hit exists
 */
function trackback_userapi_get($args)
{

    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($modname)) ||
        (!isset($objectid))) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }

    // Security check
    if (!xarSecurityCheck('ViewTrackBack', 1, 'TrackBack', "$modname:$objectid:All")) {
        return;
    }

    $modId = xarModGetIDFromName($modname);
    if (empty($modId)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }

    // Database information
    list($dbconn) = xarDBGetConn();
    $tables = xarDBGetTables();
    $trackBackTable = $tables['trackback'];

    // TODO: add item type

    // Get items
    $query = "SELECT url, blog_name, title, excerpt
            FROM $trackBackTable
            WHERE moduleid = '" . xarVarPrepForStore($modId) . "'
              AND itemid = '" . xarVarPrepForStore($objectid) . "'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $trackBack['url'] = $result->fields[0];
    $trackBack['blogname'] = $result->fields[1];
    $trackBack['title'] = $result->fields[2];
    $trackBack['exerpt'] = $result->fields[3];
    $result->close();

    return $trackBack;
}
?>