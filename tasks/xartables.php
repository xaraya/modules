<?php

/**
 * File: $Id$
 *
 * Table registration for tasks module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * 
 * @subpackage tasks
 * @author Chad Kraeft
*/

/**
 * tasks table regitration
 */
function tasks_xartables()
{
    $xartable = array();

    $taskstable = xarDBGetSystemTablePrefix() . '_tasks';
    $xartable['tasks'] = $taskstable;
    
    $xartable['tasks_columns'] = array('id'                   => $taskstable . '.xar_id',
                                       'parentid'           => $taskstable . '.xar_parentid',
                                       'modname'           => $taskstable . '.xar_modname',
                                       'objectid'           => $taskstable . '.xar_objectid',
                                       'name'               => $taskstable . '.xar_name',
                                       'description'       => $taskstable . '.xar_description',
                                       'status'               => $taskstable . '.xar_status',
                                       'priority'           => $taskstable . '.xar_priority',
                                       'private'           => $taskstable . '.xar_private',
                                       'creator'           => $taskstable . '.xar_creator',
                                       'owner'               => $taskstable . '.xar_owner',
                                       'assigner'           => $taskstable . '.xar_assigner',
                                       'date_created'       => $taskstable . '.xar_date_created',
                                       'date_approved'       => $taskstable . '.xar_date_approved',
                                       'date_changed'       => $taskstable . '.xar_date_changed',
                                       'date_start_planned'=> $taskstable . '.xar_date_start_planned',
                                       'date_start_actual' => $taskstable . '.xar_date_start_actual',
                                       'date_end_planned'  => $taskstable . '.xar_date_end_planned',
                                       'date_end_actual'   => $taskstable . '.xar_date_end_actual',
                                       'hours_planned'       => $taskstable . '.xar_hours_planned',
                                       'hours_spent'       => $taskstable . '.xar_hours_spent',
                                       'hours_remaining'   => $taskstable . '.xar_hours_remaining');
    
    
    return $xartable;
}
?>