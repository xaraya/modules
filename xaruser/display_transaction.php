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
/**
 * Display an item of the payments_transaction object
 *
 */

function payments_user_display_transaction()
{
    if (!xarSecurity::check('EditPayments')) {
        return;
    }

    if (!xarVar::fetch('name', 'str', $name, 'payments_transactions', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'int', $data['itemid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(['name' => $name]);
    $data['object']->getItem(['itemid' => $data['itemid']]);

    $data['tplmodule'] = 'payments';

    return $data;
}
