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
 * Main admin GUI function, entry point
 *
 */

function payments_admin_main()
{
    if (!xarSecurity::check('AdminPayments')) {
        return;
    }

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return [];
    } else {
        xarController::redirect(xarController::URL('payments', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
