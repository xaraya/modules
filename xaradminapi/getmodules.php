<?php
/**
 * Get configuration of module caching for modules
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.cache_manager');

/**
 * get configuration of module caching for all modules
 *
 * @return array module caching configurations
 */
function xarcachemanager_adminapi_getmodules($args)
{
    extract($args);

    // Get all module cache settings
    $modulesettings = [];
    $serialsettings = xarModVars::get('modules', 'modulecache_settings');
    if (!empty($serialsettings)) {
        $modulesettings = unserialize($serialsettings);
    }

    // Get default module functions to cache
    $defaultmodulefunctions = unserialize(xarModVars::get('xarcachemanager', 'DefaultModuleCacheFunctions'));

    // Get all modules
    $modules = xarMod::apiFunc('modules', 'admin', 'getlist');

    // Get all module functions (user GUI)
    $modulefunctions = [];
    foreach ($modules as $module) {
        $name = $module['name'];
        $modulefunctions[$name] = [];
        // find the caching configuration from the mapper file (if it exists)
        $mapperfile = realpath(sys::code() . 'modules/' . $module['directory'] . '/xarmapper.php');
        if ($mapperfile) {
            include $mapperfile;
            $funcname = $name . '_xarmapper';
            if (function_exists($funcname)) {
                $xarmapper = $funcname();
                if (!empty($xarmapper['user'])) {
                    // set the module functions
                    $modulefunctions[$name] = array_keys($xarmapper['user']);
                    // initialize the module settings from the mapper file if necessary
                    foreach ($modulefunctions[$name] as $func) {
                        if (empty($modulesettings[$name][$func]) && !empty($xarmapper['user'][$func])) {
                            $modulesettings[$name][$func] = $xarmapper['user'][$func];
                            // flip from docache in config to nocache in settings
                            if (!empty($defaultmodulefunctions[$func])) {
                                $modulesettings[$name][$func]['nocache'] = 0;
                            } else {
                                $modulesettings[$name][$func]['nocache'] = 1;
                            }
                        }
                    }
                }
            }
            continue;
        }
        // find the user GUI functions
        $userdir = realpath(sys::code() . 'modules/' . $module['directory'] . '/xaruser/');
        if ($userdir && $dh = @opendir($userdir)) {
            while (($filename = @readdir($dh)) !== false) {
                // Got a file or directory.
                $thisfile = $userdir . '/' . $filename;
                if (is_file($thisfile) && preg_match('/^(.+)\.php$/', $filename, $matches)) {
                    $func = $matches[1];
                    // add this module function to the list
                    $modulefunctions[$name][] = $func;
                    // initialize the module settings from the function file if necessary
                    if (empty($modulesettings[$name][$func])) {
                        $settings = [];
                        // try to find all xarVar::fetch() parameters in this function
                        $params = [];
                        $content = implode('', file($thisfile));
                        if (preg_match_all("/xarVar::fetch\(\s*'([^']+)'/", $content, $params)) {
                            // add the parameters discovered in the function file
                            $settings['params'] = implode(',', $params[1]);
                        } elseif (preg_match('/\w+hook$/', $func)) {
                            // default hook parameters
                            $settings['params'] = 'objectid,extrainfo';
                        } else {
                            $settings['params'] = '';
                        }
                        // flip from docache in config to nocache in settings
                        if (!empty($defaultmodulefunctions[$func])) {
                            $settings['nocache'] = 0;
                        } else {
                            $settings['nocache'] = 1;
                        }
                        $settings['usershared'] = 1;
                        $settings['cacheexpire'] = '';
                        $modulesettings[$name][$func] = $settings;
                    }
                }
            }
            closedir($dh);
        }
    }

    $moduleconfig = [];
    foreach ($modules as $module) {
        // use the module name as key for easy lookup in xarModuleCache
        $name = $module['name'];
        // TODO: filter on something else ?
        if ($name == 'authsystem' ||
            $name == 'roles' ||
            $name == 'privileges') {
            continue;
        }
        $moduleconfig[$name] = $module;
        $moduleconfig[$name]['cachesettings'] = [];
        if (isset($modulesettings[$name])) {
            foreach ($modulesettings[$name] as $func => $settings) {
                if ($settings['cacheexpire'] > 0) {
                    $settings['cacheexpire'] = xarMod::apiFunc(
                        'xarcachemanager',
                        'admin',
                        'convertseconds',
                        ['starttime' => $settings['cacheexpire'],
                                                                     'direction' => 'from', ]
                    );
                }
                $moduleconfig[$name]['cachesettings'][$func] = $settings;
            }
        }
        foreach ($modulefunctions[$name] as $func) {
            if (isset($moduleconfig[$name]['cachesettings'][$func])) {
                continue;
            }
            $settings = [];
            if (preg_match('/\w+hook$/', $func)) {
                // default hook parameters
                $settings['params'] = 'objectid,extrainfo';
            } else {
                $settings['params'] = '';
            }
            // flip from docache in config to nocache in settings
            if (!empty($defaultmodulefunctions[$func])) {
                $settings['nocache'] = 0;
            } else {
                $settings['nocache'] = 1;
            }
            $settings['usershared'] = 1;
            $settings['cacheexpire'] = '';
            $moduleconfig[$name]['cachesettings'][$func] = $settings;
        }
    }
    return $moduleconfig;
}
