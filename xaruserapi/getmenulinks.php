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
 * Return the options for the user menu
 *
 */

function otp_userapi_getmenulinks()
{
    $menulinks = array();

    if (xarSecurityCheck('ViewOtp', 0)) {
        $menulinks[] = array('url'   => xarModURL(
            'otp',
            'user',
            'main'
        ),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }

    return $menulinks;
}
