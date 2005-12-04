<?php
/**
 * Get points
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints Module
 * @link http://xaraya.com/index.php/release/782.html
 * @author Userpoints Module Development Team
 */
/**
 * add user points for displaying an item
 * @param $args['objectid'] ID of the item this point is for
 * @param $args['extrainfo'] module, itemtype and return_url of the item
 * @returns string
 * @return empty string for display hook here
 */
function userpoints_user_display($args)
{
    extract($args);

    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'object ID', 'user', 'display', 'userpoints');
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
                     'module name', 'user', 'display', 'userpoints');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return '';
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
        return '';
    }
    $uid = xarUserGetVar('uid');

    $points = xarModAPIFunc('userpoints', 'user', 'getpoints',
                            array('pmodule'=>$modname,
                                  'itemtype'=>$itemtype,
                                  'paction'=>'display'));
    if (empty($points)) {
        return '';
    }

// CHECKME: do we want to add points to the author or the viewer (or both) ?
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

/*
    $pointsvalues = xarModAPIFunc('userpoints','user','getpoints',
                                  array('pmodule'=>$modname,'itemtype'=>$itemtype,'paction'=>'D'));
    if (!$pointsvalues) {
        return '';
    }

    $points = $pointsvalues['tpoints'];

    $uptid = $pointsvalues['uptid'];

    $args['uptid'] = $uptid;
    $args['points'] = $points;
    $args['uid'] = $uid;

    $pointsadded = xarModAPIFunc('userpoints', 'admin', 'addpoints',$args);
*/

    // Nothing to see here
    return '';
}

?>
