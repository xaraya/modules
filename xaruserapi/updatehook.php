<?php

/**
 * process date/time for the modified item - hook for ('item','update','API')
 */
function julian_userapi_updatehook($args)
{
     // We may have been asked not to update (articles does this when changing article status).
    if (xarVarGetCached('Hooks.all','noupdate')) return;

    // We handle this with the create hook (which can update current records, too)
    return xarModAPIFunc('julian', 'user', 'createhook', $args);
}

?>
