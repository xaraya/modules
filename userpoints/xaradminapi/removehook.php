<?php
/**
 * File: $Id$
 * 
 * userpoints hook functions
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage userpoints
 * @author Marie Altobelli
 */
/**
 * update entry for a module item - hook for ('item','update','API')
 * Optional $extrainfo['ping_update'] from arguments
 * Optional $extrainfo['ping_url'] from arguments for url from module other than articles
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function userpoints_adminapi_removehook($args)
{
    extract($args);
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        //$msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'extrainfo', 'admin', 'createhook', 'userpoints');
        //xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        $extrainfo = array();
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
      $pointsvalues = xarModAPIFunc('userpoints','user','getpoints',array('pmodule'=>$modname,'itemtype'=>$itemtype,'paction'=>'C'));
      if(!$pointsvalues) {return $extrainfo;}
	    $points = $pointsvalues['tpoints'];      
      $uptid = $pointsvalues['uptid'];
      
      $args['uptid'] = $uptid;
      $args['points'] = $points;
      $args['uid'] = $uid;
      $args['itemtype'] = $itemtype;
      $pointsadded = xarModAPIFunc('userpoints', 'admin', 'addpoints',$args);
 
	// Return the extra info
	return $extrainfo;
}
?>