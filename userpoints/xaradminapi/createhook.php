<?php

/**
 * create linkage for an item - hook for ('item','create','API')
 * Needs $extrainfo['cids'] from arguments, or 'cids' from input
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function userpoints_adminapi_createhook($args)
{
    extract($args);

    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object ID', 'admin', 'createhook', 'userpoints');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'admin', 'createhook', 'userpoints');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }
    if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }
    if (isset($extrainfo['authorid']) && is_numeric($extrainfo['authorid'])) {
        $authorid = $extrainfo['authorid'];
    } else {
        $authorid = xarModGetUserVar('uid');
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

// Now get the points to award

    if($itemtype == 0) {

    $points = xarModGetVar('userpoints', "createpoints.$modname");

	}
	else{

    $points = xarModGetVar('userpoints', "createpoints.$modname.$itemtype");

	}


    if (!xarModAPIFunc('userpoints', 'admin', 'addpoints',
                      array('moduleid'  => $modid,
                            'itemtype'  => $itemtype,
                            'objectid' => $objectid,
                            'status' => $status,
                            'authorid' => $authorid,
                            'pubdate' => $pubdate,
                            'points' => $points))) {
        return false;
    }

    // Return the extra info
    return $extrainfo;
}

?>
