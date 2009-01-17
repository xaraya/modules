<?php
/**
 * Send an email
 *
 * @param  $module
 * @param  $id
 * @param  $name
 * @param  $role_id
 * @param  $sendername
 * @param  $senderaddress
 * @param  $recipientname
 * @param  $recipientaddress
 * @param  $locale
 * @param  $module
 * @param  $data
 *
 * The sequence of overrides is
 *  1. params passed to this function
 *  2. settings specific to the message to be sent
 *  3. settings of the module passed ot this function
 *  4. settings of the mailer module
 *
 * Returns
 *  0 on success
 *  1 no recipient address available
 *  2 no message available
 *  3 default redirect checked, but no redirect address available
 *  4 message redirect checked, but no redirect address available
 *  5 sending message failed
 *  6 BL compilation failed
 *  7 no correct user object available
 */
    function mailer_userapi_send($args)
    {
        $module = isset($args['module']) ? $args['module'] : 'mailer';
        if (!isset($args['role_id']) && !isset($args['recipientaddress'])) 
            throw new Exception(xarML('No recipient user id or email address'));

        // Get the recipient's data
        if (isset($args['role_id'])) {
                $object = DataObjectMaster::getObject(array('name' => xarModItemVars::get('mailer','defaultuserobject', xarMod::getID($module))));
                if (!is_object($object)) return 7;
                $result = $object->getItem(array('itemid' => $args['role_id']));
                if (!$result) return 7;
                if (!isset($object->properties['name']) ||!isset($object->properties['email'])) return 7;
                $recipientname = $object->properties['name']->getValue();
                $recipientaddress = $object->properties['email']->getValue();
        } else {
            $recipientname = isset($args['recipientname']) ? $args['recipientname'] : xarModItemVars::get('mailer','defaultrecipientname', xarMod::getID($module));
            $recipientaddress = isset($args['recipientaddress']) ? $args['recipientaddress'] : '';
            if (empty($recipientaddress)) return 1;
        }
        // Get the recipient's locale
            $recipientlocale = isset($args['locale']) ? $args['locale'] : '';
            if (empty($recipientlocale) && isset($recipient->properties['locale'])) $recipientlocale = $recipient->properties['locale']->getValue();
            if (empty($recipientlocale) && isset($object) && ($object->name == 'roles_users')) $recipientlocale = $recipient->properties['locale']->getValue();
            if (empty($recipientlocale)) $recipientlocale = xarModItemVars::get('mailer','defaultlocale', xarMod::getID($module));
            
        // Get the list of message aliases (translations of the same message)
            $object = DataObjectMaster::getObjectList(array('name' => xarModItemVars::get('mailer','defaultmailobject', xarMod::getID($module))));
            if (isset($args['name'])) {
                $where = "name = '" . $args['name'] . "'";
                $mailitems = $object->getItems(array('where' => $where));
                if (empty($mailitems)) return 2;
        // Grab the first one that fits
                $mailitem = current($mailitems);
                $args['id'] = $mailitem['id'];
            }
            
            $where = "locale = '" . $recipientlocale . "' AND alias = " . $args['id'];
            $mailitems = $object->getItems(array('where' => $where));
            
            if (empty($mailitems)) {
        // If no message based on the alias use the ID
                $where = "id = " . $args['id'];
                $mailitems = $object->getItems(array('where' => $where));

        // Still no message? Bail
                if (empty($mailitems)) return 2;
            }

        // Grab the first one that fits
            $mailitem = current($mailitems);
            
        // Get the footer if this message has one
            $footer = "";
            if (!empty($mailitem['footer'])) {
                $object = DataObjectMaster::getObject(array('name' => 'mailer_footers'));
                $footeritemid = $object->getItem(array('itemid' => $mailitem['footer']));
                if (!empty($object->properties['body']->getValue())) $footer = $object->properties['body']->getValue();
            }
            
        // Check if there is a default redirect
            if (xarModItemVars::get('mailer','defaultredirect', xarMod::getID($module))) {
                $redirectaddress = xarModItemVars::get('mailer','defaultredirectaddress', xarMod::getID($module));
                if (empty($redirectaddress)) return 3;
                $recipientaddress = $redirectaddress;
            }

        // Check if there is a redirect in the message
            if ($mailitem['redirect']) {
                if (empty($mailitem['redirect_address'])) return 4;
                $recipientaddress = $mailitem['redirect_address'];
            }
            
        // Get the sender's data
            $sendername = isset($args['sendername']) ? $args['sendername'] : $mailitem['sender_name'];
            if (empty($sendername)) $sendername = xarModItemVars::get('mailer','defaultsendername', xarMod::getID($module));
            $senderaddress = isset($args['senderaddress']) ? $args['senderaddress'] : $mailitem['sender_address'];
            if (empty($senderaddress)) $senderaddress = xarModItemVars::get('mailer','defaultsenderaddress', xarMod::getID($module));
        
            $subject = $mailitem['subject'];
            $message = $mailitem['body'] . $footer;
            if (($mailitem['mail_type'] == 3) || ($mailitem['mail_type'] == 4)) {
                sys::import('blocklayout.compiler');
                $blCompiler = xarBLCompiler::instance();
                $data = isset($args['data']) ? $args['data'] : array();

                try {
                    $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
                    $tplString .= $subject;
                    $tplString .= '</xar:template>';
                    $subject = $blCompiler->compilestring($tplString);
                    $subject = xarTplString($subject,$data);
                    $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
                    $tplString .= $message;
                    $tplString .= '</xar:template>';
                    $message = $blCompiler->compilestring($tplString);
                    $message = xarTplString($message,$data);
                } catch (Exception $e) {
                    return 6;
                }
            }
            
        // Bundle the data into a nice array
            $args = array('info'         => $recipientaddress,
                          'name'         => $recipientname,
                          'subject'      => $subject,
                          'message'      => $message,
                          'htmlmessage'  => $message,
                          'from'         => $senderaddress,
                          'fromname'     => $sendername,
                          'attachName'   => '',
                          'attachPath'   => '',
                          'usetemplates' => false);

        // Pass it to the mail module for processing
        if (($mailitem['mail_type'] == 2) || ($mailitem['mail_type'] == 4)) {
            if (!xarModAPIFunc('mail','admin','sendhtmlmail', $args)) return 5;
        } else {
            if (!xarModAPIFunc('mail','admin','sendmail', $args)) return 5;
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

        return 0;
    }
?>
