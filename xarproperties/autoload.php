<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @author mikespub <mikespub@xaraya.com>
 */

/**
 * Autoload function for this module's properties
 */
function calendar_properties_autoload($class)
{
    $class = strtolower($class);

    $class_array = array(
        'calendardisplayproperty'       => 'modules.calendar.xarproperties.calendardisplay',
    );
    
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
    xarAutoload::registerFunction('calendar_properties_autoload');
} else {
    // guess you'll have to register it yourself :-)
}
?>