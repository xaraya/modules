<?php
/**
 * Foo Module
 *
 * @package modules
 * @subpackage foo module
 * @copyright (C) 2011 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Main admin GUI function, entry point
 *
 */

    function foo_admin_main()
    {
        if(!xarSecurityCheck('AdminFoo')) return;

        if (xarModVars::get('modules', 'disableoverview') == 0) {
            return array();
        } else {
            xarController::redirect(xarModURL('foo', 'admin', 'modifyconfig'));
        }
        // success
        return true;
    }
?>