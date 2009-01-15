<?php
/**
 * Send an email
 *
 * @param  $module
 * @param  $id
 * @param  $role_id
 * @param  $sendername
 * @param  $senderaddress
 * @param  $recipientname
 * @param  $recipientaddress
 * @param  $locale
 * @param  $module
 *
 * The sequence of overrides is
 *  1. params passed to this function
 *  2. settings specific to the message to be sent
 *  3. settings of the module passed ot this function
 *  4. settings of the mailer module
 *
 * returns true on success, false on failure
 */
    function mailer_userapi_send($args)
    {
        $module = isset($args['module']) ? $args['module'] : 'mailer';
        if (!isset($args['role_id']) && !isset($args['recipientaddress'])) 
            throw new Exception(xarML('No recipient user id or email address'));

        // Get the recipient's data
        if (isset($args['role_id'])) {
            $object = DataObjectMaster::getObject(array('name' => xarModItemVars::get('mailer','defaultuserobject', xarMod::getID($module))));
            $recipient = $object->getItem(array('itemid' => $args['role_id']));
            $recipientname = $recipient->properties['name']->value;
            $recipientaddress = $recipient->properties['email']->value;
        } else {
            $recipientname = isset($args['recipientname']) ? $args['recipientname'] : xarModItemVars::get('mailer','defaultrecipientname', xarMod::getID($module));
            $recipientaddress = $args['recipientaddress'];
        }
        
        // Get the recipient's locale
            $recipientlocale = isset($args['locale']) ? $args['locale'] : '';
            if (empty($recipientlocale) && isset($recipient->properties['locale'])) $recipientlocale = $recipient->properties['locale']->value;
            if (empty($recipientlocale) && isset($object) && ($object->name == 'roles_users')) $recipientlocale = $recipient->properties['locale']->value;
            if (empty($recipientlocale)) $recipientlocale = xarModItemVars::get('mailer','defaultlocale', xarMod::getID($module));
            
        // Get the list of message aliases (translations of the same message)
            $object = DataObjectMaster::getObjectList(array('name' => xarModItemVars::get('mailer','defaultmailobject', xarMod::getID($module))));
            $where = "locale = '" . $recipientlocale . "' AND alias = " . $args['id'];
            $mailitems = $object->getItems(array('where' => $where));
            
        // Sanity check: do we have a message?
            if (empty($mailitems)) return false;

        // Grab the first one that fits
            $mailitem = current($mailitems);
            
        // Get the footer if this message has one
            $footer = "";
            if (!empty($mailitem['footer'])) {
                $object = DataObjectMaster::getObject(array('name' => 'mailer_footers'));
                $footeritemid = $object->getItem(array('itemid' => $mailitem['footer']));
                if (!empty($object->properties['body']->value)) $footer = $object->properties['body']->value;
            }
            
        // Check if there is a default redirect
            if (xarModItemVars::get('mailer','defaultredirect', xarMod::getID($module))) {
                $redirectaddress = xarModItemVars::get('mailer','defaultredirectaddress', xarMod::getID($module));
                if (empty($redirectaddress)) return false;
                $recipientaddress = $redirectaddress;
            }

        // Check if there is a redirect in the message
            if ($mailitem['redirect']) {
                if (empty($mailitem['redirect_address'])) return false;
                $recipientaddress = $mailitem['redirectaddress'];
            }
            
        // Get the sender's data
            $sendername = isset($args['sendername']) ? $args['sendername'] : $mailitem['sender_name'];
            if (empty($sendername)) $sendername = xarModItemVars::get('mailer','defaultsendername', xarMod::getID($module));
            $senderaddress = isset($args['senderaddress']) ? $args['senderaddress'] : $mailitem['sender_address'];
            if (empty($senderaddress)) $senderaddress = xarModItemVars::get('mailer','defaultsenderaddress', xarMod::getID($module));
        
        // Bundle the data into a nice array
            $args = array('info'         => $recipientaddress,
                          'name'         => $recipientname,
                          'subject'      => $mailitem['subject'],
                          'message'      => $mailitem['body'] . $footer,
                          'htmlmessage'  => $mailitem['body'] . $footer,
                          'from'         => $senderaddress,
                          'fromname'     => $sendername,
                          'attachName'   => '',
                          'attachPath'   => '',
                          'usetemplates' => false);

        // Pass it to the mail module for processing
        if ($mailitem['mail_type'] == 2) {
            if (!xarModAPIFunc('mail','admin','sendhtmlmail', $args)) return;
        } else {
            if (!xarModAPIFunc('mail','admin','sendmail', $args)) return;
        }
        
        // Check we want to save this message and if so do it
            if (xarModItemVars::get('mailer','savetodb', xarMod::getID($module))) {
                $object = DataObjectMaster::getObject(array('name' => 'mailer_history'));
                $args = array(
                            'mail_id' => $mailitem['id'],
                            'sender_name' => $sendername,
                            'sender_address' => $senderaddress,
                            'recipient_name' => $recipientname,
                            'recipient_address' => $recipientaddress,
                            'body' => $mailitem['body'] . $footer,
                            'subject' => $mailitem['subject'],
                            );
                $item = $object->createItem($args);
            }

        return true;
    }
?>
