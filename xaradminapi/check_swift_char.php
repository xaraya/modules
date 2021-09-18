<?php
/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Check a string for valid SWIFT character set
 * @param  $ 'string' is the string to validate
 * Return the original string passed, or false if not a valid string
 */

function payments_adminapi_check_swift_char($args)
{
    if (!isset($args['string'])) {
        throw new Exception(xarML('Missing string parameter'));
    }

    preg_match("%([A-Za-z0-9]|[+|\?|/|\-|:|\(|\)|\.|,|'|\p{Zs}])*%", $args['string'], $matches);

    if ($matches[0] == $args['string']) {
        return $args['string'];
    }
    return false;
}
