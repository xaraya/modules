<?php
/**
 * Display the trackback (= display hook)
 *
 * @param int $args['objectid'] ID of the item this trackback is for
 * @param array $args['extrainfo'] not particularly relevant here
 * @return output with trackback information
 */
function trackback_user_display($args)
{
    extract($args);

    // Run API function
    $args['modname'] = xarModGetName();

    $args['itemtype'] = 0;
    if (isset($extrainfo) && is_array($extrainfo)) {
        if (isset($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
            $args['itemtype'] = $extrainfo['itemtype'];
        }
    }

    // TODO: do you need the returnurl here too for some reason ?

    $trackBack = xarModAPIFunc('trackback',
                             'user',
                             'get',
                             $args);

    if (isset($trackBack)) {
        // Display current trackback or set the cached variable
        if (!xarVarIsCached('Hooks.trackback','save') ||
            xarVarGetCached('Hooks.trackback','save') == false ) {
        // TODO: do something here :-)
            return '(' . join('-',$trackBack) . ' ' . xarML('TODO: trackback output ?') . ')';
        } else {
            xarVarSetCached('Hooks.trackback','value',$trackBack);
        }
    }

    return '';
}
?>