<?php

/**
 * display rating for a specific item, and request rating
 * @param $args['objectid'] ID of the item this rating is for
 * @param $args['extrainfo'] URL to return to if user chooses to rate
 * @param $args['style'] style to display this rating in (optional)
 * @param $args['itemtype'] item type
 * @returns output
 * @return output with rating information
 */
function userpoints_user_display($args)
{
    extract($args);
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'extrainfo', 'admin', 'displayhook', 'userpoints');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
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

      // Need to get the correct itemtype so we send the correct URL to update
      $uid = xarUserGetVar('uid');
      if(!xarUserIsLoggedIn()) {return $extrainfo;}
      $pointsvalues = xarModAPIFunc('userpoints','user','getpoints',array('pmodule'=>$modname,'itemtype'=>$itemtype,'paction'=>'D'));
      if(!$pointsvalues) {return $extrainfo;}
	    $points = $pointsvalues['tpoints']; 
      
      $uptid = $pointsvalues['uptid'];
      
      $args['uptid'] = $uptid;
      $args['points'] = $points;
      $args['uid'] = $uid;
      
      $pointsadded = xarModAPIFunc('userpoints', 'admin', 'addpoints',$args);
    
	// Return the extra info
	return $extrainfo;
}

?>