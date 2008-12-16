<?php
/**
 * Create hook for formantibot module
 *
 * @package Xaraya modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Formantibot
 * @copyright (C) 2008 2skies.com
 * @link http://xarigami.com/project/formantibot
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * create an entry for a module item - hook for ('item','create','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return array extrainfo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function formantibot_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object id', 'admin', 'updatehook', 'formantibot');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'extrainfo', 'admin', 'updatehook', 'formantibot');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module name', 'admin', 'updatehook', 'formantibot');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    if (!empty($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'item id', 'admin', 'createhook', 'formantibot');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    $botreset = FALSE; 
    $antibotinvalid = FALSE;
    $badcaptcha = 0; //backward compat
    
    if (!isset($antibotcode)) {
        $antibotcode = $extrainfo['antibotcode'];
    } elseif  (!isset($extrainfo['antibotcode'])) {
        if(!xarVarFetch('antibotcode',  'str:6:10', $antibotcode, '',XARVAR_DONT_SET)) {return;}
    }    
    
    $correctcode =xarModAPIFunc('formantibot', 'user', 'validate', array('userInput' => $antibotcode));

    if ($correctcode != TRUE) {
        $antibotinvalid = TRUE;
        $botreset = TRUE;
        $badcaptcha = 1;
    }
    $extrainfo = array('antibotinvalid'=>$antibotinvalid,
                       'botreset'   =>$botreset,
                       'badcaptcha' => $badcaptcha);
                       
 
      return $extrainfo;
}
?>
