<?php

/**
 * Cache some useful values from the data being passed to templates.
 * Should be called up before every page is rendered.
 * The cached data is used by blocks.
 *
 * @param args array Template data to be passed to the page template for rendering.
 *
 */

function mag_userapi_cachevalues(&$args)
{
    if (empty($args) || !is_array($args)) return;

    // The names of the keys for cacheing.
    $cache_names = array(
        'function',
        'mid', 'mag',
        'iid', 'issue',
        'sid', 'series',
        'toc',
        'auid', 'author', 'article_authors',
        'aid', 'article'
    );

    // Loop over the names and cache if set.
    foreach($cache_names as $cache_name) {
        if (!empty($args[$cache_name])) xarVarSetCached('mag', $cache_name, $args[$cache_name]);
    }

    // No return value at this stage.
    return;
}

?>