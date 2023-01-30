<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */

namespace Xaraya\Modules\Workflow;

use xarAutoload;
use sys;

/**
 * Autoload function for this module's classes - using namespace here
 */
function autoload($class)
{
    $class = strtolower($class);
    $namespace = strtolower(__NAMESPACE__) . '\\';
    if (strpos($class, $namespace) !== 0) {
        return false;
    }
    $class = str_replace($namespace, '', $class);

    $class_array = [
        'workflowsproperty'     => 'modules.workflow.xarproperties.workflows',
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
    xarAutoload::registerFunction(__NAMESPACE__ . '\autoload');
} else {
    // guess you'll have to register it yourself :-)
}
