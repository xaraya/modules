<?php
/**
 * Pre-fetch pages for caching (executed by the scheduler module)
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
sys::import('modules.xarcachemanager.class.scheduler');
use Xaraya\Modules\CacheManager\CacheScheduler;

/**
 * This is a poor-man's alternative for using wget in a cron job :
 * wget -r -l 1 -w 2 -nd --delete-after -o /tmp/wget.log http://www.mysite.com/
 *
 * @uses CacheScheduler::prefetch()
 * @author mikespub
 * @access private
 */
function xarcachemanager_schedulerapi_prefetch($args)
{
    return CacheScheduler::prefetch($args);
}
