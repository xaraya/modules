<?php
/**
 * File: $Id$
 *
 * xarCacheManager event handler functions
 *
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author jsb | mikespub
 */

// Only define this event handler if auto-caching is enabled
if (defined('XARCACHE_IS_ENABLED') &&
    file_exists('var/cache/output/autocache.start')) {
/**
 * Log the URL requested by this first-time visitor
 * @return Boolean
 */
function xarcachemanager_eventapi_OnSessionCreate($args)
{
    // cfr. includes/xarCache.php
    xarPage_autoCacheLogStatus('MISS');

    return true;
}
}

?>
