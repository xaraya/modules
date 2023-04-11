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
 * Create a new item of the payments object
 *
 */
    function payments_admin_new()
    {
        if (!xarSecurity::check('AddPayments')) return;

        $data['object'] = xarMod::apiFunc('dynamicdata','user','getobjectlist', array('name' => 'payments'));
        $data['tplmodule'] = 'payments';
        return $data;
    }

?>