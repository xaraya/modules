<?php
/**
 * Karma Module
 *
 * @package modules
 * @subpackage karma
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main admin GUI function, entry point
 *
 */

    function karma_admin_main()
    {
        if(!xarSecurityCheck('AdminKarma')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return array();
        } else {
            xarResponse::redirect(xarModURL('karma', 'admin', 'modifyconfig'));
        }
        // success
        return true;
    }
?>