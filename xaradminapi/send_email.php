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
 
function reminders_adminapi_send_email($data)
{
# --------------------------------------------------------
#
# Get some properties for use in the template
#
    $data['name'] = DataPropertyMaster::getProperty(array('name' => 'name'));
    $data['checkbox'] = DataPropertyMaster::getProperty(array('name' => 'checkbox'));
    $data['date'] = DataPropertyMaster::getProperty(array('name' => 'date'));
    $data['number'] = DataPropertyMaster::getProperty(array('name' => 'number'));
    $data['integerbox'] = DataPropertyMaster::getProperty(array('name' => 'integerbox'));
    $data['floatbox'] = DataPropertyMaster::getProperty(array('name' => 'floatbox'));
    $data['textbox'] = DataPropertyMaster::getProperty(array('name' => 'textbox'));
    $data['textarea'] = DataPropertyMaster::getProperty(array('name' => 'textarea'));

# --------------------------------------------------------
#
# Send the participant an email with the attachments
#
    $bccaddress = $data['copy_emails'] ? array(xarUser::getVar('email')) : array();

    $result = array();
    $attachments = array();
    if ($data['test']) {
        // If we are testing, then send to this user
        $recipientname    = xarUser::getVar('name');
        $recipientaddress = xarUser::getVar('email');
        $bccaddress = array();
        $data['name']->value = $data['info']['name'];
    } else {
        // If we are not testing, then send to the chosen participant
        $data['name']->value = $data['info']['name'];
        $recipientname    = $data['name']->getValue();
        $recipientaddress = $data['info']['email'];
    }
    $data['reminder_text'] = $data['info']['message'];
    unset($data['info']);
    try {
        // Set the paramenters for the send function
        $args = array('sendername'       => xarModVars::get('reminders', 'defaultsendername'),
                      'senderaddress'    => xarModVars::get('reminders', 'defaultsenderaddress'),
                      'recipientname'    => $recipientname,
                      'recipientaddress' => $recipientaddress,
                      'bccaddresses'     => $bccaddress,
                      'attachments'      => $attachments,
                      'data'             => $data, 
                    );

        // Check if we have a subject/message or a message ID
        if (empty($data['params']['subject']) && empty($data['params']['message_body'])) {
            // Bail if no message ID available
            if (empty($data['params']['message_id']))
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
        }
        // Send the email
        $result['code'] = xarMod::apiFunc('mailer','user','send', $args);
      
    } catch (Exception $e) {
        $result['exception'] = $e->getMessage();
    }
    $result['name'] = $recipientname;
    $result['email'] = $recipientaddress;
    return $result;
}
?>