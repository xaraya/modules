<?php
/**
 * Regenerate static pages from script
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */

/**
 * Load the layout file so we know where to find the Xaraya directories
 */
$systemConfiguration = [];
include 'var/layout.system.php';
if (!isset($systemConfiguration['rootDir'])) {
    $systemConfiguration['rootDir'] = '../';
}
if (!isset($systemConfiguration['libDir'])) {
    $systemConfiguration['libDir'] = 'lib/';
}
if (!isset($systemConfiguration['webDir'])) {
    $systemConfiguration['webDir'] = 'html/';
}
if (!isset($systemConfiguration['codeDir'])) {
    $systemConfiguration['codeDir'] = 'code/';
}
$GLOBALS['systemConfiguration'] = $systemConfiguration;
set_include_path($systemConfiguration['rootDir'] . PATH_SEPARATOR . get_include_path());

/**
 * Load the Xaraya bootstrap so we can get started
 */
include 'bootstrap.php';

/**
 * Set up output caching if enabled
 * Note: this happens first so we can serve cached pages to first-time visitors
 *       without loading the core
 */
sys::import('xaraya.caching');
// Note : we may already exit here if session-less page caching is enabled
xarCache::init();

/**
 * Load the Xaraya core
 */
sys::import('xaraya.core');

// Load the core with all optional systems loaded
xarCore::xarInit(xarCore::SYSTEM_ALL);

sys::import('modules.xarcachemanager.class.hooks');
use Xaraya\Modules\CacheManager\CacheHooks;

$result = CacheHooks::regenstatic();
if (empty($result)) {
    $result = "Done";
} elseif (is_array($result)) {
    $result = implode("\n", $result);
}
echo $result;
echo "\n";
