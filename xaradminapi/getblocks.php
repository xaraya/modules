<?php
/**
 * Get configuration of block caching for blocks
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.config.blockcache');
use Xaraya\Modules\CacheManager\Config\BlockCache;

/**
 * get configuration of block caching for all blocks
 * @uses BlockCache::getConfig()
 * @return array Block caching configurations
 */
function xarcachemanager_adminapi_getblocks($args)
{
    extract($args);

    // Get all block cache settings
    return BlockCache::getConfig();
}
