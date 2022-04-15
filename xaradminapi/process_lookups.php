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


function reminders_adminapi_process_lookups($args)
{
    if (!isset($args['test']))        $args['test'] = false;
    if (!isset($args['params']))      $args['params'] = array();
    if (!isset($args['copy_emails'])) $args['copy_emails'] = false;

    sys::import('modules.dynamicdata.class.objects.master');
    $mailer_template = DataObjectMaster::getObject(array('name' => 'mailer_mails'));
    	
    /*
    * For each item we need to find the latest reminder that has not yet been sent
    */
    
	if ($args['test']) {

    	// In the test environment, we just look which lookups were checked in the test lookup admin page. We will receive all those emails.
    	if (!xarVarFetch('entry_list',    'str', $data['entry_list'],    '', XARVAR_NOT_REQUIRED)) return;
		$items = xarMod::apiFunc('reminders', 'user', 'getall_lookups', array('itemids' => $data['entry_list']));

		$data['results'] = array();
		foreach ($items as $key => $row) {
	
			// Prepare the data we need to send an email
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
			$data['result'] = xarMod::apiFunc('reminders', 'admin', 'send_email_lookup', array('info' => $row, 'params' => $params, 'copy_emails' => $args['copy_emails'], 'test' => $args['test']));        	
			$data['results'] = array_merge($data['results'], array($data['result']));

		}
	} else {
		// Get the owners to be processed (sent an email)
		$owners = xarMod::apiFunc('reminders', 'user', 'getall_owners', array('do_lookup' => true));
		
		// Run through the owners
		$data['results'] = array();
		foreach ($owners as $owner) {
			// Get the entry which will figure in the email
			$row = xarMod::apiFunc('reminders', 'admin', 'generate_random_entry', array('user' => $owner['id']));

			// Rename the parameters for sending to the email template
			$row['lookup_name'] = $row['name'];
			$row['lookup_email'] = $row['email'];
			$row['lookup_phone'] = $row['phone'];
			
			// Add the owner name and addfress to the row data so we know where to send
			$row['name'] = $owner['name'];
			$row['address'] = $owner['address'];
			
		    // Add the information for subject and message the email recipient can use to create his/her email
		    $row['subject'] = xarModVars::get('reminders', 'subject');
		    $row['message'] = xarModVars::get('reminders', 'message');
		    
		    // Encode the row information so we can send that to the template
			$row['encoded'] = 'dork' //base64_encode($row);
			
			// Prepare the data we need to send an email
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
			$data['result'] = xarMod::apiFunc('reminders', 'admin', 'send_email_lookup', array('info' => $row, 'params' => $params, 'copy_emails' => $args['copy_emails'], 'test' => $args['test']));        	
			$data['results'] = array_merge($data['results'], array($data['result']));
		}
	}
    return $data['results'];
}
?>