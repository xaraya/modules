<?php

/**
 * the main user function (nothing interesting here - might be removed)
 */
function hitcount_user_main()
{
// Security Check
	if(!xarSecurityCheck('ViewHitcountItems')) return;

    // Load API
    if (!xarModAPILoad('hitcount', 'user')) return;

    $data['title'] = "Modules we're currently counting display hits for : (test)";
    $data['moditems'] = array();

    $modlist = xarModAPIFunc('hitcount','user','getmodules');
    foreach ($modlist as $modid => $itemtypes) {
        $modinfo = xarModGetInfo($modid);
        // Get the list of all item types for this module (if any)
        $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                 // don't throw an exception if this function doesn't exist
                                 array(), 0);
        foreach ($itemtypes as $itemtype => $numitems) {
            $moditem = array();
            $moditem['numitems'] = $numitems;
            if ($itemtype == 0) {
                $moditem['name'] = ucwords($modinfo['displayname']);
                $moditem['link'] = xarModURL($modinfo['name'],'user','main');
            } else {
                if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                    $moditem['name'] = $mytypes[$itemtype]['label'];
                    $moditem['link'] = $mytypes[$itemtype]['url'];
                } else {
                    $moditem['name'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                    $moditem['link'] = xarModURL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
                }
            }
            $moditem['tophits'] = xarModAPIFunc('hitcount','user','topitems',
                                                array('modname'  => $modinfo['name'],
                                                      'itemtype' => $itemtype));
            if (isset($moditem['tophits']) && count($moditem['tophits']) > 0) {
                $itemids = array();
                $itemid2hits = array();
                foreach ($moditem['tophits'] as $tophit) {
                    $itemids[] = $tophit['itemid'];
                    $itemid2hits[$tophit['itemid']] = $tophit['hits'];
                }
                xarModAPILoad($modinfo['name'], 'user');
                if (function_exists($modinfo['name'].'_userapi_getitemlinks') ||
                    file_exists("modules/$modinfo[osdirectory]/xaruserapi/getitemlinks.php")){
                    $moditem['toplinks'] = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                                                         array('itemtype' => $itemtype,
                                                               'itemids' => $itemids));
                    foreach ($moditem['toplinks'] as $itemid => $toplink) {
                        if (!isset($itemid2hits[$itemid])) continue;
                        $moditem['toplinks'][$itemid]['hits'] = $itemid2hits[$itemid];
                    }
                }
            }
            $data['moditems'][] = $moditem;
        }
    }

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Top Items')));

    // Return output
    return $data;
}

?>
