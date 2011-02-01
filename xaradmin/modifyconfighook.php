<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/147.html
 * @author Johnny Robeson <johnny@xaraya.com>
 */
/**
 * Modify configuration for a module - hook for ('module','modifyconfig','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function comments_admin_modifyconfighook($args)
{
    include_once (sys::code().'modules/comments/xarincludes/defines.php');
    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module name', 'admin', 'modifyconfighook', 'comments');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $itemtype = 0;
    if (isset($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    }

    $data = array();
    
    // Have we set any vars for this itemtype yet? 
    // if not, get them from the default mod vars
    $data['depth'] = xarModVars::get($modname,'depth.' . $itemtype);
    if (!isset($data['depth'])) {
        $data['depth'] = xarModVars::get('comments','depth');
        $data['render'] = xarModVars::get('comments','render');
        $data['sortby'] = xarModVars::get('comments','sortby');
        $data['order'] = xarModVars::get('comments','order');
        $data['editstamp'] = xarModVars::get('comments','editstamp');
        $data['postanon'] = xarModVars::get('comments','AllowPostAsAnon');
        $data['wrap'] = xarModVars::get('comments','wrap');
        $data['showoptions'] = xarModVars::get('comments','showoptions');
        $data['edittimelimit'] = xarModVars::get('comments','edittimelimit');
    } else {
        $data['edittimelimit'] = xarModVars::get($modname,'edittimelimit.' . $itemtype);
        $data['depth'] = xarModVars::get($modname,'depth.' . $itemtype);
        $data['render'] = xarModVars::get($modname,'render.' . $itemtype);
        $data['sortby'] = xarModVars::get($modname,'sortby.' . $itemtype);
        $data['order'] = xarModVars::get($modname,'order.' . $itemtype);
        $data['editstamp'] = xarModVars::get($modname,'editstamp.' . $itemtype);
        $data['postanon'] = xarModVars::get($modname,'AllowPostAsAnon.' . $itemtype);
        $data['wrap'] = xarModVars::get($modname,'wrap.' . $itemtype);
        $data['showoptions'] = xarModVars::get($modname,'showoptions.' . $itemtype);
    }
    
    $data['modname'] = $modname;

    return xarTplModule('comments','admin','modifyconfighook', $data);
}
?>
