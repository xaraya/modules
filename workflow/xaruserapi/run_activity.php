<?php

/**
 * the run activity user API function
 * 
 * @author mikespub
 * @access public 
 */
function workflow_userapi_run_activity($args)
{
    // Security Check
    if (!xarSecurityCheck('ReadWorkflow')) return;

// Common setup for Galaxia environment (possibly include more than once here !)
    include('modules/workflow/tiki-setup.php');
    $tplData = array();

// Adapted from tiki-g-run_activity.php

include (GALAXIA_DIR.'/API.php');

// TODO: evaluate why this is here
//include_once ("lib/webmail/htmlMimeMail.php");

global $__activity_completed;
global $__comments;

$__activity_completed = false;

if ($feature_workflow != 'y') {
	$msg = xarML("This feature is disabled");
	xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
			new SystemException($msg));
	return;
}

if (!isset($args['auto'])) {
	if ($tiki_p_use_workflow != 'y') {
		$msg = xarML("Permission denied");
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
				new SystemException($msg));
		return;
	}
}

// Determine the activity using the activityId request
// parameter and get the activity information
// load then the compiled version of the activity
if (!isset($args['activityId'])) {
	$msg = xarML("No activity indicated");
	xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
			new SystemException($msg));
	return;
}

$activity = $baseActivity->getActivity($args['activityId']);
$process->getProcess($activity->getProcessId());

// Get user roles

// Get activity roles
$act_roles = $activity->getRoles();
$user_roles = $activity->getUserRoles($user);

$act_role_names = $activity->getActivityRoleNames($user);

// FIXME: what's this for ?
foreach ($act_role_names as $role) {
	$name = 'tiki-role-' . $role['name'];

	if (in_array($role['roleId'], $user_roles)) {
		$smarty->assign("$name", 'y');

		$$name = 'y';
	} else {
		$smarty->assign("$name", 'n');

		$$name = 'n';
	}
}

$source = GALAXIA_DIR.'/processes/' . $process->getNormalizedName(). '/compiled/' . $activity->getNormalizedName(). '.php';
$shared = GALAXIA_DIR.'/processes/' . $process->getNormalizedName(). '/code/shared.php';

// Existing variables here:
// $process, $activity, $instance (if not standalone)

// Include the shared code
include_once ($shared);

// Now do whatever you have to do in the activity
include_once ($source);

// Process comments
if (isset($args['__removecomment'])) {
	$__comment = $instance->get_instance_comment($args['__removecomment']);

	if ($__comment['user'] == $user or $tiki_p_admin_workflow == 'y') {
		$instance->remove_instance_comment($args['__removecomment']);
	}
}

$tplData['__comments'] =&  $__comments;

if (!isset($args['__cid']))
	$args['__cid'] = 0;

if (isset($args['__post'])) {
	$instance->replace_instance_comment($args['__cid'], $activity->getActivityId(), $activity->getName(),
		$user, $args['__title'], $args['__comment']);
}

$__comments = $instance->get_instance_comments();

    return true;
}

?>
