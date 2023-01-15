<?php
/**
 * Scheduler Module
 *
 * @package modules
 * @subpackage scheduler module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/189.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */

/**
 * Autoload function for this module's properties
 */
function scheduler_properties_autoload($class)
{
    $class = strtolower($class);

    $class_array = [
        'crontabproperty'               => 'modules.scheduler.xarproperties.crontab',
    ];

    if (isset($class_array[$class])) {
        sys::import($class_array[$class]);
        return true;
    }
    return false;
}

/**
 * Register this function for autoload on import
 */
if (class_exists('xarAutoload')) {
    xarAutoload::registerFunction('scheduler_properties_autoload');
} else {
    // guess you'll have to register it yourself :-)
}
