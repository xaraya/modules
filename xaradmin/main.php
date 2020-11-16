<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Main admin GUI function, entry point
 *
 */

    function realms_admin_main()
    {
        if (!xarSecurityCheck('AdminRealms')) {
            return;
        }

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return array();
        } else {
            xarController::redirect(xarModURL('realms', 'admin', 'modifyconfig'));
        }
        // success
        return true;
    }
