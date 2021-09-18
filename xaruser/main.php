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
 * Main user GUI function, entry point
 *
 */

    function realms_user_main()
    {
        // Security Check
        if (!xarSecurity::check('ReadRealms')) {
            return;
        }

//        xarController::redirect(xarController::URL('realms', 'user', 'view'));
        // success
        return []; //true;
    }
