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
  Addition to the workflow module when there is a need
  to retrieve the activityid.

  @author Mike Dunn submitted by Court Shrock
  @access public
  @param $activityName the name of the activity you need an id for (required)
  @return int workflow activityid
*/
function workflow_userapi_getActivityId($args)
{
  extract($args);

  if(!isset($activityName)) return;

  include('modules/workflow/tiki-setup.php');
  include(GALAXIA_LIBRARY.'/processmonitor.php');


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