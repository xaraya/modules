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
 * Main user GUI function, entry point
 *
 */

    function mailer_user_main()
    {
        // Security Check
        if (!xarSecurityCheck('ReadMailer')) return;

//        xarResponse::redirect(xarModURL('mailer', 'user', 'view'));
        // success
        return array(); //true;
    }

?>
