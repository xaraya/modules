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
    if (!xarSecurity::check('ManageReminders')) return;
    xarTpl::setPageTitle('Send Lookup Email');

    if (!xarVar::fetch('confirm',     'int',   $confirm,             0, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('code',        'str',   $code,                '', xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('subject',     'isset', $subject,             NULL, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('message',     'isset', $message,             NULL, xarVar::NOT_REQUIRED)) return;
    if (!xarVar::fetch('copy_emails', 'bool',  $data['copy_emails'], true, xarVar::NOT_REQUIRED)) return;

    $data['copy_emails'] = true;
    
    // Get the available email messages
    $data['message_options'] = xarMod::apiFunc('mailer' , 'user' , 'getall_mails', array('state'=>3, 'module'=> "reminders", 'category' => xarModVars::get('reminders', 'lookup_emails')));

# --------------------------------------------------------
#
# Unpack the code that was passed
#
	if (empty($code)) die(xarML('No code passed'));
	
	$args['params'] = unserialize(base64_decode($code));
	
	$data['lookup_id'] = $args['params']['lookup_id'];
	$data['owner_id'] = $args['params']['owner'];
	$data['subject'] = $args['params']['subject'];
	if (isset($subject)) $data['subject'] = $subject;
	$data['message'] = unserialize($args['params']['message']);
	if (isset($message)) $data['message'] = $message;
	$data['lookup_template'] = $args['params']['template_id'];
		
	// FIXME: obviously
	$data['lookup_template'] = 20;
	
	$name = DataPropertyMaster::getProperty(array('name' => 'name'));
	
	// Get the name components of the recipient to pass to the template
	$name->value = $args['params']['lookup_name'];
	$components = $name->getValueArray();
	foreach ($components as $component) $emailargs[$component['id']] =  $component['value'];
	$emailargs['name'] = $emailargs['first_name'] . " " . $emailargs['last_name'];
	$emailargs['email'] = $args['params']['lookup_email'];
	
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
    $emailargs['name_property'] = DataPropertyMaster::getProperty(array('name' => 'name'));
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
        if (empty($data['lookup_template']) && empty($data['message'])) {
            $data['message_warning'] = xarML('No message was defined');
            return $data;
        }
            
        // Are we testing?
        if (!xarVar::fetch('test',        'int', $data['test'],                0, xarVar::NOT_REQUIRED)) return;
        
        if ($data['test']) {
            // If we are testing, then send to this user
            $recipientname    = xarUser::getVar('name');
            $recipientaddress = xarUser::getVar('email');
            $bccaddress = array();
        } else {
            // If we are not testing, then send to the chosen participant
            $recipientname    = $emailargs['name'];
            $recipientaddress = $emailargs['email'];
        }
        // Only send if we don't have any errors
        if (empty($data['message_warning'])) {
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
                    if (!empty($data['message'])) {
                    // We have a message ID (which indicates a template) and also a message body
                        $args['subject'] = $data['subject'];
                    // In this case we insert the latter into the former
                        $object = DataObjectMaster::getObject(array('name' => 'mailer_mails'));
                        $object->getItem(array('itemid' => $args['id']));
                        $message = $object->properties['body']->value;
                        $args['message'] = str_replace('#$message#', $data['message'], $message);
                        $args['mail_type'] = $object->properties['mail_type']->value;
                        unset($args['id']);
                    }
                } elseif (!empty($data['message'])) {
                    // We have only a message body
                    $args['subject'] = $data['subject'];
                    $args['message'] = $data['message'];
                    // In this case we set the mail type to "text to html"
                    $args['mail_type'] = 2;
                }
                
                // This sends the mail
                $data['result']['code'] = xarMod::apiFunc('mailer','user','send', $args);
                
                // Now record this email in the history table, if the email was successfully sent
                if ($data['result']['code'] == 0) {
					sys::import('xaraya.structures.query');
					$tables = xarDB::getTables();
					$q = new Query('INSERT', $tables['reminders_lookup_history']);
					$q->addfield('lookup_id',   (int)$data['lookup_id']);
					$q->addfield('owner_id',    (int)$data['owner_id']);
					$q->addfield('date',        time());
					$q->addfield('subject',     $data['subject']);
					$q->addfield('message',     $data['message']);
					$q->addfield('timecreated', time());
					$q->run();
                }
                
            } catch (Exception $e) {
                $data['result']['exception'] = $e->getMessage();
            }
            
			$data['result']['name'] = $emailargs['name'];
			$data['result']['email'] = $emailargs['email'];

            if ($data['test']) {
                $data['result']['test_name'] = $recipientname;
                $data['result']['test_email'] = $recipientaddress;
            }
        }
    }
    return $data;
}
?>