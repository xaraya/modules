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
 * show the result of a workflow activity (called via <xar:workflow-activity tag)
 *
 * @author mikespub
 * @access public
 */
function workflow_userapi_showactivity($args)
{
    // Security Check
    if (!xarSecurityCheck('ReadWorkflow',0)) {
        return '';
    }

    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    $tplData = array();

    include (GALAXIA_LIBRARY.'/api.php');

    if (empty($args['activityId'])) {
        return xarML("No activity found");
    }

    $activity = WorkFlowActivity::get($args['activityId']);
    $process = new Process($activity->getProcessId());

    if (empty($user)) {
        $user = xarUserGetVar('id');
    }
    if (!empty($args['instanceId'])) {
        $instance->getInstance($args['instanceId']);
        $instance->setActivityUser($activity->getActivityId(), $user);
    }

    // Get user roles

    // Get activity roles
    $act_roles = $activity->getRoles();
    $user_roles = $activity->getUserRoles($user);

    // Only check roles if this is an interactive
    // activity
    if ($activity->isInteractive()) {
        // TODO: revisit this when roles/users is clearer
        $canrun = false;
        foreach ($act_roles as $candidate)
            if (in_array($candidate["roleId"], $user_roles)) $canrun = true;
        if (!$canrun) {
            return xarML("You can't execute this activity");
        }
    }

    $act_role_names = $activity->getActivityRoleNames($user);

    // FIXME: what's this for ?
    foreach ($act_role_names as $role) {
        $name = 'tiki-role-' . $role['name'];

        if (in_array($role['roleId'], $user_roles)) {
            $tplData[$name] = 'y';
            $$name = 'y';
        } else {
            $tplData[$name] = 'n';
            $$name = 'n';
        }
    }

    $source = GALAXIA_PROCESSES.'/' . $process->getNormalizedName(). '/compiled/' . $activity->getNormalizedName(). '.php';
    $shared = GALAXIA_PROCESSES.'/' . $process->getNormalizedName(). '/code/shared.php';

    // Existing variables here:
    // $process, $activity, $instance (if not standalone)

    // Include the shared code
    include_once ($shared);

    // Now do whatever you have to do in the activity
    include_once ($source);

    // This goes to the end part of all activities
    // If this activity is interactive then we have to display the template

    $tplData['procname'] =  $process->getName();
    $tplData['procversion'] =  $process->getVersion();
    $tplData['actname'] =  $activity->getName();
    $tplData['actid'] = $activity->getActivityId();

    // Put the current activity id in a template variable
    $tplData['activityId'] = $activity->getActivityId();

    // Put the current instance id in a template variable
    $tplData['iid'] = $instance->getInstanceId();

    // URL to return to if some action is taken
    $tplData['return_url'] = xarServerGetCurrentURL();

    if ($activity->isInteractive()) {
        $template = $activity->getNormalizedName(). '.tpl';
        // not very clean way, but it works :)
        $output = xarTpl__executeFromFile(GALAXIA_PROCESSES . '/' . $process->getNormalizedName(). '/code/templates/' . $template, $tplData);
        return $output;
    } else {
        $instance->getInstance($instance->instanceId);
        $instance->complete($activity->getActivityId());
        return '';
    }
}
?>
