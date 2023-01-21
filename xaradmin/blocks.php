<?php
/**
 * Config block caching
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.config.blockcache');
use Xaraya\Modules\CacheManager\Config\BlockCache;

/**
 * configure block caching
 * @uses BlockCache::modifyConfig()
 * @return array
 */
function xarcachemanager_admin_blocks($args)
{
    return BlockCache::modifyConfig($args);
}
