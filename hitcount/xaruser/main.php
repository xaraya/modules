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

    $numitems = xarModGetVar('hitcount','numitems');
    if (empty($numitems)) {
        $numitems = 10;
    }
    $modlist = xarModAPIFunc('hitcount','user','getmodules');
    foreach ($modlist as $modid => $itemtypes) {
        $modinfo = xarModGetInfo($modid);
        // Get the list of all item types for this module (if any)
        $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                 // don't throw an exception if this function doesn't exist
                                 array(), 0);
        if (!isset($moduleList[$modinfo['displayname']]['modid'])) {
            $moduleList[$modinfo['displayname']]['modid'] = $modid;
        }

        $mod =& $moduleList[$modinfo['displayname']];
        $mod['numitems'] = 0;
        $mod['numhits']  = 0;
        $mod['tophits']  = NULL;
        $mod['toplinks'] = NULL;

        foreach ($itemtypes as $itemtype => $stats) {
            $moditem = array();
            $mod['numitems'] += $moditem['numitems'] = $stats['items'];
            $mod['numhits'] += $moditem['numhits'] = $stats['hits'];
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
                                                      'itemtype' => $itemtype,
                                                      'numitems' => $numitems));
            foreach ($moditem['tophits'] as $tophit) {
                $mod['tophits']["$tophit[hits]:$tophit[itemid]"]['itemtype'] = $itemtype;
                $mod['tophits']["$tophit[hits]:$tophit[itemid]"]['itemid'] = $tophit['itemid'];
                $mod['tophits']["$tophit[hits]:$tophit[itemid]"]['hits'] = $tophit['hits'];
            }


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

                foreach($moditem['toplinks'] as $itemid => $toplink) {
                    $mod['toplinks']["$toplink[hits]:$itemid"]['itemtype'] = $itemtype;
                    $mod['toplinks']["$toplink[hits]:$itemid"]['itemid']   = $itemid;
                    $mod['toplinks']["$toplink[hits]:$itemid"]['url']      = $toplink['url'];
                    $mod['toplinks']["$toplink[hits]:$itemid"]['title']    = $toplink['title'];
                    $mod['toplinks']["$toplink[hits]:$itemid"]['label']    = $toplink['label'];
                    $mod['toplinks']["$toplink[hits]:$itemid"]['hits']     = $toplink['hits'];
                }
            }
            $data['moditems'][] = $moditem;
        }
    }

    // Sort the toplinks / tophits by most hits -> least and newest --> oldest
    foreach ($moduleList as $modName => $module) {

        uksort($module['tophits'], 'strnatcasecmp');
        $moduleList[$modName]['tophits'] = array_reverse($module['tophits']);

        uksort($module['toplinks'], 'strnatcasecmp');
        $moduleList[$modName]['toplinks'] = array_reverse($module['toplinks']);
    }

    $data['moduleList'] = $moduleList;

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Top Items')));

    // Return output
    return $data;
}

?>
