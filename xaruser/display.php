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
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function workflow_user_display()
{
    if (!xarSecurityCheck('ReadWorkflow')) return;
    xarTplSetPageTitle('Display Activities');

    // Get all the activities
    sys::import('modules.dynamicdata.class.objects.master');
    $activities = DataObjectMaster::getObjectList(array('name' => 'workflow_activities'));
//    $where = "type = 'start'";
    $activities->getItems();

    // For each activity get the roles and add them to the activity
    sys::import('xaraya.structures.query');
    $tables = xarDB::getTables();
    $q = new Query('SELECT',$tables['workflow_activity_roles']);
    foreach ($activities->items as $key => $item) {
        $q->eq('activityId',$item['id']);
        $q->run();
        $result = $q->output();
        $q->clearconditions();
        if (!empty($result)) {
            $roles = array();
            foreach ($result as $row) $roles[] = $row['roleid'];
            $activities->items[$key]['roles'] = $roles;
        } else {
            $activities->items[$key]['roles'] = array();
        }
    }
    $data['activities'] = $activities->items;

    // Get all the instances
    $instances = DataObjectMaster::getObjectList(array('name' => 'workflow_instance_activities'));
    $where = "status = 'running'";
    $instances->getItems(array('where'=>$where));
    $data['properties'] = $instances->getProperties();
    $data['items'] = $instances->items;
    return $data;
}
?>