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
 * find instances with a certain status, activityId and/or max. started date
 *  - now accepts an activityName and processName if you don't have the ids
 *
 * @author mikespub
 * @access public
 */
function workflow_userapi_findinstances($args)
{
    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    include(GALAXIA_LIBRARY.'/processmonitor.php');

    extract($args);
    if (!isset($status)) {
        $status = 'active';
    }
    if (!isset($actstatus)) {
        $actstatus = 'running';
    }

    $where = '';
    $wheres = array();
    // TODO: reformulate this with bindvars
    if (!empty($status)) {
        $wheres[] = "gi.status='" . $status . "'";
    }
    if (!empty($actstatus)) {
        $wheres[] = "gia.status='" . $actstatus . "'";
    }
    if (!empty($activityId)) {
        $wheres[] = "gia.activityId=" . $activityId;
    }
    if (!empty($max_started)) {
        $wheres[] = "gi.started <= " . $max_started;
    }
        if (!empty($activityName) && !empty($processName)) {
                $wheres[] = "ga.name = '" . $activityName . "' AND gp.name = '".$processName."'";
        }

    $where = implode(' and ', $wheres);

    $items = $processMonitor->monitor_list_instances(0, -1, 'instanceId_asc', '', $where, array());

    if (isset($items) && isset($items['data'])) {
        return $items['data'];
    } else {
        return array();
    }
}

?>
