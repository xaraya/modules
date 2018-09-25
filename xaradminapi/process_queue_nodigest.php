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

    // Get the recipients
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
    // Only pending jobs
    $q->eq('p.state',2);
//    $q->qecho();
    $q->run();
    
    $recipients = array();
    foreach ($q->output() as $row) {
        // Add a cc email if there is one
        // Add a default name as we have no other
        if (!empty($row['email'])) $recipients[$row['event_id']][$row['email']] = xarML('Subscriber');
        // Add a user if one was passed
        $user_id = (int)$row['userid'];
        $user = xarMod::apiFunc('roles', 'user', 'get', array('id' => $user_id));
        if (!empty($user)) $recipients[$row['event_id']][$user['email']] = $user['name'];
        // Add the descendants of a group, if one was passed
        $group_id = (int)$row['groupid'];
        sys::import('modules.dynamicdata.class.objects.master');
        $group = DataObjectMaster::getObject(array('name' => 'roles_groups'));
        $group->getItem(array('itemid' => $group_id));
        $users = $group->getDescendants(3);
        foreach ($users as $user) {
            $recipients[$row['event_id']][$user->properties['email']->value] = $user->properties['name']->value;
        }
    }

    $q = new Query('SELECT', $tables['pubsub_process']);
    $q->eq('state',2);
//    $q->qecho();
    $q->run();
    
    // set count to 1 so that the scheduler knows we're doing OK :)
    $count = 1;

    // Run through each of the entries in the queue
    foreach ($q->output() as $row) {
        xarMod::apiFunc('pubsub','admin','runjob',
                      array('event_id'      => (int)$row['event_id'],
                            'object_id'     => (int)$row['object_id'],
                            'module_id'     => (int)$row['module_id'],
                            'itemtype'      => (int)$row['itemtype'],
                            'itemid'        => (int)$row['itemid'],
                            'template_id'   => (int)$row['template_id'],
                            'recipients'    => $recipients[$row['event_id']],
                            'sendername'    => xarModVars::get('pubsub', 'defaultsendername'),
                            'senderaddress' => xarModVars::get('pubsub', 'defaultsenderaddress'),
                            ));
        $count++;
    }
    return $count;

} // END processq

?>