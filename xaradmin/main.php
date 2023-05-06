<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main admin GUI function, entry point
 *
 */

    function eav_admin_main()
    {
        if(!xarSecurity::check('ManageEAV')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return array();
        } else {
            xarController::redirect(xarController::URL('eav', 'admin', 'modifyconfig'));
        }
        // success
        return true;
    }
?>