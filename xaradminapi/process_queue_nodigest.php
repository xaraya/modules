<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Process the queue and run all pending jobs (executed by the scheduler module)
 * nodigest - that is one email per event
 * @returns bool
 * @return number of jobs run on success, false if not
 * @throws DATABASE_ERROR
 */
function pubsub_adminapi_process_queue_nodigest($args)
{
    // Get arguments from argument array
    extract($args);

    // Database information
    $tables =& xarDB::getTables();
    $q = new Query();
    $q->addtable($tables['pubsub_process'], 'p');
    $q->addtable($tables['pubsub_events'], 'e');
    $q->join('p.event_id', 'e.id');
    $q->addtable($tables['pubsub_subscriptions'], 's');
    $q->join('s.event_id', 'e.id');
    $q->addfield('e.id AS event_id');
    $q->addfield('s.groupid AS groupid');
    $q->addfield('s.userid AS userid');
    $q->addfield('s.email AS email');
    $q->addfield('p.template_id AS template_id');
    $q->addfield('p.object_id AS object_id');
    $q->addfield('p.module_id AS module_id');
    $q->addfield('p.itemtype AS itemtype');
    $q->addfield('p.itemid AS itemid');
//    $q->qecho();
    $q->run();
    
    $recipients = array();
    foreach ($q->output() as $row) {
        // Add a cc email if there is one
        // Add a default name as we have no other
        if (!empty($row['email'])) $recipients[$row['email']] = xarML('Subscriber');
        // Add a user if one was passed
        $user_id = (int)$row['userid'];
        $user = xarMod::apiFunc('roles', 'user', 'get', array('id' => $user_id));
        if (!empty($user)) $recipients[$user['email']] = $user['name'];
        // Add the descendants of a group, if one was passed
        $group_id = (int)$row['groupid'];
        sys::import('modules.dynamicdata.class.objects.master');
        $group = DataObjectMaster::getObject(array('name' => 'roles_groups'));
        $group->getItem(array('itemid' => $group_id));
        $users = $group->getDescendants(3);
        foreach ($users as $user) {
            $recipients[$user->properties['email']->value] = $user->properties['name']->value;
        }
    }
    var_dump($recipients);
    
    // set count to 1 so that the scheduler knows we're doing OK :)
    $count = 1;

    foreach ($q->output() as $row) {
        xarMod::apiFunc('pubsub','admin','runjob',
                      array('event_id'    => $event_id,
                            'object_id'   => $object_id,
                            'module_id'   => $module_id,
                            'itemtype'    => $itemtype,
                            'itemid'      => $itemid,
                            'template_id' => $template_id));
        $count++;
    }
    return $count;

} // END processq

?>