<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Categories module development team
 */
include_once('modules/comments/xarincludes/defines.php');
/**
 * update configuration for a module - hook for ('module','updateconfig','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return bool
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function comments_adminapi_updateconfighook($args)
{
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'admin', 'updateconfighook', 'categories');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // @todo check in $extrainfo (is it worth it?)
    if (!xarVarFetch('showoptions', 'checkbox', $showoptions, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('postanon', 'checkbox', $postanon, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('depth', 'int:1:', $depth, _COM_MAX_DEPTH, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('render', 'str:1:', $render, _COM_VIEW_THREADED, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('sortby', 'str:1:', $sortby, _COM_SORTBY_THREAD, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('order', 'str:1:', $order, _COM_SORT_ASC, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('editstamp','checkbox',$editstamp,0,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('wrap','checkbox', $wrap, false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('edittimelimit', 'str:1:', $edittimelimit, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('authorize', 'checkbox', $authorize, false, XARVAR_NOT_REQUIRED)) return;

    $itemtype = 0;
    if (isset($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    }

    xarModSetVar($modname, 'edittimelimit.' . $itemtype, $edittimelimit);
    xarModSetVar($modname, 'AllowPostAsAnon.' . $itemtype, $postanon);
    xarModSetVar($modname, 'AuthorizeComments.' . $itemtype, $authorize);
    xarModSetVar($modname, 'depth.' . $itemtype, $depth);
    xarModSetVar($modname, 'render.' . $itemtype, $render);
    xarModSetVar($modname, 'sortby.' . $itemtype, $sortby);
    xarModSetVar($modname, 'order.' . $itemtype, $order);
    xarModSetVar($modname, 'editstamp.' . $itemtype, $editstamp);
    xarModSetVar($modname, 'wrap.' . $itemtype, $wrap);
    xarModSetVar($modname, 'showoptions.' . $itemtype, $showoptions);

    return $extrainfo;
}
?>
