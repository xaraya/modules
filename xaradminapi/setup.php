<?php
/**
 * Otp Module
 *
 * @package modules
 * @subpackage otp
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2017 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Generate the password array
 *
 */

    function otp_adminapi_setup($args)
    {
        sys::import('modules.otp.xarincludes.php-otp.Otp');
        $otp = new Otp();
        $result = $otp->initializeOtp($args['passphrase'], $args['seed'], $args['seq_number'], $args['algorithm']);
        return $result;
    }
