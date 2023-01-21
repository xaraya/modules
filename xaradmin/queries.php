<?php
/**
 * Queries
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
 * configure query caching (TODO)
 * @uses QueryCache::modifyConfig()
 * @return array
 */
function xarcachemanager_admin_queries($args)
{
    return QueryCache::modifyConfig($args);
}
