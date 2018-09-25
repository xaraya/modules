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

    // Get the recipients
    $tables =& xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($tables['pubsub_process'], 'p');
    $q->addtable($tables['pubsub_events'], 'e');
    $q->join('p.event_id', 'e.id');
    $q->addtable($tables['pubsub_subscriptions'], 's');
    $q->join('s.event_id', 'e.id');
    $q->addfield('e.id AS event_id');
    $q->addfield('s.groupid AS groupid');
    $q->addfield('s.userid AS userid');
    $q->addfield('s.email AS email');
    // Only pending jobs
    $q->eq('p.state',2);
//    $q->qecho();
    $q->run();
    
    $pendings = $q->output();
    // Bail if nothing to do
    if (empty($pendings)) return false;

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
        $group_id = (int)$row['groupid'];
        sys::import('modules.dynamicdata.class.objects.master');
        $group = DataObjectMaster::getObject(array('name' => 'roles_groups'));
        $group->getItem(array('itemid' => $group_id));
        $users = $group->getDescendants(3);
        foreach ($users as $user) {
            $recipients[$row['event_id']][$user->properties['email']->value] = $user->properties['name']->value;
        }
    }

    // Get the queue again, without subscriptions
    $q = new Query('SELECT');
    $q->addtable($tables['pubsub_process'], 'p');
    $q->addtable($tables['pubsub_events'], 'e');
    $q->join('p.event_id', 'e.id');
    $q->addfield('e.id AS event_id');
    $q->addfield('e.event_type AS event_type');
    $q->addfield('p.template_id AS template_id');
    $q->addfield('p.object_id AS object_id');
    $q->addfield('p.module_id AS module_id');
    $q->addfield('p.itemtype AS itemtype');
    $q->addfield('p.itemid AS itemid');
    $q->addfield('p.url AS url');
    $q->eq('p.state',2);
//    $q->qecho();
    $q->run();

    // set count to 1 so that the scheduler knows we're doing OK :)
    $count = 1;

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
    
    // Run through each of the entries in the queue
    sys::import('modules.dynamicdata.class.properties.master');
    foreach ($q->output() as $row) {

        if (!in_array($row['event_type'], $recognized_events)) continue;
        
        // Assemble the message
        $event_object = DataObjectMaster::getObject(array('objectid' => (int)$row['object_id']));
        $mail_data['object_name'] = $event_object->name;
        $mail_data['module_name'] = xarMod::getName();
        $mail_data['event_type']  = $row['event_type'];
        $mail_data['url']         = $row['url'];
        
        // Send the mails
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
                            'mail_data'     => $mail_data,
                            ));
        $count++;
    }
    return $count;

}

?>