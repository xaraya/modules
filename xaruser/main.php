<?php
/**
 * Hitcount Module
 *
 * @package modules
 * @subpackage hitcount module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */

/**
 * the main user function (nothing interesting here - might be removed)
 */
function hitcount_user_main()
{
// Security Check
    if(!xarSecurity::check('ViewHitcountItems')) return;

    // Load API
    if (!xarMod::apiLoad('hitcount', 'user')) return;

    $data['title'] = xarML('Modules we are currently counting display hits for : (test)');
    $data['moditems'] = array();
    $moduleList = array();

    $numitems = xarModVars::get('hitcount','numitems');
    if (empty($numitems)) {
        $numitems = 10;
    }
    $modlist = xarMod::apiFunc('hitcount','user','getmodules');
    foreach ($modlist as $modid => $itemtypes) {
        $modinfo = xarMod::getInfo($modid);
        // Get the list of all item types for this module (if any)
        $mytypes = xarMod::apiFunc($modinfo['name'],'user','getitemtypes',
                                 // don't throw an exception if this function doesn't exist
                                 array(), 0);
        if (!isset($moduleList[$modinfo['displayname']]['modid'])) {
            $moduleList[$modinfo['displayname']]['modid'] = $modid;
        }

        $mod =& $moduleList[$modinfo['displayname']];
        $mod['numitems'] = 0;
        $mod['numhits']  = 0;
        $mod['tophits']  = array();
        $mod['toplinks'] = array();

        foreach ($itemtypes as $itemtype => $stats) {
            $moditem = array();
            $mod['numitems'] += $moditem['numitems'] = $stats['items'];
            $mod['numhits'] += $moditem['numhits'] = $stats['hits'];
            if ($itemtype == 0) {
                $moditem['name'] = ucwords($modinfo['displayname']);
                $moditem['link'] = xarController::URL($modinfo['name'],'user','main');
            } else {
                if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                    $moditem['name'] = $mytypes[$itemtype]['label'];
                    $moditem['link'] = $mytypes[$itemtype]['url'];
                } else {
                    $moditem['name'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                    $moditem['link'] = xarController::URL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
                }
            }
            $moditem['tophits'] = xarMod::apiFunc('hitcount','user','topitems',
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

                $moditem['toplinks'] = xarMod::apiFunc($modinfo['name'],'user','getitemlinks',
                                                     array('itemtype' => $itemtype,
                                                           'itemids' => $itemids),
                                                     0); // don't throw an exception here
                if (!empty($moditem['toplinks'])) {
                    foreach ($moditem['toplinks'] as $itemid => $toplink) {
                        if (!isset($itemid2hits[$itemid])) continue;
                        $moditem['toplinks'][$itemid]['hits'] = $itemid2hits[$itemid];
                    }
                } else {
                    $moditem['toplinks'] = array();
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

    xarTpl::setPageTitle(xarVar::prepForDisplay(xarML('Top Items')));

    // Return output
    return $data;
}

?>
