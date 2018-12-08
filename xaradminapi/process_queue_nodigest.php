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
 * @author Marc Lutolf <marc@luetolf-carroll.com>
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

    // Get the events with subscriptions
    $tables =& xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($tables['pubsub_process'], 'p');
    $q->addtable($tables['pubsub_events'], 'e');
    $q->join('p.event_id', 'e.id');
    $q->addtable($tables['pubsub_subscriptions'], 's');
    $q->join('s.event_id', 'e.id');
    $q->addfield('e.id AS event_id');           // The ID of the event
    $q->addfield('s.groupid AS groupid');       // The ID of a group of recipients
    $q->addfield('s.userid AS userid');         // The ID of a specific user
    $q->addfield('s.email AS email');           // The 
    // Only want pending jobs
    $q->eq('e.state',3);
    $q->eq('p.state',2);
//    $q->qecho();
    $q->run();

    $pendings = $q->output();
    // Bail if nothing to do
    if (empty($pendings)) return 0;

    $recipients = array();
    foreach ($pendings as $row) {
        // Add a cc email if there is one
        // Add a default name as we have no other
        if (!empty($row['email'])) $recipients[$row['event_id']][$row['email']] = xarML('Subscriber');
        // Add a user if one was passed
        $user_id = (int)$row['userid'];
        $user = xarMod::apiFunc('roles', 'user', 'get', array('id' => $user_id));
        if (!empty($user)) $recipients[$row['event_id']][$user['email']] = $user['name'];
        
        // Add the descendants of a group, if one was passed
        if (!empty($row['groupid'])) {
            $group_id = (int)$row['groupid'];
            sys::import('modules.dynamicdata.class.objects.master');
            $group = DataObjectMaster::getObject(array('name' => 'roles_groups'));
            $group->getItem(array('itemid' => $group_id));
            $users = $group->getDescendants(xarRoles::ROLES_STATE_ACTIVE);
            foreach ($users as $user) {
                $recipients[$row['event_id']][$user->properties['email']->value] = $user->properties['name']->value;
            }
        }
    }

    // Get the queue again, without subscriptions
    $q = new Query('SELECT');
    $q->addtable($tables['pubsub_process'], 'p');
    $q->addtable($tables['pubsub_events'], 'e');
    $q->join('p.event_id', 'e.id');
    $q->addfield('e.id AS event_id');
    $q->addfield('e.event_type AS event_type');
    $q->addfield('p.id AS job_id');
    $q->addfield('p.template_id AS template_id');
    $q->addfield('p.object_id AS object_id');
    $q->addfield('p.module_id AS module_id');
    $q->addfield('p.itemtype AS itemtype');
    $q->addfield('p.itemid AS itemid');
    $q->addfield('p.url AS url');
    $q->eq('p.state',2);
//    $q->qecho();
    $q->run();

    // This is the data which is inserted into the mail message when it compiles
    $mail_data = array(
                    'header'  => xarML('Notification from #(1)', xarModVars::get('themes', 'SiteName')),
                    'footer'  => xarML('Xaraya #(1) Module', UCFirst(xarMod::getName())),
                    'title'   => date('r'),
    );

    // We only recognize certain types of events
    $recognized_events = xarModVars::get('pubsub', 'recognized_events');
    if (empty($recognized_events)) return false;
    
    $recognized_events = explode(',', xarModVars::get('pubsub', 'recognized_events'));
    foreach ($recognized_events as $k => $v) $recognized_events[$k] = trim($v);
    
    // Set up an object to update each job
    $q1 = new Query('UPDATE', $tables['pubsub_process']);
    $q1->addfield('time_modified', time());
    $q1->addfield('state', 1);

    // Run through each of the entries in the queue
    $count = 0;
    $results = array();
    sys::import('modules.dynamicdata.class.properties.master');
    foreach ($q->output() as $row) {
        // Is this a proper event?
        if (!in_array($row['event_type'], $recognized_events)) continue;
        // Does this event have subscribers?
        if (!isset($recipients[$row['event_id']])) continue;
        
        // Assemble the message
        $event_object = DataObjectMaster::getObject(array('objectid' => (int)$row['object_id']));
        $mail_data['event_id']    = (int)$row['event_id'];
        $mail_data['object_id']   = (int)$row['object_id'];
        $mail_data['object_name'] = $event_object->name;
        $mail_data['module_id']   = (int)$row['module_id'];
        $mail_data['module_name'] = xarMod::getName();
        $mail_data['itemtype']    = (int)$row['itemtype'];
        $mail_data['itemid']      = (int)$row['itemid'];
        $mail_data['event_type']  = $row['event_type'];
        $mail_data['url']         = $row['url'];
        
        // Send the mails
        $result = xarMod::apiFunc('pubsub','admin','runjob',
                      array('template_id'   => (int)$row['template_id'],
                            'recipients'    => $recipients[$row['event_id']],
                            'sendername'    => xarModVars::get('pubsub', 'defaultsendername'),
                            'senderaddress' => xarModVars::get('pubsub', 'defaultsenderaddress'),
                            'mail_data'     => $mail_data,
                            ));
        $count = $count + count($recipients[$row['event_id']]);
        // Set the job's state to inactive
        $q1->eq('id', (int)$row['job_id']);
//        $q1->qecho();
        $q1->run();
        // Clear this condition for the next round
        $q1->clearconditions();
        
        // If debug mode is on, then write the results to the log
        $message = xarML('Pubsub: Sent out #(1) emails', $count);
        xarLog::message($message, xarLog::LEVEL_DEBUG);
    }
    return $count;

}

?>