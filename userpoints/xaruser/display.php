<?php

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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'extrainfo', 'user', 'display', 'userpoints');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return '';
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    if (!empty($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
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

    $pointsadded = xarModAPIFunc('userpoints', 'admin', 'addpoints',
                                 array('uid' => $uid,
                                       'points' => $points));

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
