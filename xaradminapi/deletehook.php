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
 * start a delete activity for a module item - hook for ('item','delete','API')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return bool true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function workflow_adminapi_deletehook($args)
{
    extract($args);

    // everything is already validated in HookSubject, except possible empty objectid/itemid for create/display
    $modname = $extrainfo['module'];
    $itemtype = $extrainfo['itemtype'];
    $itemid = $extrainfo['itemid'];
    $modid = $extrainfo['module_id'];
    if (empty($itemid)) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = ['item id', 'admin', 'deletehook', 'workflow'];
        throw new BadParameterException($vars, $msg);
    }

    // see if we need to start some workflow activity here
    if (!empty($itemtype)) {
        $activityId = xarModVars::get('workflow', "$modname.$itemtype.delete");
    }
    if (empty($activityId)) {
        $activityId = xarModVars::get('workflow', "$modname.delete");
    }
    if (empty($activityId)) {
        $activityId = xarModVars::get('workflow', 'default.delete');
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
                'hooktype' => 'ItemDelete',
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
