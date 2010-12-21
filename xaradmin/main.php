<?php
/**
 * Mailer Module
 *
 * @package modules
 * @subpackage mailer module
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main admin GUI function, entry point
 *
 */

    function mailer_admin_main()
    {
        if(!xarSecurityCheck('ManageMailer')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return array();
        } else {
            xarController::redirect(xarModURL('mailer', 'admin', 'view'));
        }
        // success
        return true;
    }
?>