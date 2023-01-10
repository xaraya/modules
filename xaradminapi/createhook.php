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

    // everything is already validated in HookSubject, except possible empty objectid/itemid for create/display
    $modname = $extrainfo['module'];
    $itemtype = $extrainfo['itemtype'];
    $itemid = $extrainfo['itemid'];
    $modid = $extrainfo['module_id'];

    // see if we need to start some workflow activity here
    if (!empty($itemtype)) {
        $activityId = xarModVars::get('workflow', "$modname.$itemtype.create");
    }
    if (empty($activityId)) {
        $activityId = xarModVars::get('workflow', "$modname.create");
    }
    if (empty($activityId)) {
        $activityId = xarModVars::get('workflow', 'default.create');
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
                'hooktype' => 'ItemCreate',
                'module' => $modname,
                'itemtype' => $itemtype,
                'itemid' => $objectid,
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
                             'itemid' => $objectid, ]
    )) {
        return $extrainfo;
    }

    return $extrainfo;
}
