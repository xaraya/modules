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
    
    // Get the reminder entries to process
    if ($args['test']) {
    	if (!xarVarFetch('entry_list',    'str', $data['entry_list'],    '', XARVAR_NOT_REQUIRED)) return;
    } else {
    	$data['entry_list'] = '';
    }
    $items = xarMod::apiFunc('reminders', 'user', 'getall', array('itemids' => $data['entry_list']));

    // Get today's date
    $datetime = new XarDateTime();
    $datetime->settoday();
    $today = $datetime->getTimestamp();
    
    // Run through the active reminders and send emails
    $current_id = 0;
    $previous_id = 0;
    $templates = array();
    $data['results'] = array();
        
    /*
    * For each item we need to find the latest reminder that has not yet been sent
    *
    */
    sys::import('xaraya.structures.query');
    $tables = xarDB::getTables();
    $q = new Query('UPDATE', $tables['reminders_entries']);
    $data['results'] = array();
    foreach ($items as $key => $row) {
    
    	// Get the array of all the reminder dates of this reminder
    	$dates = xarMod::apiFunc('reminders', 'user', 'get_date_array', array('array' => $row));
    	
    	// Run through each of the 10 possible steps
    	$done = false;
    	$sent_ids = array();
    	foreach ($dates as $step) {
    	
    		// An empty step means that no date was defined
    		if ($step['date'] == 0) continue;
    		
    		// We ignore steps with dates that have already passed, whether or not an email was sent
    		if ($step['date'] < $today) continue;
    		
    		// If the step date coincides with today's date, we send an email
    		if ($step['date'] == $today) {
				// Get the template information for this message
				$this_template_id = $row['template_id'];
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
               
    			// This is not a test, so set this period reminder as done
    			if (!$args['test']) {
	    			$sent_ids[] = $step['index'];
	    			$done = true;
	    			// Jump to the next loop
	    			continue;
    			}
    		}
    			
    		// Run through the rest of the steps, in case we have 2 or more with today's date
    		if ($step['date'] == $today) {
	    		$sent_ids[] = $step['index'];
    		}
    	}
    	
    	// Update this reminder for the email(s) we have sent
		$q->clearfields();
		$q->clearconditions();
		$q->eq('id', $row['id']);
    	foreach ($sent_ids as $id) {
    		$q->eq('reminder_done_' . $id, 1);
    	}
    	$q->run();
    	
/*        $current_id = $row['id'];
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
				$this_template_id = $row['template_id'];
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
        */
    }
    return $data['results'];
}
?>