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
/**
 * Test the reminders process
 *
 */

function reminders_admin_test()
{
    if (!xarSecurityCheck('ManageReminders')) return;
    
    if (!xarVarFetch('message_id', 'int',      $data['message_id'], 0,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'checkbox', $data['confirm'],    false, XARVAR_NOT_REQUIRED)) return;
    if ($data['confirm']) {
        
        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        //Assemble the parameters for the email
        $params['message_id']   = $data['message_id'];
        $params['message_body'] = $data['message_body'];
        $params['subject']      = $data['subject'];

        // Check if we get a copy of the email(s)
        $checkbox = DataPropertyMaster::getProperty(array('name' => 'checkbox'));
        $checkbox->checkInput('copy_emails');
        $bccaddress = $checkbox->value ? array(xarUser::getVar('email')) : array();
   
        xarMod::apiFunc('reminders', 'admin', 'process', array('test' => true, 'params' => $params, 'copy_emails' => $bccaddress));
    }
    
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => 'reminders_entries'));
    $data['object']->dataquery->eq('state', 3);

    // Get the available email messages
    $data['message_options'] = xarMod::apiFunc('mailer' , 'user' , 'getall_mails', array('state' => 3, 'module' => "reminders"));

    return $data;
}
?>