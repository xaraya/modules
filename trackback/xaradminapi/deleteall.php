<?php
/**
 * Delete all trackback items for a module - hook for ('module','remove','API')
 *
 * @param int $args['objectid'] ID of the object (must be the module name here !!)
 * @param array $args['extrainfo'] extra information
 * @return bool true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function trackback_adminapi_deleteall($args)
{
    extract($args);

    // When called via hooks, we should get the real module name from objectid
    // here, because the current module is probably going to be 'modules' !!!
    if (!isset($objectid) || !is_string($objectid)) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $modId = xarModGetIDFromName($objectid);
    if (empty($modId)) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // TODO: re-evaluate this for hook calls !!

    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('DeleteTrackBack', 1, 'TrackBack', "All:$objectid:All")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();
    $trackBackTable = $tables['trackback'];

    $query = "DELETE FROM $trackBackTable
            WHERE moduleid = ?";
    $bindvars = array($modId);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // hmmm, I think we'll skip calling more hooks here... :-)
    //xarModCallHooks('item', 'delete', '', '');

    // Return the extra info
    if (!isset($extraInfo)) {
        $extraInfo = array();
    }
    return $extraInfo;
}
?>