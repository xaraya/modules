<?php
/**
 * Logconfig initialization functions
 *
 * @package modules
 * @subpackage logconfig
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2022 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Gets the log levels Xaraya recognizes
 */
function logconfig_adminapi_get_levels()
{
    sys::import('xaraya.log.loggers.xarLogger');
    $logger = new xarLogger();
    $levels = $logger->levels;

    return $levels;
}

?>