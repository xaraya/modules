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
 *
 * Table information
 *
 */

    function otp_xartables()
    {
        // Initialise table array
        $xartable = [];

        $xartable['otp_used_seeds']    = xarDB::getPrefix() . '_otp_used_seeds';
        $xartable['otp_otps']          = xarDB::getPrefix() . '_otp_otps';

        // Return the table information
        return $xartable;
    }
