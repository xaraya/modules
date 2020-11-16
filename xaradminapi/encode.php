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
 * Encode an OTP code array
 *
 */

function otp_adminapi_encode($args)
{
    // Checks
    if (!isset($args['code'])) {
        die(xarML('No proper code array was passed to the encode function'));
    }
    if (!is_array($args['code'])) {
        die(xarML('The code passed to the encode function is not an array'));
    }
    
    // The actual encoding
    $code = implode(':', $args['code']);
    $code = base64_encode($code);
    return $code;
}
