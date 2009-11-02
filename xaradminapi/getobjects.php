<?php
/**
 * Get configuration of object caching for objects
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * get configuration of object caching for all objects
 *
 * @return array object caching configurations
 */
function xarcachemanager_adminapi_getobjects($args)
{
    extract($args);

    // Get all object cache settings
    $objectsettings = array();
    $serialsettings = xarModVars::get('dynamicdata','objectcache_settings');
    if (!empty($serialsettings)) {
        $objectsettings = unserialize($serialsettings);
    }

    // Get all objects
    $objects = xarMod::apiFunc('dynamicdata', 'user', 'getobjects');

    foreach ($objects as $key => $object) {
// TODO: filter on visibility, dummy datastores etc.
        if ($object['objectid'] < 4) {
            $object['nocache'] = 1;
            $objects[$key]['nocache'] = 1;
        }
        if (isset($objectsettings[$key])) {
            $object = $objectsettings[$key];
            $objects[$key]['nocache'] = $objectsettings[$key]['nocache'];
            $objects[$key]['displaycache'] = $objectsettings[$key]['displaycache'];
            $objects[$key]['usershared'] = $objectsettings[$key]['usershared'];
            if ($objectsettings[$key]['cacheexpire'] > 0) {
                $objects[$key]['cacheexpire'] = xarMod::apiFunc('xarcachemanager', 'admin', 'convertseconds',
                                                                array('starttime' => $objectsettings[$key]['cacheexpire'],
                                                                      'direction' => 'from'));
            } else {
                $objects[$key]['cacheexpire'] = $objectsettings[$key]['cacheexpire'];
            }
        } else {
/*
            // Try loading some defaults from the object init array (cfr. articles/random)
            if (!empty($object['module']) && !empty($object['type'])) {
                $initresult = xarModAPIfunc('objects', 'user', 'read_type_init',
                                            array('module' => $object['module'],
                                                  'type' => $object['type']));
                if (!empty($initresult) && is_array($initresult)) {
                    if (isset($initresult['nocache'])) {
                        $object['nocache'] = $initresult['nocache'];
                        $objects[$key]['nocache'] = $initresult['nocache'];
                    }
                    if (isset($initresult['displaycache'])) {
                        $object['displaycache'] = $initresult['displaycache'];
                        $objects[$key]['displaycache'] = $initresult['displaycache'];
                    }
                    if (isset($initresult['usershared'])) {
                        $object['usershared'] = $initresult['usershared'];
                        $objects[$key]['usershared'] = $initresult['usershared'];
                    }
                    if (isset($initresult['cacheexpire'])) {
                        $object['cacheexpire'] = $initresult['cacheexpire'];
                        $objects[$key]['cacheexpire'] = $initresult['cacheexpire'];
                    }
                }
            }
*/
        }
        if (!isset($object['nocache'])) {
            $objects[$key]['nocache'] = 0;
        }
// TODO: specify supported methods ?
        if (!isset($object['displaycache'])) {
            $objects[$key]['displaycache'] = 1;
        }
        if (!isset($object['usershared'])) {
            $objects[$key]['usershared'] = 1;
        }
        if (!isset($object['cacheexpire'])) {
            $objects[$key]['cacheexpire'] = '';
        }
    }
    return $objects;
}

?>
