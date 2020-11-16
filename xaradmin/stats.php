<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * View Statistics about comments per module
 *
 */
function comments_admin_stats()
{

    // Security Check
    if (!xarSecurity::check('AdminComments')) {
        return;
    }

    $data['gt_items']     = 0;
    $data['gt_total']     = 0;
    $data['gt_inactive']  = 0;

    $modlist = xarMod::apiFunc('comments', 'user', 'modcounts');

    $inactive = xarMod::apiFunc(
        'comments',
        'user',
        'modcounts',
        array('status' => 'inactive')
    );

    $moditems = array();
    foreach ($modlist as $modid => $itemtypes) {
        $modinfo = xarMod::getInfo($modid);
        // Get the list of all item types for this module (if any)
        //Psspl:Commneted codew for resolving error.
        //$mytypes = xarMod::apiFunc($modinfo['name'],'user','getitemtypes',
        // don't throw an exception if this function doesn't exist
        //                       array(), 0);
        foreach ($itemtypes as $itemtype => $stats) {
            $moditem = array();
            $moditem['modid'] = $modid;
            $moditem['items'] = $stats['items'];
            $moditem['total'] = $stats['comments'];
            if (isset($inactive[$modid]) && isset($inactive[$modid][$itemtype])) {
                $moditem['inactive'] = $inactive[$modid][$itemtype]['comments'];
            } else {
                $moditem['inactive'] = 0;
            }
            if ($itemtype == 0) {
                $moditem['modname'] = ucwords($modinfo['displayname']) . ': itemtype ' . $itemtype;
            //    $moditem['modlink'] = xarController::URL($modinfo['name'],'user','main');
            } else {
                if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                    $moditem['modname'] = ucwords($modinfo['displayname']) . ': itemtype: ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                //    $moditem['modlink'] = $mytypes[$itemtype]['url'];
                } else {
                    $moditem['modname'] = ucwords($modinfo['displayname']) . ': itemtype ' . $itemtype;
                    //    $moditem['modlink'] = xarController::URL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
                }
            }
            $moditem['module_url'] = xarController::URL(
                'comments',
                'admin',
                'module_stats',
                array('modid' => $modid,
                                                     'itemtype' => $itemtype)
            );

            $moditem['delete_url'] = xarController::URL(
                'comments',
                'admin',
                'delete',
                array('dtype' => 'itemtype',
                                                     'modid' => $modid,
                                                    'redirect' => 'stats',
                                                    'itemtype' => $itemtype)
            );
            $moditems[] = $moditem;
            $data['gt_items'] += $moditem['items'];
            $data['gt_total'] += $moditem['total'];
            $data['gt_inactive'] += $moditem['inactive'];
        }
    }
    $data['moditems']             = $moditems;
    $data['delete_all_url']   = xarController::URL(
        'comments',
        'admin',
        'delete',
        array('dtype' => 'all', 'redirect' => 'stats')
    );

    return $data;
}
