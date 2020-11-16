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
 * Get the information of the requesting device
 *
 */
function wurfl_userapi_get_device($args)
{
    sys::import('modules.wurfl.wurfl_init');
    $wurflManager = wurfl_init($args);
    if (empty($args['ua'])) {
        $requestingDevice = $wurflManager->getDeviceForHttpRequest($_SERVER);
    } else {
        $requestingDevice = $wurflManager->getDeviceForUserAgent($args['ua']);
    }
    return $requestingDevice;

    $capabilities = xarSession::getVar(wurfl_requesting_device);
    if (empty($capabilities)) {
        sys::import('modules.wurfl.wurfl_config_standard');
        $requestingDevice = $wurflManager->getDeviceForUserAgent($_SERVER);
        $capabilities = $requestingDevice->getCapability;
        xarSession::getVar(wurfl_requesting_device, $capabilities);
    }
    return $requestingDevice;
}
