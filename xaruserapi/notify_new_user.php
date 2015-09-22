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


function pubsub_userapi_notify_new_user($args)
{
// Load the publications and eventhub module so that we have access to the publications tables
	xarModLoad('publications');
	xarModLoad('eventhub');

	// Get the list of events not yet broadcast
	sys::import('xaraya.structures.query');
	$xartables = xarDB::getTables();
	$q = new Query();
	$q->addtable($xartables['eventhub_broadcast'],'eb');
	
	// Join the publications table to get only publish events
	$q->addtable($xartables['publications'],'pu');
	$q->addfield('eb.id');
	$q->addfield('eb.event_id');
	$q->addfield('eb.broadcast');
	$q->join('eb.event_id','pu.id');
	$q->eq('pu.state', 3);
	$q->eq('eb.broadcast', 0);
	$q->run();
	
	$eventlist = $q->output();
	$allevents = $eventlist;

	// Set up the array to store the list of events for each user
	$user_event_master = array();
	$result = "";
	
	if(!empty($eventlist)) {
			// send Welcome mail to the subscribed user
			try {
			//	$user = $entry['user'];
			//	$eventlist = $entry['events'];			
				$user['name'] = "Eventhub Subscriber";
				$user['email']= $args;
				
				$subscriber_template_id = xarModVars::get('pubsub', 'usermessage');
				
				$args = array('id'               => $subscriber_template_id,
						'sendername'       => "Administrator",
						'senderaddress'    => "admin@eventhubsacramento.com",
						'recipientname'    => "EventHub SUbscriber",
						'recipientaddress' => $user['email'],
					//	'data' => array('eventlist' => $eventlist, 'user' => $user)
					);
				
				$sendmail = xarModAPIFunc('mailer','user','send', $args);
				} catch (Exception $e) {
					$result .= implode(", ", $e->getMessage());
				}
		/*}*/
		
		// Now send notification mail to admin with the number of events and the number of users notified
		// if allowed to notify admin from pubsub backend
		
		$events_count = count($allevents);
		//$total_users_count =  count($user_event_master);
		$total_users_count =1;
		
		$args = array('id'               => 11,
				'sendername'       => "Administrator",
				'senderaddress'    => "admin@eventhubsacramento.com",
				'recipientname'    => 'EventHub_SUbscriber',
				'recipientaddress' => 'netspan@paramss.com',
				'data' => array('events_count' => $events_count, 'users_count' => $total_users_count));
		$sendmail = xarModAPIFunc('mailer','user','send', $args);
		
		if(xarModVars::get('pubsub','debugmode') && in_array(xarUser::getVar('id'),xarConfigVars::get(null, 'Site.User.DebugAdmins'))) {
			$result .=  xarML("Emails sent to #(1) users", $total_users_count);
			$result .= "<pre>";
			foreach ($user_event_master as $entry) {
				$user = $entry['user'];
				$eventlist = $entry['events'];
				$result .= "</pre>";
				$result .= $user['uname'] . "(" . $user['email'] . ")<br/>";
				/*
				foreach ($eventlist as $event) {
					$item = xarMod::apiFunc('publications','user','get', array('id' => $event['id']));
					$result .= $item['title'] . "<br/>";
				}
				*/
			}
			 $result .= "</pre>"; 
			return $result;
		} else {
			$result .=  xarML("Emails sent to #(1) users", $total_users_count);
			return $result;
		}
	} else {
		$result .= xarML("No events to send");
		return $result;
	}

}

?>
