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

function reminders_admin_delete_entry()
{
    if (!xarSecurityCheck('ManageReminders')) return;

    if (!xarVarFetch('name',       'str:1',     $name,            'reminders_entries',     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemid' ,    'int',       $data['itemid'] , '' ,          XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',    'checkbox',  $data['confirm'], false,     XARVAR_NOT_REQUIRED)) return;

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));

    $data['tplmodule'] = 'reminders';
    $data['authid'] = xarSecGenAuthKey('reminders');

    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        // Delete the item
        $item = $data['object']->deleteItem();
            
        // Jump to the next page
        xarController::redirect(xarModURL('reminders','admin','view_entries'));
        return true;
    }
    return $data;
}

?>