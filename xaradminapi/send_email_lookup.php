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
 
function reminders_adminapi_send_email_lookup($data)
{
# --------------------------------------------------------
#
# Get some properties for use in the template
#
    $data['name']        = DataPropertyMaster::getProperty(array('name' => 'name'));
    $data['checkbox']    = DataPropertyMaster::getProperty(array('name' => 'checkbox'));
    $data['date']        = DataPropertyMaster::getProperty(array('name' => 'date'));
    $data['number']      = DataPropertyMaster::getProperty(array('name' => 'number'));
    $data['integerbox']  = DataPropertyMaster::getProperty(array('name' => 'integerbox'));
    $data['floatbox']    = DataPropertyMaster::getProperty(array('name' => 'floatbox'));
    $data['textbox']     = DataPropertyMaster::getProperty(array('name' => 'textbox'));
    $data['textarea']    = DataPropertyMaster::getProperty(array('name' => 'textarea'));

# --------------------------------------------------------
#
# Send the owner an email
#

    $result = array();
    $attachments = array();
    $data['name']->value = $data['info']['name'];
    
    // Set a placeholder name if we don't have one
	if (empty($data['name']->value)) $data['name']->setValue(array(array('id' => 'last_name', 'value' => xarModVars::get('mailer', 'defaultrecipientname'))));
    
	// Get the name and address of the chosen participant
	$recipientname    = $data['name']->getValue();
	$recipientaddress = $data['info']['address'];

	// Add a CC if there is one
	if (!empty($data['info']['address_2'])) {
	    $data['name']->value = $data['info']['name'];
	    $ccname = $data['name']->getValue();
    	$ccaddress = array($data['info']['address_2'] => $ccname);
	} else {
    	$ccaddress = array();
	}
    // Maybe we'll add a BCC at some point
    $bccaddress = $data['copy_emails'] ? array(xarUser::getVar('email')) : array();

    $data['lookup_name']   = $data['info']['lookup_name'];
    $data['lookup_email']  = $data['info']['lookup_email'];
    $data['lookup_phone']  = $data['info']['lookup_phone'];

    // We also need the first name of the recipient of the subsequent email to the lookup
    $data['name']->value = $data['lookup_name'];
    $components = $data['name']->getValueArray();
    $data['lookup_first_name']   = $components[1]['value'];
    // Set the property back to its previous value. We use this for the salutation in this email
    $data['name']->value = $data['info']['name'];
    
    $data['encoded']      = $data['info']['encoded'];

    // Get today's date
    $datetime = new XarDateTime();
    $datetime->settoday();
    $today = $datetime->getTimestamp();

    unset($data['info']);

    try {
        // Set the paramenters for the send function
        $args = array('sendername'       => xarModVars::get('reminders', 'defaultsendername'),
                      'senderaddress'    => xarModVars::get('reminders', 'defaultsenderaddress'),
                      'recipientname'    => $recipientname,
                      'recipientaddress' => $recipientaddress,
                      'ccaddresses'      => $ccaddress,
                      'bccaddresses'     => $bccaddress,
                      'attachments'      => $attachments,
                      'data'             => $data, 
                    );
		
        // Check if we have a subject/message or a message ID
        if (empty($data['params']['subject']) && empty($data['params']['message_body'])) {
            // Bail if no message ID available
            if (empty($data['params']['message_id']));
            $result['code'] = 2;
            return $result;
        }

        if (!empty($data['params']['message_id']))  {
            // We have a message ID
            $args['id'] = (int)$data['params']['message_id'];
            // Get the message template
            $object = DataObjectMaster::getObject(array('name' => 'mailer_mails'));
            $object->getItem(array('itemid' => $args['id']));
            // The subject can be overwritten
            $args['subject'] = $object->properties['subject']->value;
            if (!empty($data['params']['subject'])) $args['subject'] = $data['params']['subject'];
            if (!empty($data['params']['message_body'])) {
            // We have a message ID (which indicates a template) and also a message body
            // In this case we insert the latter into the former
                $message = $object->properties['body']->value;
                $args['message'] = str_replace('#$message#', $data['params']['message_body'], $message);
                $args['mail_type'] = $object->properties['mail_type']->value;
                $sendername = $object->properties['sender_name']->value;
                if (!empty($sendername)) $args['sendername'] = $sendername;
                $senderaddress = $object->properties['sender_address']->value;
                if (!empty($senderaddress)) $args['senderaddress'] = $senderaddress;
                unset($args['id']);
            }
        } elseif (!empty($data['params']['message_body'])) {
            // We have only a message body
            if (isset($data['params']['subject'])) $args['subject'] = $data['params']['subject'];
            $args['message'] = $data['params']['message_body'];
            // In this case we set the mail type to "text to html"
            $args['mail_type'] = 2;
        } else {
        	// This should not happen
        	$result['code'] = 2;
        }
        // Send the email
        $result['code'] = xarMod::apiFunc('mailer','user','send', $args);

        // Save to the database if called for
/*
        if (xarModVars::get('reminders', 'save_history') && ($result['code'] == 0)) {
			$history = DataObjectMaster::getObject(array('name' => 'reminders_history'));
			$history->createItem(array(
									'entry_id' => $data['entry_id'],
									'message'  => $data['reminder_text'],
									'address'  => $recipientaddress,
								));
        }
*/      
    } catch (Exception $e) {
        $result['exception'] = $e->getMessage();
    }
    $result['name'] = $recipientname;
    $result['email'] = $recipientaddress;
    
    // if we are testing, then add the test name and email
	if ($data['test']) {
		// If we are testing, then send to this user
		$result['test_name']    = xarUser::getVar('name');
		$result['test_email']   = xarUser::getVar('email');
	}

    return $result;
}
?>