<?php 
// File: $Id: s.xaruser.php 1.11 02/12/22 21:48:43-05:00 John.Cox@mcnabb. $
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: Gregor J. Rothfuss
// Purpose of file:  trackback user display functions
// ----------------------------------------------------------------------

/**
 * the main user function (nothing interesting here - might be removed)
 */
function trackback_user_main()
{
    // Security check
    if (!xarSecAuthAction(0, 'Trackback::', '::', ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $data['title'] = "Modules we're currently counting display trackbacks for : (test)";
    $data['moditems'] = array();

    $modlist = xarModAPIFunc('trackback','user','getmodules');
    foreach ($modlist as $modid => $numitems) {
        $modinfo = xarModGetInfo($modid);
        $moditem = array();
        $moditem['name'] = $modinfo['name'];
        $moditem['numitems'] = $numitems;
        $moditem['link'] = xarModURL($modinfo['name'],'user','main');

        $data['moditems'][] = $moditem;
    }

    xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                       xarVarPrepForDisplay(xarML('Trackback Items')));

    // Return output
    return $data;
}

/**
 * display the trackback (= display hook)
 *
 * @param $args['objectid'] ID of the item this trackback is for
 * @param $args['extrainfo'] not particularly relevant here
 * @returns output
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

    $trackback = xarModAPIFunc('trackback',
                             'user',
                             'get',
                             $args);

    if (isset($trackback)) {
        // Display current trackback or set the cached variable
        if (!xarVarIsCached('Hooks.trackback','save') ||
            xarVarGetCached('Hooks.trackback','save') == false ) {
// TODO: do something here :-)
            return '(' . join('-',$trackback) . ' ' . xarML('TODO: trackback output ?') . ')';
        } else {
            xarVarSetCached('Hooks.trackback','value',$trackback);
        }
    }

    return '';
}

?>
