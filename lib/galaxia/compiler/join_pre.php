<?php
//Code to be executed before a join activity
// If we didn't retrieve the instance before
if(empty($instance->instanceId)) {
  // This activity needs an instance to be passed to
  // be started, so get the instance into $instance.
  if(isset($_REQUEST['iid'])) {
    $instance->getInstance($_REQUEST['iid']);
  } else {
    // defined in lib/galaxia/config.php
    throw new Exception("No instance indicated");
  }
}
// Set the current user for this activity
if(isset($user) && !empty($instance->instanceId) && !empty($activity->activityId)) {
  $instance->setActivityUser($activity->activityId,$user);
}

?>
