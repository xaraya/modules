<?php
/**
 * Display hook
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian
 * @link  link to information for the subpackage
 * @author Julian development Team
 */
/**
 * Display hook
 *
 * show date/time/schedule for an item - hook for ('item','display','GUI')
 *
 * @author  Julian Development Team
 * @author  JornB, MichelV. <michelv@xarayahosting.nl>
 * @access  public
 * @param   id $objectid
 * @param   array $extrainfo
 * @return  array
 * @todo    nothing
 */
function julian_user_displayhook($args)
{
    extract($args);

     // extra info as supplied by the hooking module.
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

     // Get the id of the object to display (the id as used in the hooking module).
    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object ID', 'user', 'modifyhook', 'julian');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // When called via hooks, the module name may be empty, so we get it from the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

     // Convert module name into module id.
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'user', 'displayhook', 'julian');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Get item type.
    if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }
    $item = xarModAPIFunc('julian', 'user', 'gethooked', array('modid' => $modid, 'itemtype' => $itemtype, 'objectid' => $objectid));

    return xarTplModule('julian','user','displayhook',$item);
}

?>
