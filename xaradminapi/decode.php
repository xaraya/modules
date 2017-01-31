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
 * Decode an OTP code array
 *
 */

function otp_adminapi_decode($args)
{
    // Checks
    if (!isset($args['code']))
        die(xarML('No proper code was passed to the decode function'));
        
    // The actual decoding
    $code = base64_decode($args['code']);
    $code = explode(':', $code);

    // More checks
    if (!is_array($code))
        die(xarML('The code passed to the decode function does not decode to an array'));
    
    return $code;
}
?>