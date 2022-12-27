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
 * Authenticate a password
 *
 */

function otp_adminapi_authenticate($args)
{
    sys::import('modules.otp.xarincludes.php-otp.Otp');
    $otp = new Otp();
    $result = $otp->authAgainstHexOtp($userInput, $masterHexOtp, $masterHexOtpType, $sequence, $algorithm);
    initializeOtp($args['passphrase'], $args['seed'], $args['seq_number'], $args['algorithm']);

    // Debug code
    if (xarModVars::get('otp', 'debugmode') &&
    in_array(xarUser::getVar('id'), xarConfigVars::get(null, 'Site.User.DebugAdmins'))) {
        echo "Authentication result: ";
        var_dump($result);
        echo "<br />";
    }
    return $result;
}
