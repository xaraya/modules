<?php

/**
  Addition to the workflow module when there is a need
  to retrieve the activityid.

  @author Mike Dunn submitted by Court Shrock
  @access public
  @param $activityName the name of the activity you need an id for (required)
  @returns workflow activityid
*/
function workflow_userapi_getActivityId($args) {
  extract($args);

  if(!isset($activityName)) return;

  include('modules/workflow/tiki-setup.php');
  include(GALAXIA_LIBRARY.'/ProcessMonitor.php');


  $items = $processMonitor->monitor_list_activities(0, -1, 'activityId_asc', $activityName, '', array());
  unset($processMonitor);
  $activityId = '';

  if(is_array($items)) {
    $keyarray = array_keys($items['data']);
    $key = $keyarray[0];
    $activityId = $items['data'][$key]['activityId'];
  }// if

  return $activityId;
}
?>