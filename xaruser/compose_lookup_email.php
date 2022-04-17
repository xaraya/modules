<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2014 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
 
function reminders_user_compose_lookup_email($args)
{
    // Xaraya security
    if (!xarSecurityCheck('ManageReminders')) return;
    xarTpl::setPageTitle('Send Lookup Email');

    if (!xarVarFetch('confirm',     'int', $confirm,            0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('code',        'str', $code,               '', XARVAR_NOT_REQUIRED)) return;

    $data['copy_emails'] = true;
    
    // Get the available email messages
    $data['message_options'] = xarMod::apiFunc('mailer' , 'user' , 'getall_mails', array('state'=>3, 'module'=> "reminders", 'category' => xarModVars::get('reminders', 'lookup_emails')));

# --------------------------------------------------------
#
# Unpack the code that was passed
#
	if (empty($code)) die(xarML('No code passed'));
	
	$args['params'] = unserialize(base64_decode($code));
	
	$data['subject'] = $args['params']['lookup_subject'];
	$data['message_body'] = unserialize($args['params']['lookup_message']);
	$data['lookup_template'] = $args['params']['lookup_template'];
	$name = DataPropertyMaster::getProperty(array('name' => 'name'));
	
	// Get the name components of the recipient to pass to the template
	$name->value = $args['params']['lookup_name'];
	$components = $name->getValueArray();
	foreach ($components as $component) $emailargs[$component['id']] =  $component['value'];
	$emailargs['lookup_name'] = $emailargs['first_name'] . " " . $emailargs['last_name'];
	$emailargs['lookup_email'] = $args['params']['lookup_email'];
	
	// Get the name components of the sender to pass to the template
	$name->value = $args['params']['name'];
	$components = $name->getValueArray();
	$emailargs['my_first_name'] = $components[1]['value'];
	$emailargs['my_last_name'] = $components[2]['value'];
	$emailargs['my_name'] = $emailargs['my_first_name'] . " " . $emailargs['my_last_name'];
	$emailargs['my_email'] = $args['params']['address'];
	
# --------------------------------------------------------
#
# Get some properties for use in the template
#
    $emailargs['name'] = DataPropertyMaster::getProperty(array('name' => 'name'));
    $emailargs['checkbox'] = DataPropertyMaster::getProperty(array('name' => 'checkbox'));
    $emailargs['date'] = DataPropertyMaster::getProperty(array('name' => 'date'));
    $emailargs['number'] = DataPropertyMaster::getProperty(array('name' => 'number'));
    $emailargs['integerbox'] = DataPropertyMaster::getProperty(array('name' => 'integerbox'));
    $emailargs['floatbox'] = DataPropertyMaster::getProperty(array('name' => 'floatbox'));
    $emailargs['textbox'] = DataPropertyMaster::getProperty(array('name' => 'textbox'));
    $emailargs['textarea'] = DataPropertyMaster::getProperty(array('name' => 'textarea'));
    
    if ($confirm) {
# --------------------------------------------------------
#
# Send the email
#        
        $checkbox = DataPropertyMaster::getProperty(array('name' => 'checkbox'));
        $checkbox->checkInput('copy_emails');
        $bccaddress = $checkbox->value ? array(xarUser::getVar('email')) : array();

        // Bail if no message was chosen
        if (empty($data['lookup_template']) && empty($data['message_body'])) {
            $data['message_warning'] = xarML('No message was defined');
            return $data;
        }
            
        // Are we testing?
        if (!xarVarFetch('test',        'int', $data['test'],                0, XARVAR_NOT_REQUIRED)) return;
        
        if ($data['test']) {
            // If we are testing, then send to this user
            $recipientname    = xarUser::getVar('name');
            $recipientaddress = xarUser::getVar('email');
            $bccaddress = array();
        } else {
            // If we are not testing, then send to the chosen participant
            $recipientname    = $emailargs['lookup_name'];
            $recipientaddress = $emailargs['lookup_email'];
        }
        // Only send if we don't have any errors
        if (empty($data['message'])) {
            $data['result'] = array();
            try {
                $args = array('sendername'       => $emailargs['my_name'],
                              'senderaddress'    => $emailargs['my_email'],
                              'recipientname'    => $recipientname,
                              'recipientaddress' => $recipientaddress,
                              'bccaddresses'     => $bccaddress,
                              'data'             => $emailargs,
                                            );
                if (!empty($data['lookup_template']))  {
                    // We have a message ID
                    $args['id'] = (int)$data['lookup_template'];
                    if (!empty($data['message_body'])) {
                    // We have a message ID (which indicates a template) and also a message body
                        $args['subject'] = $data['subject'];
                    // In this case we insert the latter into the former
                        $object = DataObjectMaster::getObject(array('name' => 'mailer_mails'));
                        $object->getItem(array('itemid' => $args['id']));
                        $message = $object->properties['body']->value;
                        $args['message'] = str_replace('#$message#', $data['message_body'], $message);
                        $args['mail_type'] = $object->properties['mail_type']->value;
                        $sendername = $object->properties['sender_name']->value;
                        if (!empty($sendername)) $args['sendername'] = $sendername;
                        $senderaddress = $object->properties['sender_address']->value;
                        if (!empty($senderaddress)) $args['senderaddress'] = $senderaddress;
                        unset($args['id']);
                    }
                } elseif (!empty($data['message_body'])) {
                    // We have only a message body
                    $args['subject'] = $data['subject'];
                    $args['message'] = $data['message_body'];
                    // In this case we set the mail type to "text to html"
                    $args['mail_type'] = 2;
                }
                
                // This sends the mail
                $data['result']['code'] = xarMod::apiFunc('mailer','user','send', $args);
                
                // Now record this email in the history table
                sys::import('xaraya.structures.query');
                $tables = xarDB::getTables();
                $q = new Query('INSERT', $tables['reminders_lookup_history']);
                $q->addfield('lookup_id',   (int)$args['params']['lookup_id']);
                $q->addfield('owner_id',    (int)$args['params']['owner_id']);
                $q->addfield('date',        time());
                $q->addfield('subject',     $args['params']['lookup_subject']);
                $q->addfield('message',     $args['params']['lookup_message']);
                $q->addfield('timecreated', time());
                $q->run();
                
            } catch (Exception $e) {
                $data['result']['exception'] = $e->getMessage();
            }
            
			$data['result']['name'] = $emailargs['lookup_name'];
			$data['result']['email'] = $emailargs['lookup_email'];

            if ($data['test']) {
                $data['result']['test_name'] = $recipientname;
                $data['result']['test_email'] = $recipientaddress;
            }
        }
    }
    return $data;
}
?>