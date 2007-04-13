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
 * the run activity user function
 *
 * @author mikespub
 * @access public
 */
function workflow_user_run_activity()
{
    xarLogMessage("Running activity");
    // Security Check
    // CHECKME: what if an activity is Auto? (probably nothing different)
    if (!xarSecurityCheck('ReadWorkflow')) return;

// Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-run_activity.php

include (GALAXIA_LIBRARY.'/API.php');

// TODO: evaluate why this is here
//include_once ("lib/webmail/htmlMimeMail.php");

global $__activity_completed;
global $__comments;
$__activity_completed = false;

// Determine the activity using the activityId request
// parameter and get the activity information
// load then the compiled version of the activity
if (!isset($_REQUEST['activityId'])) {
  $tplData['msg'] =  xarML("No workflow activity indicated");
  return xarTplModule('workflow', 'user', 'error', $tplData);
}

$activity = $baseActivity->getActivity($_REQUEST['activityId']);
if (empty($activity)) {
  $tplData['msg'] = xarML("Invalid workflow activity specified");
  return xarTplModule('workflow', 'user', 'error', $tplData);
}
$process->getProcess($activity->getProcessId());

// Get user roles

// Get activity roles
$act_roles = $activity->getRoles();
$user_roles = $activity->getUserRoles($user);

// Only check roles if this is an interactive
// activity
if ($activity->isInteractive() == 'y') {
  if (!count(array_intersect($act_roles, $user_roles))) {
    $tplData['msg'] =  xarML("You can't execute this activity");

    return xarTplModule('workflow', 'user', 'error', $tplData);
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
// cls - this needs to be included each time, otherwise you can't run more than 1 activity per request
include ($source);

// Process comments
if (isset($_REQUEST['__removecomment'])) {
  $__comment = $instance->get_instance_comment($_REQUEST['__removecomment']);

  if ($__comment['user'] == $user or $tiki_p_admin_workflow == 'y') {
    $instance->remove_instance_comment($_REQUEST['__removecomment']);
  }
}

$tplData['__comments'] =&  $__comments;

if (!isset($_REQUEST['__cid']))
  $_REQUEST['__cid'] = 0;

if (isset($_REQUEST['__post'])) {
  $instance->replace_instance_comment($_REQUEST['__cid'], $activity->getActivityId(), $activity->getName(),
    $user, $_REQUEST['__title'], $_REQUEST['__comment']);
}

$__comments = $instance->get_instance_comments();

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

// URL to return to if some action is taken - use htmlspecialchars() here
if (!empty($_REQUEST['return_url'])) {
    $tplData['return_url'] = htmlspecialchars($_REQUEST['return_url']);
} else {
    $tplData['return_url'] = '';
}

if (!isset($_REQUEST['auto']) && $activity->isInteractive() && $__activity_completed) {
    if (!empty($_REQUEST['return_url'])) {
        xarResponseRedirect($_REQUEST['return_url']);
    } elseif (empty($instance->instanceId)) {
        xarResponseRedirect(xarModURL('workflow', 'user', 'activities'));
    } else {
        xarResponseRedirect(xarModURL('workflow', 'user', 'instances'));
    }
    return true;
} elseif (!isset($_REQUEST['auto']) && $activity->isInteractive() && $activity->getType() == 'standalone' && !empty($_REQUEST['return_url'])) {
    xarResponseRedirect($_REQUEST['return_url']);
    return true;
} else {
  if (!isset($_REQUEST['auto']) && $activity->isInteractive()) {
    //$section = 'workflow';
    //include_once ('tiki-section_options.php');
    $template = $activity->getNormalizedName(). '.tpl';
    $tplData['mid'] =  $process->getNormalizedName(). '/' . $template;
  // not very clean way, but it works :)
                $output = xarTplFile(GALAXIA_PROCESSES . '/' . $process->getNormalizedName(). '/code/templates/' . $template, $tplData);
                $tplData['mid'] = $output;
    $template = 'running';

                // call display hooks if we have an instance
                if (!empty($instance->instanceId)) {
                    // get object properties for this instance - that's a bit much :)
                    //$item = get_object_vars($instance);
                    $props = array('id','instanceId','properties','owner','status','started','ended','nextActivity','nextUser','workitems');
                    $item = array();
                    foreach ($props as $prop) {
                        $item[$prop] = $instance->$prop;
                    }
                    $item['module'] = 'workflow';
                    $item['itemtype'] = $activity->getProcessId();
                    $item['itemid'] = $instance->getInstanceId();
                    $item['returnurl'] = xarModURL('workflow','user','run_activity',
                                                   array('activityId' => $activity->getActivityId(),
                                                         'iid' => $instance->getInstanceId()));
                    $tplData['hooks'] = xarModCallHooks('item','display',$instance->instanceId,$item);
                }
  } else {
    $template = 'completed';
  }
}

    return xarTplModule('workflow','user','activity',$tplData,$template);
}

?>
