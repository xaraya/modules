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
    if (!xarVarFetch('message_id',  'int', $data['message_id'], 0, XARVAR_NOT_REQUIRED)) return;

    $data['copy_emails'] = true;
    
    // Get the available email messages
    $data['message_options'] = xarMod::apiFunc('mailer' , 'user' , 'getall_mails', array('state'=>3, 'module'=> "reminders", 'category' => xarModVars::get('reminders', 'custom_emails')));

# --------------------------------------------------------
#
# Unpack the code that was passed
#
	if (empty($code)) die(xarML('No code passed'));
	
	$args['params'] = unserialize(base64_decode($code));
	
	$data['subject'] = $args['params']['subject'];
	$data['message'] = $args['params']['message'];
	
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
        $attachments = array();
        
    	// An attachment taken from the dropdown list
        if (!empty($data['trusted_attachment'])) {
            $attachments[] = array(
                                'name' => $data['trusted_attachment'],
                                'path' => xarModVars::get('grader', 'attachment_dir') . "/" . $data['trusted_attachment'],
                                );
        }
        
        $checkbox = DataPropertyMaster::getProperty(array('name' => 'checkbox'));
        $checkbox->checkInput('copy_emails');
        $bccaddress = $checkbox->value ? array(xarUser::getVar('email')) : array();

        // Bail if no message was chosen
        if (empty($data['message_id']) && empty($data['message_body'])) {
            $data['message_warning'] = xarML('No message was defined');
            return $data;
        }
            
        // Are we testing?
        if (!xarVarFetch('test',        'int', $data['test'],                0, XARVAR_NOT_REQUIRED)) return;
        
        // Bail if no participant was chosen
        if ($data['participant_id'] == 0) {
            $data['message_warning'] = xarML('No participant was defined');
            return $data;
        }
            
        if ($data['test']) {
            // If we are testing, then send to this user
            $recipientname    = xarUser::getVar('name');
            $recipientaddress = xarUser::getVar('email');
            $bccaddress = array();
        } else {
            // If we are not testing, then send to the chosen participant
            $recipientname    = $emailargs['participant']['roles_name'];
            $recipientaddress = $emailargs['participant']['email'];
        }
        // Only send if we don't have any errors
        if (empty($data['message'])) {
            $data['result'] = array();
            try {
                $args = array('sendername'       => xarModVars::get('grader', 'defaultsendername'),
                              'senderaddress'    => xarModVars::get('grader', 'defaultsenderaddress'),
                              'recipientname'    => $recipientname,
                              'recipientaddress' => $recipientaddress,
                              'bccaddresses'     => $bccaddress,
                              'attachments'      => $attachments,
                              'data'             => $emailargs,
                                            );
                if (!empty($data['message_id']))  {
                    // We have a message ID
                    $args['id'] = (int)$data['message_id'];
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
                    $args['message'] = $data['message_body'];
                    $args['subject'] = $data['subject'];
                    // In this case we set the mail type to "text to html"
                    $args['mail_type'] = 2;
                }
                
                // This sends the mail
                $data['result']['code'] = xarMod::apiFunc('mailer','user','send', $args);
                
            } catch (Exception $e) {
                $data['result']['exception'] = $e->getMessage();
            }
            if ($data['participant_id'] != 0) {
                $data['result']['name'] = $emailargs['participant']['roles_name'];
                $data['result']['email'] = $emailargs['participant']['email'];
            } else {
                $data['result']['name'] = '';
                $data['result']['email'] = '';
            }
            if ($data['test']) {
                $data['result']['test_name'] = $recipientname;
                $data['result']['test_email'] = $recipientaddress;
            }
        }
    }

    return $data;
}
?>