<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

/*
 * Clone a transaction
 */

function payments_user_clone_transaction()
{
    // Xaraya security
    if (!xarSecurity::check('AddPayments')) {
        return;
    }
    xarTpl::setPageTitle('Clone Transaction');

    if (!xarVar::fetch('itemid', 'isset', $itemid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('confirm', 'int', $confirm, 0, xarVar::DONT_SET)) {
        return;
    }

    if (empty($itemid)) {
        xarController::redirect(xarController::URL('payment', 'user', 'view_transactions'));
        return true;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $dateobject = DataPropertyMaster::getProperty(['name' => 'date']);
    $dateobject->checkInput('newdate');

    $data['object'] = DataObjectMaster::getObject(['name' => 'payments_transactions']);
    $data['object']->getItem(['itemid' => $itemid]);

    if ($confirm) {
        // Set up the $args to be passed to the clone object.
        $args = [
            'itemid' => 0,
            'transaction_date' => $dateobject->value,
            'created' => time(),
            'processed' => 0,
        ];

        // Add info for the log entry
        $script = implode('_', xarController::$request->getInfo());
        $args['script'] = $script;

        // Create the clone
        $cloneid = $data['object']->createItem($args);

        if (!empty($return_url)) {
            xarController::redirect($return_url);
        } else {
            xarController::redirect(xarController::URL('payments', 'user', 'modify_transaction', ['itemid' => $cloneid]));
        }
        return true;
    }
    return $data;
}
