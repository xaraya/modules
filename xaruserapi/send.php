<?php
/**
 * Send an email
 *
 * @param  $id      OR          the ID of the message to be sent
 * @param  $name    OR          the name/designation of the message to be sent
 * @param  $subject             the message subject
 * @param  $message             the message body
 * @param  $mail_id             the id of the mail to be sent (from another module)
 * @param  $mail_type           the type of the mail to be sent (text, html)
 * @param  $role_id             the ID of the recipient
 * @param  $sendername
 * @param  $senderaddress
 * @param  $recipientname
 * @param  $recipientaddress
 * @param  $ccaddresses         format is an array with elements emailaddr => name
 * @param  $bccaddresses        format is an array with elements emailaddr => name
 * @param  $module              the module where the user object is defined
 * @param  $messagemodule       the module where the message to be sent is defined (for the history only)
 * @param  $data
 *
 * We can define a message by:
 * - its raw content
 * - its name
 * - its ID
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
    sys::import('modules.dynamicdata.class.objects.master');
    
    function mailer_userapi_send($args)
    {
        // The module(s) where our information iscoming from
        $module = isset($args['module']) ? $args['module'] : 'mailer';
        $messagemodule = isset($args['messagemodule']) ? $args['messagemodule'] : xarMod::getRegID('mailer');
        ;
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
            
            if (isset($args['message'])) {
        // We'll send the message passed directly
                $mailitems = array(array('body' => $args['message']));                
            } else {
                if (isset($args['name'])) {
            // Get the list of message aliases (translations of the same message)
                    $object = DataObjectMaster::getObjectList(array('name' => xarModItemVars::get('mailer','defaultmailobject', xarMod::getID($module))));
                    $where = "name = '" . $args['name'] . "'";
                    $mailitems = $object->getItems(array('where' => $where));
                    if (empty($mailitems)) return 2;
            // Grab the first one that fits
                    $mailitem = current($mailitems);
                    $args['id'] = $mailitem['id'];
                } else {
            // If no message or message name available, need an id
                    if (!isset($args['id'])) return 2;
                }
                // FIXME: sholdn't need to instantiate the object again
                $object = DataObjectMaster::getObjectList(array('name' => xarModItemVars::get('mailer','defaultmailobject', xarMod::getID($module))));
                $where = "locale = '" . $recipientlocale . "' AND alias = " . $args['id'];
                $mailitems = $object->getItems(array('where' => $where));
            }
            
            
            if (empty($mailitems)) {
        // If no message based on the alias use the ID
                $where = "id = " . $args['id'];
                $mailitems = $object->getItems(array('where' => $where));

        // Still no message? Bail
                if (empty($mailitems)) return 2;
            }

        // Grab the first one that fits
            $mailitem = current($mailitems);

        // Adjust the mail id if such a param was passed
            $mailid = !empty($args['mail_id']) ? $args['mail_id'] : 0;
            if (!empty($mailid)) $mailitem['id'] = $mailid;
            if (empty($mailitem['id'])) $mailitem['id'] = 0;            
            
        // Adjust the mail type if such a param was passed
        // Ensure we always have a value for this
            $mailtype = !empty($args['mail_type']) ? $args['mail_type'] : 0;
            if (!empty($mailtype)) $mailitem['mail_type'] = $mailtype;
            if (empty($mailitem['mail_type'])) $mailitem['mail_type'] = 1;            
            
        // Get the header if this message has one
            $header = "";
            if (isset($mailitem['header']) && !empty($mailitem['header'])) {
                $object = DataObjectMaster::getObject(array('name' => 'mailer_headers'));
                $headeritemid = $object->getItem(array('itemid' => $mailitem['header']));
                $header = $object->properties['body']->getValue();
            }
            
        // Get the footer if this message has one
            $footer = "";
            if (!empty($mailitem['footer'])) {
                $object = DataObjectMaster::getObject(array('name' => 'mailer_footers'));
                $footeritemid = $object->getItem(array('itemid' => $mailitem['footer']));
                $footer = $object->properties['body']->getValue();
            }
        // Set redirect information. 
            $redirectsending = xarModItemVars::get('mailer','defaultredirect', xarMod::getID($module));
            $redirectaddress = xarModItemVars::get('mailer','defaultredirectaddress', xarMod::getID($module));
            
        // Check if there is a default redirect
            if ($redirectsending) {
                if (empty($redirectaddress)) return 3;
                $recipientaddress = $redirectaddress;
            }

        // Check if there is a redirect in the message
            if (!empty($mailitem['redirect'])) {
                if (empty($mailitem['redirect_address'])) return 4;
                $recipientaddress = $mailitem['redirect_address'];
                $redirectsending = $mailitem['redirect'];
                $redirectaddress = $mailitem['redirect_address'];
            }
            
        // Get the sender's data
            $sendername = isset($mailitem['sender_name']) ? $mailitem['sender_name'] : '';
            $sendername = isset($args['sendername']) ? $args['sendername'] : $sendername;
            if (empty($sendername)) $sendername = xarModItemVars::get('mailer','defaultsendername', xarMod::getID($module));
            $senderaddress = isset($mailitem['sender_address']) ? $mailitem['sender_address'] : '';
            $senderaddress = isset($args['senderaddress']) ? $args['senderaddress'] : $senderaddress;
            if (empty($senderaddress)) $senderaddress = xarModItemVars::get('mailer','defaultsenderaddress', xarMod::getID($module));
        
            if (($mailitem['mail_type'] == 1) || ($mailitem['mail_type'] == 2)) {
                
                $data = isset($args['data']) ? $args['data'] : array();

                foreach ($data as $key => $value) {
                    $placeholder = '/%%' . $key . '%%/';
                    $mailitem['body'] = preg_replace($placeholder,
                                                     $value,
                                                     $mailitem['body']);
                }
            } 

            $subject = !empty($mailitem['subject']) ? $mailitem['subject'] : '';
            $subject = isset($args['subject']) ? $args['subject'] : $subject;
            $message = $header . $mailitem['body'] . $footer;

            if (($mailitem['mail_type'] == 3) || ($mailitem['mail_type'] == 4)) {
                sys::import('xaraya.templating.compiler');
                $blCompiler = XarayaCompiler::instance();
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
            
        // Take care of other data passed through directly
            $ccaddresses = isset($args['ccaddresses']) ? $args['ccaddresses'] : '';
            $bccaddresses = isset($args['bccaddresses']) ? $args['bccaddresses'] : '';
            $custom_header = isset($args['custom_header']) ? $args['custom_header'] : array();
            $message_envelope = isset($args['message_envelope'])? $args['message_envelope'] : "";
            
        // Bundle the data into a nice array
            $args = array('info'             => $recipientaddress,
                          'name'             => $recipientname,
                          'ccrecipients'     => $ccaddresses,
                          'bccrecipients'    => $bccaddresses,
                          'subject'          => $subject,
                          'message'          => $message,
                          'htmlmessage'      => $message,
                          'from'             => $senderaddress,
                          'fromname'         => $sendername,
                          'attachName'       => '',
                          'attachPath'       => '',
                          'redirectsending'  => $redirectsending,
                          'redirectaddress'  => $redirectaddress,
                          'usetemplates'     => false,
                          'custom_header'    => $custom_header,
                          'message_envelope' => $message_envelope
            );

        // Pass it to the mail module for processing
        if (!empty($mailitem['mail_type']) && (($mailitem['mail_type'] == 2) || ($mailitem['mail_type'] == 4))) {
            if (!xarModAPIFunc('mail','admin','sendhtmlmail', $args)) return 5;
        } else {
            if (!xarModAPIFunc('mail','admin','sendmail', $args)) return 5;
        }
        
        // Check we want to save this message and if so do it
            if (xarModItemVars::get('mailer','savetodb', xarMod::getID($module))) {
                $object = DataObjectMaster::getObject(array('name' => 'mailer_history'));
                $args = array(
                            'mail_id' => $mailitem['id'],
                            'module' => $messagemodule,
                            'sender_name' => $sendername,
                            'sender_address' => $senderaddress,
                            'recipient_name' => $recipientname,
                            'recipient_address' => $recipientaddress,
                            'body' => $message,
                            'subject' => $subject,
                            );
                $item = $object->createItem($args);
            }

        return 0;
    }
?>