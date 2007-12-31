<?php

/**
 * Central exchange for the module.
 *
 * @param pid integer Xarpages page ID.
 *
 * If a xarpags page ID is passed in, then the function to run will be read
 * from the GET parameter 'mfunc'. This then loads the correct page, then 
 * causes all URLs within the module to be generated in the context of the
 * given xarpages page ID.
 *
 * In addition, any other parameters can be passed in, such as the mag id
 * to ensure the page is locked onto a given magazine, or issue or whatever.
 *
 */

function mag_user_main($args)
{
    extract($args);

    // Check if we have been called up from xarpages
    if (!empty($pid)) {
        // We have a xarpages page ID.
        // Get the function, i.e. the view.
        xarVarFetch('mfunc', 'enum:archive:article:authors:contents:current:mags:series', $mfunc, NULL, XARVAR_NOT_REQUIRED);

        // Set the cached value that drives all URLs in this module.
        /*if (!empty($mfunc))*/ xarVarSetCached('mag', 'pid', $pid);
    }

    if (empty($mfunc)) $mfunc = 'mags';

    // Call up the required view.
    return xarModFunc('mag', 'user', $mfunc, $args);
}

?>