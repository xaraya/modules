<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */


function reminders_adminapi_process($args)
{
    if (!isset($args['test']))        $args['test'] = false;
    if (!isset($args['params']))      $args['params'] = array();
    if (!isset($args['copy_emails'])) $args['copy_emails'] = false;

    sys::import('modules.dynamicdata.class.objects.master');
    //$entries = DataObjectMaster::getObjectList(array('name' => 'reminders_entries'));
    $mailer_template = DataObjectMaster::getObject(array('name' => 'mailer_mails'));
    
    /*
    // Set all the relevant properties active here
    foreach($entries->properties as $name => $property) {
        if ($property->getDisplayStatus() == DataPropertyMaster::DD_DISPLAYSTATE_DISABLED) continue;
        $entries->properties[$name]->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE);
    }
    
    // Turn the email properties into numbers, because we need to link their corresponding rows from the email table
    // Modify the some properties to be foreign table indices (make them static text)
    $entries->modifyProperty('email_1', array('type' => 1));
    $entries->modifyProperty('email_2', array('type' => 1));
    
    $entries->setFieldList();
    
    $q = $entries->dataquery;*/
    // Add the emails table for each email
    
    $tables = xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($tables['reminders_entries'], 'entries');
    $q->addtable($tables['reminders_emails'], 'email_1');
    $q->leftjoin('entries.email_id_1', 'email_1.id');
    $q->addtable($tables['reminders_emails'], 'email_2');
    $q->leftjoin('entries.email_id_2', 'email_2.id');
    
    // Add only these fields
    $q->addfields(array(
    				'entries.id',
    			  	'email_1.name AS name_1',
    			  	'email_1.address AS address_1',
    			  	'email_2.name AS name_2',
    			  	'email_2.address AS address_2',
    			  	'message',
    			  	'template_id',
    			  	'due_date',
    			  	'recurring',
    			  	'recur_period',
    			  	'reminder_warning_1 AS reminder_1',
    			  	'reminder_done_1',
    			  	'reminder_warning_2 AS reminder_2',
    			  	'reminder_done_2',
    			  	'reminder_warning_3 AS reminder_3',
    			  	'reminder_done_3',
    			  	'reminder_warning_4 AS reminder_4',
    			  	'reminder_done_4',
    			  	'reminder_warning_5 AS reminder_5',
    			  	'reminder_done_5',
    			  	'reminder_warning_6 AS reminder_6',
    			  	'reminder_done_6',
    			  	'reminder_warning_7 AS reminder_7',
    			  	'reminder_done_7',
    			  	'reminder_warning_8 AS reminder_8',
    			  	'reminder_done_8',
    			  	'reminder_warning_9 AS reminder_9',
    			  	'reminder_done_9',
    			  	'reminder_warning_10 AS reminder_10',
    			  	'reminder_done_10',
    			  )
    );
    
    // Only active reminders
    $q->eq('entries.state', 3);

    if ($args['test']) {
    	if (!xarVarFetch('entry_list',    'str', $data['entry_list'],    '', XARVAR_NOT_REQUIRED)) return;
    	$data['entry_list'] = explode(',', $data['entry_list']);
    	$q->in('entries.id', $data['entry_list']);
    }
    $q->qecho();
//    $items = $entries->getItems();
	$q->run();
    $items = $q->output();
    var_dump($items);exit;
    /*
    sys::import('xaraya.structures.query');
    $tables = xarDB::getTables();
    $q = new Query('SELECT');
    $q->addtable($tables['reminders_entries'], 'entries');
    $q->addtable($tables['reminders_emails'],  'emails');
    $q->join('entries.email_id', 'emails.id');
    $q->run();
    $result = $q->output();
*/    
    // Run through the active reminders and send emails
    $current_id = 0;
    $previous_id = 0;
    $templates = array();
    $data['results'] = array();
    
    // Get the time in seconds before the due date for each of the possible periods
    $days = xarMod::apiFunc('reminders', 'admin', 'get_warning_period_time', array('timeframe' => 'seconds'));
    
    /*
    * For each item we need to find the latest reminder that has not yet been sent
    *
    */
    $data['results'] = array();
    foreach ($items as $key => $row) {
        $current_id = $row['id'];
        $found = false;
        if ($current_id != $previous_id) {
            for ($i=1;$i<=10;$i++) {
            
                // Get the data for this period
                $this_id = 'reminder_' . $i;
                $this_done_id = 'reminder_done_' . $i;
                $this_reminder = $row[$this_id];
                $this_reminder_done = $row[$this_done_id];
                
                // Get the data for the next period
                $j = $i + 1;
                $next_id = 'reminder_' . $j;
                $next_done_id = 'reminder_done_' . $j;
                
                // If the reminder period is 0 (was not defined), then consider it done and move on
                if (empty($this_reminder)) {
                    $items[$key][$this_reminder_done] = 1;
                }
                
                // If we already sent an email for this date, then move on
                if ($this_reminder_done) {
	                continue;
                }
                
				// The email for this period is not sent: do it
				// Get the template information for this message
				$this_template_id = $row['template'];
				if (isset($templates[$this_template_id])) {
					// We already have the information.
				} else {
					// Get the information
					$mailer_template->getItem(array('itemid' => $this_template_id));
					$values = $mailer_template->getFieldValues();
					$templates[$this_template_id]['message_id']   = $values['id'];
					$templates[$this_template_id]['message_body'] = $values['body'];
					$templates[$this_template_id]['subject']      = $values['subject'];
				}
				// Assemble the parameters for the email
				$params['message_id']   = $templates[$this_template_id]['message_id'];
				$params['message_body'] = $templates[$this_template_id]['message_body'];
				$params['subject']      = $templates[$this_template_id]['subject'];
				// Send the email
				$data['result'] = xarMod::apiFunc('reminders', 'admin', 'send_email', array('info' => $row, 'params' => $params, 'copy_emails' => $args['copy_emails'], 'test' => $args['test']));        	
				$data['results'] = array_merge($data['results'], array($data['result']));
               
    			// If this is a test, exit now
    			if ($args['test']) break;
    			
    			// This is not a test, so set this period reminder as done
    			$items[$key][$this_reminder_done] = 1;
            }
        }
        $previous_id = $current_id;
    }
    return $data['results'];
}
?>