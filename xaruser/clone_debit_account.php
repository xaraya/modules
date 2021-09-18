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
    if (!xarSecurity::check('AddPayments')) {
        return;
    }
    xarTpl::setPageTitle('Clone Debit Account');

    if (!xarVar::fetch('itemid', 'isset', $itemid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('newname', 'str', $newname, "", xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'int', $confirm, 0, xarVar::DONT_SET)) {
        return;
    }

    if (empty($itemid)) {
        xarController::redirect(xarController::URL('ledgerar', 'user', 'view_debit_accounts'));
        return true;
    }

    $data['object'] = DataObjectMaster::getObject(['name' => 'payments_debit_account']);
    $data['object']->getItem(['itemid' => $itemid]);

    if ($confirm) {
        // Get the name for the clone
        if (empty($newname)) {
            $newname = $object->properties['name']->value . "_copy";
        }
        $newname = str_ireplace(" ", "_", $newname);

        // Check if this object already exists
        $testobject = DataObjectMaster::getObjectList(['name' => 'payments_debit_account']);
        $items = $testobject->getItems(['where' => "name = '" . $newname . "'"]);
        if (count($items)) {
            return xarTpl::module('payments', 'user', 'errors', ['layout' => 'duplicate_account_name', 'newname' => $newname]);
        }

        // Create the clone
        $data['object']->properties['name']->setValue($newname);
        $cloneid = $data['object']->createItem(['itemid' => 0]);

        if (!empty($return_url)) {
            xarController::redirect($return_url);
        } else {
            xarController::redirect(xarController::URL('payments', 'user', 'modify_debit_account'));
        }
    }
    return $data;
}
