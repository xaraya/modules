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
    $pie->enable_caching($enable_cache);
    $pie->enable_xmldump(false);

    return $pie;
}

?>
