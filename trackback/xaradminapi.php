<?php 
// File: $Id: s.xaradminapi.php 1.11 03/01/06 21:31:06-05:00 John.Cox@mcnabb. $
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Gregor J. Rothfuss
// Purpose of file:  trackback administration API
// ----------------------------------------------------------------------

/**
 * create a new trackback item - hook for ('item','create','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @param $args['modname'] name of the calling module (not used in hook calls)
 * @param $args['url'] url where the item is tracked (not used in hook calls)
 * @param $args['blogname'] name of the site where the item is tracked (not used in hook calls)
 * @param $args['title'] title of the trackback post (not used in hook calls)
 * @param $args['excerpt'] exccerpt from the trackback post (not used in hook calls)
 * @returns int
 * @return trackback item ID on success, void on failure
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
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'create', 'trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

// TODO: re-evaluate this for hook calls !!
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecAuthAction(0, 'Trackback::', "$modname::$objectid", ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $trackbacktable = $xartable['trackback'];

    // Get a new trackback ID
    $nextId = $dbconn->GenId($trackbacktable);
    // Create new trackback
    if (!isset($url)) {
         if (isset($extrainfo['url'])) {
             $url = $extrainfo['url'];
         } 
    }

    if (!isset($title)) {
         if (isset($extrainfo['title'])) {
             $title = $extrainfo['title'];
         } 
    }

    if (!isset($blogname)) {
         if (isset($extrainfo['blogname'])) {
             $blogname = $extrainfo['blogname'];
         } 
    }

    if (!isset($excerpt)) {
         if (isset($extrainfo['excerpt'])) {
             $excerpt = $extrainfo['excerpt'];
         } 
    }
    
    $query = "INSERT INTO $trackbacktable(xar_trackbackid,
                                       xar_moduleid,
                                       xar_itemid,
                                       xar_url,
                                       xar_blog_name,
                                       xar_title,
                                       xar_excerpt)
            VALUES ($nextId,
                    '" . xarVarPrepForStore($modid) . "',
                    '" . xarVarPrepForStore($objectid) . "',
                    '" . xarVarPrepForStore($url) . "',
                    '" . xarVarPrepForStore($blogname) . "',
                    '" . xarVarPrepForStore($title) . "',
                    '" . xarVarPrepForStore($excerpt) . "')";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $tbid = $dbconn->PO_Insert_ID($trackbacktable, 'xar_trackbackid');

    // hmmm, I think we'll skip calling more hooks here... :-)
    //xarModCallHooks('item', 'create', $tbid, 'trackbackid');

    // Return the extra info with the id of the newly created item
    // (not that this will be of any used when called via hooks, but
    // who knows where else this might be used)
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }
    $extrainfo['tbid'] = $tbid;
    return $extrainfo;
}

/**
 * delete a trackback item - hook for ('item','delete','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @param $args['modname'] name of the calling module (not used in hook calls)
 * @returns bool
 * @return true on success, false on failure
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
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'delete', 'trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

// TODO: re-evaluate this for hook calls !!
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecAuthAction(0, 'Trackback::', "$modname::$objectid", ACCESS_DELETE)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $trackbacktable = $xartable['trackback'];

    // Don't bother looking if the item exists here...

    $query = "DELETE FROM $trackbacktable
            WHERE xar_moduleid = '" . xarVarPrepForStore($modid) . "'
              AND xar_itemid = '" . xarVarPrepForStore($objectid) . "'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // hmmm, I think we'll skip calling more hooks here... :-)
    //xarModCallHooks('item', 'delete', $exid, '');

    // Return the extra info
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }
    return $extrainfo;
}

/**
 * delete all trackback items for a module - hook for ('module','remove','API')
 *
 * @param $args['objectid'] ID of the object (must be the module name here !!)
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
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

    $modid = xarModGetIDFromName($objectid);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module ID', 'admin', 'deleteall', 'Trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

// TODO: re-evaluate this for hook calls !!
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecAuthAction(0, 'Trackback::', "$objectid::", ACCESS_DELETE)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $trackbacktable = $xartable['trackback'];

    $query = "DELETE FROM $trackbacktable
            WHERE xar_moduleid = '" . xarVarPrepForStore($modid) . "'";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // hmmm, I think we'll skip calling more hooks here... :-)
    //xarModCallHooks('item', 'delete', '', '');

    // Return the extra info
    if (!isset($extrainfo)) {
        $extrainfo = array();
    }
    return $extrainfo;
}

?>
