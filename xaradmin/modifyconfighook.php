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
    include_once 'modules/comments/xarincludes/defines.php';
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
    $data['depth'] = xarModGetVar($modname,'depth.' . $itemtype);
    if (!isset($data['depth'])) {
        $data['depth'] = xarModGetVar('comments','depth');
        $data['render'] = xarModGetVar('comments','render');
        $data['sortby'] = xarModGetVar('comments','sortby');
        $data['order'] = xarModGetVar('comments','order');
        $data['editstamp'] = xarModGetVar('comments','editstamp');
        $data['postanon'] = xarModGetVar('comments','AllowPostAsAnon');
        $data['wrap'] = xarModGetVar('comments','wrap');
        $data['showoptions'] = xarModGetVar('comments','showoptions');
        $data['edittimelimit'] = xarModGetVar('comments','edittimelimit');
    } else {
        $data['edittimelimit'] = xarModGetVar($modname,'edittimelimit.' . $itemtype);
        $data['depth'] = xarModGetVar($modname,'depth.' . $itemtype);
        $data['render'] = xarModGetVar($modname,'render.' . $itemtype);
        $data['sortby'] = xarModGetVar($modname,'sortby.' . $itemtype);
        $data['order'] = xarModGetVar($modname,'order.' . $itemtype);
        $data['editstamp'] = xarModGetVar($modname,'editstamp.' . $itemtype);
        $data['postanon'] = xarModGetVar($modname,'AllowPostAsAnon.' . $itemtype);
        $data['wrap'] = xarModGetVar($modname,'wrap.' . $itemtype);
        $data['showoptions'] = xarModGetVar($modname,'showoptions.' . $itemtype);
    }
    
    $data['modname'] = $modname;

    return xarTplModule('comments','admin','modifyconfighook', $data);
}
?>
