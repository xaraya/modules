<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
/**
 * start an update activity for a module item - hook for ('item','update','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return bool true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function workflow_adminapi_updatehook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('object id', 'admin', 'updatehook', 'workflow');
        throw new BadParameterException($vars, $msg);
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('extrainfo', 'admin', 'updatehook', 'workflow');
        throw new BadParameterException($vars, $msg);
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarMod::getName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarMod::getRegID($modname);
    if (empty($modid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('module name', 'admin', 'updatehook', 'workflow');
        throw new BadParameterException($vars, $msg);
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
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array('item id', 'admin', 'updatehook', 'workflow');
        throw new BadParameterException($vars, $msg);
    }

    // see if we need to start some workflow activity here
    if (!empty($itemtype)) {
        $activityId = xarModVars::get('workflow', "$modname.$itemtype.update");
    }
    if (empty($activityId)) {
        $activityId = xarModVars::get('workflow', "$modname.update");
    }
    if (empty($activityId)) {
        $activityId = xarModVars::get('workflow', 'default.update');
    }
    if (empty($activityId)) {
        return $extrainfo;
    }

    // Symfony Workflow transition
    if (!is_numeric($activityId) && strpos($activityId, '/') !== false) {
        [$workflowName, $transitionName] = explode('/', $activityId);
        if (!xarMod::apiFunc('workflow', 'user', 'run_transition', [
                'workflow' => $workflowName,
                'subjectId' => null,
                'transition' => $transitionName,
                // extra parameters from hook functions
                'hooktype' => 'ItemUpdate',
                'module' => $modname,
                'itemtype' => $itemtype,
                'itemid' => $itemid,
                'module_id' => $modid,
                'extrainfo' => $extrainfo,
            ])) {
            return $extrainfo;
        }
        return $extrainfo;
    }

    // Galaxia Workflow activity
    if (!xarMod::apiFunc(
        'workflow',
        'user',
        'run_activity',
        ['activityId' => $activityId,
                             'auto' => 1,
                             // standard arguments for use in activity code
                             'module' => $modname,
                             'itemtype' => $itemtype,
                             'itemid' => $itemid, ]
    )) {
        return $extrainfo;
    }

    // Return the extra info
    return $extrainfo;
}
