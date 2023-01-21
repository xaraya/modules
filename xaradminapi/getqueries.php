<?php
/**
 * Get queries caching config
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.config.querycache');
use Xaraya\Modules\CacheManager\Config\QueryCache;

/**
 * get configuration of query caching for expensive queries

 * @todo currently unsupported + refers to legacy modules
 * @uses QueryCache::getConfig()
 * @return array of query caching configurations
 */
function xarcachemanager_adminapi_getqueries($args)
{
    extract($args);

    // Get all query cache settings
    return QueryCache::getConfig();
}
