<?php
/**
 * File: $Id$
 * 
 * Workflow table definitions function
 * 
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage workflow
 * @author mikespub
 */

/**
 * Return workflow table names to xaraya
 * 
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 * 
 * @access private 
 * @return array 
 */
function workflow_xartables()
{ 
    // Initialise table array
    $xarTables = array(); 

// Warning : the table names are hard-coded in lib/Galaxia at the moment !

    // Get the name for the workflow tables.
    $xarTables['workflow_activities'] = 'galaxia_activities';
    $xarTables['workflow_activity_roles'] = 'galaxia_activity_roles';
    $xarTables['workflow_instance_activities'] = 'galaxia_instance_activities';
    $xarTables['workflow_instance_comments'] = 'galaxia_instance_comments';
    $xarTables['workflow_instances'] = 'galaxia_instances';
    $xarTables['workflow_processes'] = 'galaxia_processes';
    $xarTables['workflow_roles'] = 'galaxia_roles';
    $xarTables['workflow_transitions'] = 'galaxia_transitions';
    $xarTables['workflow_user_roles'] = 'galaxia_user_roles';
    $xarTables['workflow_workitems'] = 'galaxia_workitems';

    // Return the table information
    return $xarTables;
} 

?>
