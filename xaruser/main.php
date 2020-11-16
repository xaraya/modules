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
 * Main user GUI function, entry point
 *
 */

    function eav_user_main()
    {
        // Security Check
        if (!xarSecurityCheck('ReadEAV')) {
            return;
        }

//        xarController::redirect(xarModURL('eav', 'user', 'view'));
        // success
        return array(); //true;
    }
