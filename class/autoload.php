<?php
/**
 * xarCacheManager Module
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */

namespace Xaraya\Modules\CacheManager;

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
        'cachemanager'    => 'modules.xarcachemanager.class.manager',
        'cacheadmin'      => 'modules.xarcachemanager.class.admin',
        'cachehooks'      => 'modules.xarcachemanager.class.hooks',
        'cachescheduler'  => 'modules.xarcachemanager.class.scheduler',
    ];

    if (isset($class_array[$class])) {
        sys::import($class_array[$class]);
        return true;
    }

    return false;
}

/**
 * Register this function for autoload on import - using namespace here
 */
if (class_exists('xarAutoload')) {
    xarAutoload::registerFunction(__NAMESPACE__ . '\autoload');
} else {
    // guess you'll have to register it yourself :-)
}
