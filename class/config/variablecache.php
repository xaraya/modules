<?php
/**
 * Classes to manage config for the cache system of Xaraya
 *
 * @package modules\xarcachemanager
 * @subpackage xarcachemanager
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.info/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
**/

namespace Xaraya\Modules\CacheManager\Config;

use xarSecurity;
use xarCache;
use xarVar;
use xarSec;
use xarConfigVars;
use xarModVars;
use xarMod;
use xarVariableCache;
use sys;

sys::import('modules.xarcachemanager.class.config');
sys::import('modules.xarcachemanager.class.utility');
use Xaraya\Modules\CacheManager\CacheConfig;
use Xaraya\Modules\CacheManager\CacheUtility;

class VariableCache extends CacheConfig
{
    public static function init(array $args = [])
    {
    }

    /**
     * configure variable caching
     * @return array|void
     */
    public static function modifyConfig($args)
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
        $data['variables'] = static::getConfig();

        $data['authid'] = xarSec::genAuthKey();
        return $data;
    }

    /**
     * get configuration of variable caching for all variables
     *
     * @return array variable caching configurations
     */
    public static function getConfig()
    {
        // Get all variable cache settings
        $variablesettings = [];
        $serialsettings = xarModVars::get('dynamicdata', 'variablecache_settings');
        if (!empty($serialsettings)) {
            $variablesettings = unserialize($serialsettings);
        }

        // Get all variables
        //$variables = xarMod::apiFunc('dynamicdata', 'user', 'getvariables');
        $variables = array_keys(xarVariableCache::getCacheSettings());

        $variableconfig = [];
        foreach ($variables as $name) {
            $settings = [];
            $settings['name'] = $name;
            if (isset($variablesettings[$name])) {
                $settings = $variablesettings[$name];
                if ($settings['cacheexpire'] > 0) {
                    $settings['cacheexpire'] = CacheUtility::convertFromSeconds($settings['cacheexpire']);
                } else {
                    $settings['cacheexpire'] = '';
                }
            } else {
                $settings['name'] = $name;
                // flip from docache in config to nocache in settings
                $settings['nocache'] = 1;
                $settings['cacheexpire'] = '';
            }
            $variableconfig[$name] = $settings;
        }
        return $variableconfig;
    }
}
