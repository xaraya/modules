<?php
/**
 * Return the list of storage types supported on this server
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.manager');

/**
 * @author jsb
 *
 * @return array Storage types, with key set to storage type and value set to its settings
 */
function xarcachemanager_adminapi_getstoragetypes()
{
    static $storagetypes;
    if (!empty($storagetypes)) {
        return $storagetypes;
    }

    $storagetypes = [];
    $storagetypes['filesystem']   = ['name'    => 'filesystem',
                                          'label'   => 'Filesystem',
                                          'enabled' => true, ];
    $storagetypes['database']     = ['name'    => 'database',
                                          'label'   => 'Database',
                                          'enabled' => true, ];
    $storagetypes['apcu']         = ['name'    => 'apcu',
                                          'label'   => 'APC User Cache (APCu)',
                                          'enabled' => function_exists('apcu_fetch') ? true : false, ];
    $storagetypes['doctrine']     = ['name'    => 'doctrine',
                                          'label'   => 'Doctrine Cache (via composer)',
                                          'enabled' => class_exists('Doctrine\\Common\\Cache\\CacheProvider') ? true : false, ];
    /**
    $storagetypes['eaccelerator'] = ['name'    => 'eaccelerator',
                                          'label'   => 'eAccelerator',
                                          'enabled' => function_exists('eaccelerator_get') ? true : false, ];
     */
    $storagetypes['memcached']    = ['name'    => 'memcached',
                                          'label'   => 'Memcached Server(s)',
                                          'enabled' => class_exists('Memcache') ? true : false, ];
    /**
    $storagetypes['mmcache']      = ['name'    => 'mmcache',
                                          'label'   => 'Turck MMCache',
                                          'enabled' => function_exists('mmcache_get') ? true : false, ];
     */
    /**
    $storagetypes['predis']       = ['name'    => 'predis',
                                          'label'   => 'Redis Server(s) (via composer)',
                                          'enabled' => class_exists('Predis\\Client') ?  true : false, ];
     */
    /**
    $storagetypes['redis']        = ['name'    => 'redis',
                                          'label'   => 'Redis Server(s) (extension)',
                                          'enabled' => class_exists('Redis') ? true : false, ];
     */
    /**
    $storagetypes['xcache']       = ['name'    => 'xcache',
                                          'label'   => 'XCache',
                                          'enabled' => function_exists('xcache_get') ? true : false, ];
     */
    $storagetypes['dummy']        = ['name'    => 'dummy',
                                          'label'   => 'Dummy Storage',
                                          'enabled' => false, ];

    // return the storage types and their settings
    return $storagetypes;
}
