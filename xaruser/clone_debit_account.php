<?php
/**
 * LedgerAR Module
 *
 * @package ledger
 * @subpackage ledgerar module
 * @category Third Party Xaraya Module
 * @version 0.9.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
function payments_user_clone_debit_account()
{
    // Xaraya security
    if (!xarSecurityCheck('AddPayments')) {
        return;
    }
    xarTplSetPageTitle('Clone Debit Account');

    if (!xarVarFetch('itemid', 'isset', $itemid, null, XARVAR_DONT_SET)) {
        return;
    }
    if (!xarVarFetch('newname', 'str', $newname, "", XARVAR_NOT_REQUIRED)) {
        return;
    }
    if (!xarVarFetch('confirm', 'int', $confirm, 0, XARVAR_DONT_SET)) {
        return;
    }
    
    if (empty($itemid)) {
        xarController::redirect(xarModURL('ledgerar', 'user', 'view_debit_accounts'));
        return true;
    }

    $data['object'] = DataObjectMaster::getObject(array('name' => 'payments_debit_account'));
    $data['object']->getItem(array('itemid' => $itemid));
    
    if ($confirm) {
        // Get the name for the clone
        if (empty($newname)) {
            $newname = $object->properties['name']->value . "_copy";
        }
        $newname = str_ireplace(" ", "_", $newname);
            
        // Check if this object already exists
        $testobject = DataObjectMaster::getObjectList(array('name' => 'payments_debit_account'));
        $items = $testobject->getItems(array('where' => "name = '" . $newname . "'"));
        if (count($items)) {
            return xarTplModule('payments', 'user', 'errors', array('layout' => 'duplicate_account_name', 'newname' => $newname));
        }
        
        // Create the clone
        $data['object']->properties['name']->setValue($newname);
        $cloneid = $data['object']->createItem(array('itemid' => 0));

        if (!empty($return_url)) {
            xarController::redirect($return_url);
        } else {
            xarController::redirect(xarModURL('payments', 'user', 'modify_debit_account'));
        }
    }
    return $data;
}
