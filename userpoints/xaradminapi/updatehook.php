<?php

/**
 * update points for an item - hook for ('item','update','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function userpoints_adminapi_updatehook($args)
{
    extract($args);

    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'object ID', 'admin', 'updatehook', 'userpoints');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $extrainfo;
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'module name', 'admin', 'updatehook', 'userpoints');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $extrainfo;
    }
    if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }
    if (isset($extrainfo['authorid']) && is_numeric($extrainfo['authorid'])) {
        $authorid = $extrainfo['authorid'];
    } else {
        $authorid = xarUserGetVar('uid');
    }

// Get Status Of Item
// 0 = Submitted
// 1 = Rejected
// 2 = Approved
// 3 = Frontpage

    if (isset($extrainfo['status']) && is_numeric($extrainfo['status'])) {
        $status = $extrainfo['status'];
    } else {
        $status = 2;
    }

    if (isset($extrainfo['pubdate']) && is_numeric($extrainfo['pubdate'])) {
        $pubdate = $extrainfo['pubdate'];
    } else {
        $pubdate = 1;
    }

    if (!xarUserIsLoggedIn()) {
        return $extrainfo;
    }
    $uid = xarUserGetVar('uid');

    $points = xarModAPIFunc('userpoints', 'user', 'getpoints',
                            array('pmodule'=>$modname,
                                  'itemtype'=>$itemtype,
                                  'paction'=>'update'));
    if (empty($points)) {
        return $extrainfo;
    }

// CHECKME: do we want to add points to the author or the updater (or both) ?
    $pointsadded = xarModAPIFunc('userpoints', 'admin', 'addpoints',
                                 array('uid' => $uid,
                                       'points' => $points,
                                    // currently unused
                                       'moduleid'  => $modid,
                                       'itemtype'  => $itemtype,
                                       'objectid' => $objectid,
                                       'status' => $status,
                                       'authorid' => $authorid,
                                       'pubdate' => $pubdate));

    // Return the extra info
    return $extrainfo;
}
?>
