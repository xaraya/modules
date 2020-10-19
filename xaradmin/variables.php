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
/**
 * configure variable caching
 * @return array
 */
function xarcachemanager_admin_variables($args)
{
    extract($args);

    if (!xarSecurityCheck('AdminXarCache')) { return; }

    $data = array();
    if (!xarCache::$variableCacheIsEnabled) {
        $data['variables'] = array();
        return $data;
    }

    xarVarFetch('submit','str',$submit,'');
    if (!empty($submit)) {
        // Confirm authorisation code
        if (!xarSecConfirmAuthKey()) return;

        xarVarFetch('docache','isset',$docache,array());
        xarVarFetch('cacheexpire','isset',$cacheexpire,array());

        $newvariables = array();
        // loop over something that should return values for every variable
        foreach ($cacheexpire as $name => $expire) {
            $newvariables[$name] = array();
            $newvariables[$name]['name'] = $name;
            // flip from docache in template to nocache in settings
            if (!empty($docache[$name])) {
                $newvariables[$name]['nocache'] = 0;
            } else {
                $newvariables[$name]['nocache'] = 1;
            }
            if (!empty($expire)) {
                $expire = xarMod::apiFunc('xarcachemanager', 'admin', 'convertseconds',
                                          array('starttime' => $expire,
                                                'direction' => 'to'));
            } elseif ($expire === '0') {
                $expire = 0;
            } else {
                $expire = NULL;
            }
            $newvariables[$name]['cacheexpire'] = $expire;
        }
        // save settings to dynamicdata in case xarcachemanager is removed later
        xarModVars::set('dynamicdata','variablecache_settings', serialize($newvariables));

        // variables could be anywhere, we're not smart enough not know exactly where yet
        $key = '';
        // and flush the variables
        xarVariableCache::flushCached($key);
    }

    // Get all variable caching configurations
    $data['variables'] = xarModAPIfunc('xarcachemanager', 'admin', 'getvariables');

    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>
