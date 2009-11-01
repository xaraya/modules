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
/**
 * @author jsb
 *
 * @return array Storage types, with key set to storage type and value set to its settings
 */
function xarcachemanager_adminapi_getstoragetypes()
{
    static $storagetypes;
    if (!empty($storagetypes)) return $storagetypes;

    $storagetypes = array();
    $storagetypes['filesystem']   = array('name'    => 'filesystem',
                                          'label'   => 'Filesystem',
                                          'enabled' => true);
    $storagetypes['database']     = array('name'    => 'database',
                                          'label'   => 'Database',
                                          'enabled' => true);
    $storagetypes['apc']          = array('name'    => 'apc',
                                          'label'   => 'Alternative PHP Cache (APC)',
                                          'enabled' => function_exists('apc_fetch') ? true : false);
    $storagetypes['eaccelerator'] = array('name'    => 'eaccelerator',
                                          'label'   => 'eAccelerator',
                                          'enabled' => function_exists('eaccelerator_get') ? true : false);
    $storagetypes['memcached']    = array('name'    => 'memcached',
                                          'label'   => 'Memcached Server(s)',
                                          'enabled' => class_exists('Memcache') ? true : false);
    $storagetypes['mmcache']      = array('name'    => 'mmcache',
                                          'label'   => 'Turck MMCache',
                                          'enabled' => function_exists('mmcache_get') ? true : false);
    $storagetypes['xcache']       = array('name'    => 'xcache',
                                          'label'   => 'XCache',
                                          'enabled' => function_exists('xcache_get') ? true : false);
    $storagetypes['dummy']        = array('name'    => 'dummy',
                                          'label'   => 'Dummy Storage',
                                          'enabled' => false);

    // return the storage types and their settings
    return $storagetypes;
}
?>
