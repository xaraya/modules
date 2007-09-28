<?php

/**
 * Create a new SimplePie object.
 * TODO: cache the object localy
 * TODO: ensure there is somewhere to cache feed data
 * TODO: hook into the error handler
 * TODO: log some of the simple failures
 */

function simplepie_userapi_newfeed($args)
{
    // SimplePie assumes PHP 5.1.0
    // Define some required constants if we aren't there yet.
    if (!defined('CURLOPT_ENCODING')) define('CURLOPT_ENCODING', 999);

    // Include the main class file
    include_once('modules/simplepie/xarclass/simplepie.inc');

    extract($args);

    // Assume cache will be used.
    if (!isset($enable_cache)) $enable_cache = true;

    if (empty($feed_url)) $feed_url = null;
    if (empty($cache_max_minutes)) $cache_max_minutes = null;

    // Make sure there is a cache directory if required.
    if ($enable_cache) {
        if (empty($cache_location)) {
            // No alternative cache specified, so use the var root.
            $cache_location = xarCoreGetVarDirPath() . '/cache/simplepie';

            // Create the simplepie directory if it does not already exist.
            if (!is_dir($cache_location)) {
                if (!@mkdir($cache_location)) $enable_cache = false;
            }
        }
    }

    // Check the cache directory is writable.
    if ($enable_cache && (!is_dir($cache_location) || !is_writable($cache_location))) $enable_cache = false;

    // Create a new object.
    $pie = new SimplePie($feed_url, $cache_location, $cache_max_minutes);

    // Set some properties of the parser object.
    // Check the methods exist before trying to use them, as SimplePie have rather
    // dynamic public methods.
    if (method_exists($pie, 'enable_caching')) $pie->enable_caching($enable_cache);
    if (method_exists($pie, 'enable_cache')) $pie->enable_cache($enable_cache);

    if (method_exists($pie, 'enable_xmldump')) $pie->enable_xmldump(false);
    if (method_exists($pie, 'enable_xml_dump')) $pie->enable_xml_dump(false);

    // Do not try to change the order of the feed.
    if (method_exists($pie, 'enable_order_by_date')) $pie->enable_order_by_date(false);

    return $pie;
}

?>
