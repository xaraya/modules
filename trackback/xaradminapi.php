<?php
/**
 * File: $Id$
 *
 * Trackback Admin API
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage trackback
 * @author Gregor J. Rothfuss
 */

/**
 * Create a new trackback item - hook for ('item','create','API')
 *
 * @param int    $args['objectid'] ID of the object
 * @param array  $args['extrainfo'] extra information
 * @param string $args['modname'] name of the calling module (not used in hook calls)
 * @param string $args['url'] url where the item is tracked (not used in hook calls)
 * @param string $args['blogname'] name of the site where the item is tracked (not used in hook calls)
 * @param string $args['title'] title of the trackback post (not used in hook calls)
 * @param string $args['excerpt'] exccerpt from the trackback post (not used in hook calls)
 * @return int trackback item ID on success, void on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function trackback_adminapi_create($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'create', 'trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // When called via hooks, modname will be empty, but we get it from the
    // current module
    if (empty($modname)) {
        $modname = xarModGetName();
    }
    $modId = xarModGetIDFromName($modname);
    if (empty($modId)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'create', 'trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // TODO: re-evaluate this for hook calls !!

    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AddTrackBack', 1, 'TrackBack', "$modname:$objectid:All")) {
        return;
    }

    // Create new trackback
    if (!isset($url)) {
         if (isset($extraInfo['url'])) {
             $url = $extraInfo['url'];
         }
    }

    if (!isset($title)) {
         if (isset($extraInfo['title'])) {
             $title = $extraInfo['title'];
         }
    }

    if (!isset($blogname)) {
         if (isset($extraInfo['blogname'])) {
             $blogname = $extraInfo['blogname'];
         }
    }

    if (!isset($excerpt)) {
         if (isset($extraInfo['excerpt'])) {
             $excerpt = $extraInfo['excerpt'];
         }
    }

    list($dbconn) = xarDBGetConn();
    $tables = xarDBGetTables();
    $trackBackTable = $tables['trackback'];

    // Get a new trackback ID
    $nextId = $dbconn->GenId($trackbackTable);

    $query = "INSERT INTO $trackBackTable(trackbackid,
                                          moduleid,
                                          itemid,
                                          url,
                                          blog_name,
                                          title,
                                          excerpt)
            VALUES ($nextId,
                    '" . xarVarPrepForStore($modId) . "',
                    '" . xarVarPrepForStore($objectid) . "',
                    '" . xarVarPrepForStore($url) . "',
                    '" . xarVarPrepForStore($blogname) . "',
                    '" . xarVarPrepForStore($title) . "',
                    '" . xarVarPrepForStore($excerpt) . "')";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $tbId = $dbconn->PO_Insert_ID($trackBackTable, 'trackbackid');

    // hmmm, I think we'll skip calling more hooks here... :-)
    //xarModCallHooks('item', 'create', $tbid, 'trackbackid');

    // Return the extra info with the id of the newly created item
    // (not that this will be of any used when called via hooks, but
    // who knows where else this might be used)
    if (!isset($extraInfo)) {
        $extraInfo = array();
    }
    $extraInfo['tbid'] = $tbId;
    return $extraInfo;
}

/**
 * Delete a trackback item - hook for ('item','delete','API')
 *
 * @param int $args['objectid'] ID of the object
 * @param array $args['extrainfo'] extra information
 * @param string $args['modname'] name of the calling module (not used in hook calls)
 * @return bool true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function trackback_adminapi_delete($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'delete', 'trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // When called via hooks, modname will be empty, but we get it from the
    // current module
    if (empty($modname)) {
        $modname = xarModGetName();
    }
    $modId = xarModGetIDFromName($modname);
    if (empty($modId)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'delete', 'trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // TODO: re-evaluate this for hook calls !!

    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('DeleteTrackBack', 1, 'TrackBack', "$modname:$objectid:All")) {
        return;
    }

    list($dbconn) = xarDBGetConn();
    $tables = xarDBGetTables();
    $trackBackTable = $tables['trackback'];

    // Don't bother looking if the item exists here...

    $query = "DELETE FROM $trackBackTable
            WHERE moduleid = '" . xarVarPrepForStore($modId) . "'
              AND itemid = '" . xarVarPrepForStore($objectid) . "'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // hmmm, I think we'll skip calling more hooks here... :-)
    //xarModCallHooks('item', 'delete', $exid, '');

    // Return the extra info
    if (!isset($extraInfo)) {
        $extraInfo = array();
    }
    return $extraInfo;
}

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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID (= module name)', 'admin', 'deleteall', 'Trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    $modId = xarModGetIDFromName($objectid);
    if (empty($modId)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module ID', 'admin', 'deleteall', 'Trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // TODO: re-evaluate this for hook calls !!

    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('DeleteTrackBack', 1, 'TrackBack', "All:$objectid:All")) {
        return;
    }

    list($dbconn) = xarDBGetConn();
    $tables = xarDBGetTables();
    $trackBackTable = $tables['trackback'];

    $query = "DELETE FROM $trackBackTable
            WHERE moduleid = '" . xarVarPrepForStore($modId) . "'";
    $result =& $dbconn->Execute($query);
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