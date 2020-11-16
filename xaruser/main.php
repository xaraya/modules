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
 * Main user GUI function, entry point
 *
 */

    function karma_user_main()
    {
        // Security Check
        if (!xarSecurity::check('ReadKarma')) {
            return;
        }

//        xarResponse::redirect(xarController::URL('karma', 'user', 'view'));
        // success
        return array(); //true;
    }
