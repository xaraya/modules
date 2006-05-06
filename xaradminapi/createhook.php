<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * start a create activity for a module item - hook for ('item','create','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return array extrainfo array
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function workflow_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'createhook', 'workflow');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $extrainfo = array();
    }

    // When called via hooks, modname wil be empty, but we get it from the
    // extrainfo or the current module
    if (empty($modname)) {
        if (!empty($extrainfo['module'])) {
            $modname = $extrainfo['module'];
        } else {
            $modname = xarModGetName();
        }
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'createhook', 'workflow');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (!isset($itemtype) || !is_numeric($itemtype)) {
         if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
             $itemtype = $extrainfo['itemtype'];
         } else {
             $itemtype = 0;
         }
    }

    // see if we need to start some workflow activity here
    if (!empty($itemtype)) {
        $activityId = xarModGetVar('workflow',"$modname.$itemtype.create");
    }
    if (empty($activityId)) {
        $activityId = xarModGetVar('workflow',"$modname.create");
    }
    if (empty($activityId)) {
        $activityId = xarModGetVar('workflow','default.create');
    }
    if (empty($activityId)) {
        return $extrainfo;
    }

    if (!xarModAPIFunc('workflow','user','run_activity',
                       array('activityId' => $activityId,
                             'auto' => 1,
                             // standard arguments for use in activity code
                             'module' => $modname,
                             'itemtype' => $itemtype,
                             'itemid' => $objectid))) {
        return $extrainfo;
    }

    return $extrainfo;
}

?>
