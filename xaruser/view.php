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
 * View items of the payments object
 *
 */
    function payments_user_view($args)
    {
        // Data Managers have access
        if (!xarSecurity::check('ProcessPayments') || !xarUser::isLoggedIn()) {
            return;
        }

        // Set a return url
        xarSession::setVar('ddcontext.' . 'payments', array('return_url' => xarServer::getCurrentURL()));

        return array();
    }
