<?php
/**
 * Wurfl Module
 *
 * @package modules
 * @subpackage wurfl module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Check a user agent identiication against the requesting device
 *
 */
function wurfl_userapi_check_device($args=[])
{
    if (empty($args['agent'])) {
        $args['agent'] = 'generic';
    }
    if (empty($args['mode'])) {
        $args['mode'] = 'performance';
    }
    $requestingDevice = xarMod::apiFunc('wurfl', 'user', 'get_device', ['mode' => $args['mode']]);
    $device_id = $requestingDevice->id;
    $check = preg_match("/".$args['agent']."/i", $device_id);
    return $check;
}
