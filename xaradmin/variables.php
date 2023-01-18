<?php
/**
 * Config variable caching
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.utility');
use Xaraya\Modules\CacheManager\CacheUtility;

/**
 * configure variable caching
 * @return array
 */
function xarcachemanager_admin_variables($args)
{
    extract($args);

    if (!xarSecurity::check('AdminXarCache')) {
        return;
    }

    $data = [];
    if (!xarCache::$variableCacheIsEnabled) {
        $data['variables'] = [];
        return $data;
    }

    xarVar::fetch('reset', 'str', $reset, '');
    if (!empty($reset)) {
        // Confirm authorisation code
        if (!xarSec::confirmAuthKey()) {
            return;
        }
        xarConfigVars::delete(null, 'Site.Variable.CacheSettings');
        xarModVars::delete('dynamicdata', 'variablecache_settings');
    }

    xarVar::fetch('submit', 'str', $submit, '');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSec::confirmAuthKey()) {
            return;
        }

        xarVar::fetch('docache', 'isset', $docache, []);
        xarVar::fetch('cacheexpire', 'isset', $cacheexpire, []);

        $newvariables = [];
        // loop over something that should return values for every variable
        foreach ($cacheexpire as $name => $expire) {
            $newvariables[$name] = [];
            $newvariables[$name]['name'] = $name;
            // flip from docache in template to nocache in settings
            if (!empty($docache[$name])) {
                $newvariables[$name]['nocache'] = 0;
            } else {
                $newvariables[$name]['nocache'] = 1;
            }
            if (!empty($expire)) {
                $expire = CacheUtility::convertToSeconds($expire);
            } elseif ($expire === '0') {
                $expire = 0;
            } else {
                $expire = null;
            }
            $newvariables[$name]['cacheexpire'] = $expire;
        }
        // save settings to dynamicdata in case xarcachemanager is removed later
        xarModVars::set('dynamicdata', 'variablecache_settings', serialize($newvariables));

        // variables could be anywhere, we're not smart enough not know exactly where yet
        $key = '';
        // and flush the variables
        xarVariableCache::flushCached($key);
    }

    // Get all variable caching configurations
    $data['variables'] = xarMod::apiFunc('xarcachemanager', 'admin', 'getvariables');

    $data['authid'] = xarSec::genAuthKey();
    return $data;
}
