<?php
/**
 * New hook for Formantibot
 *
 * @package Xaraya modules
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Formantibot
 * @copyright (C) 2008,2009 2skies.com
 * @link http://xarigami.com/project/formantibot
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * add information on an entry for a module item - hook for ('item','new','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return string hook output in HTML
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function formantibot_admin_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1)', 'extrainfo');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid)) {
        $msg = xarML('Invalid #(1)', 'object ID');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $msg;
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
        $msg = xarML('Invalid #(1)', 'module name');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $msg;
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $itemid = 0;
    }

    //backward compatibility
    if (xarModIsAvailable('formantibot')) {
        $data['AntiBot_Available'] = TRUE;
    } 
    
    $usecaptcha = 0;// default is not to use
    
    //check whether to use it at all
    $captchatype= xarModGetvar('formantibot','captchatype');

    if (isset($extrainfo['antibotinvalid'])) {
        $antibotinvalid = $extrainfo['antibotinvalid'];
    } else {
        if(!xarVarFetch('antibotinvalid','int:0:1', $antibotinvalid, NULL,XARVAR_DONT_SET)) {return;}
    }

    if (isset($extrainfo['botreset'])) {
        $botreset = $extrainfo['botreset'];
    } else {
        if(!xarVarFetch('botreset', 'bool',$botreset, false, XARVAR_DONT_SET)) {return;}
    }
    
    //check whether to use it for registered users as well as anon
    $registered = xarModGetvar('formantibot','registered');
    
    //
    if ((!xarUserIsLoggedIn() || (($registered == 1) && xarUserIsLoggedIn())) && $captchatype != 0) {
        $usecaptcha = 1;
    }

    if ($usecaptcha != 1) {
        return ;
    } else {
    
            return xarTplModule('formantibot','admin','newhook',
                array(
                    'antibotinvalid' => $antibotinvalid,
                    'botreset'       => $botreset,
                    'itemid'         => $itemid,
                    'itemtype'       => $itemtype,
                    'modid'          => $modid,
                    'usecaptcha'     => $usecaptcha,
                    'captchatype'    => $captchatype
                )
            );
    }
}

?>