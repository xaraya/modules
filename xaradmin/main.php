<?php
/**
 * Wurfl Module
 *
 * @package modules
 * @subpackage wurfl module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main admin GUI function, entry point
 *
 */

    function wurfl_admin_main()
    {
        if(!xarSecurityCheck('ManageWurfl')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return array();
        } else {
            xarController::redirect(xarModURL('wurfl', 'admin', 'modifyconfig'));
        }
        // success
        return true;
    }
?>