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
    if (!xarSecurityCheck('ManageReminders')) {
        return;
    }
    
    if (!xarVarFetch('confirm', 'checkbox', $data['confirm'], false, XARVAR_NOT_REQUIRED)) {
        return;
    }
    
    if ($data['confirm']) {
        
        // Check for a valid confirmation key
        if (!xarSecConfirmAuthKey()) {
            return;
        }

        // Check if we get a copy of the email(s)
        $checkbox = DataPropertyMaster::getProperty(array('name' => 'checkbox'));
        $checkbox->checkInput('copy_emails');
        $bccaddress = $checkbox->value ? array(xarUser::getVar('email')) : array();
   
        $data['results'] = xarMod::apiFunc('reminders', 'admin', 'process', array('test' => true, 'copy_emails' => $bccaddress));
        $data['test'] = true;
    }
    
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => 'reminders_entries'));
    $data['object']->dataquery->eq('state', 3);

    return $data;
}
