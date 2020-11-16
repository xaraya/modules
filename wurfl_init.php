<?php
/**
 * Wurfl Module
 *
 * @package modules
 * @subpackage wurfl module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Initialize WURFL
 *
 */

function wurfl_init($args=array())
{
    if (!isset($args['mode'])) {
        $args['mode'] = 'performance';
    }
    
    sys::import('modules.wurfl.xarincludes.WURFL.Application');
    $resourcesDir = sys::code() . 'modules/wurfl/xarincludes/resources';
    
    $persistenceDir = $resourcesDir.'/storage/persistence';
    $cacheDir = $resourcesDir.'/storage/cache';
    
    // Create WURFL Configuration
    $wurflConfig = new WURFL_Configuration_InMemoryConfig();
    
    // Set location of the WURFL File
    $wurflConfig->wurflFile($resourcesDir.'/wurfl.xml');
    
    // Set the match mode for the API ('performance' or 'accuracy')
    $wurflConfig->matchMode($args['mode']);
    
    // Setup WURFL Persistence
    $wurflConfig->persistence('file', array('dir' => $persistenceDir));
    
    // Setup Caching
    $wurflConfig->cache('file', array('dir' => $cacheDir, 'expiration' => 36000));
    
    // Create a WURFL Manager Factory from the WURFL Configuration
    $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
    
    // Create a WURFL Manager
    /* @var $wurflManager WURFL_WURFLManager */
    $wurflManager = $wurflManagerFactory->create();
    return $wurflManager;
}
