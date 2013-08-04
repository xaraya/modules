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
    if(!xarSecurityCheck('AdminComments'))
        return;

    $output['gt_pages']     = 0;
    $output['gt_total']     = 0;
    $output['gt_inactive']  = 0;

    // get statistics for all comments (excluding root nodes)
    $modlist = xarMod::apiFunc('comments','user','getmodules');

    // get statistics for all inactive comments
    $inactive = xarMod::apiFunc('comments','user','getmodules',
                              array('status' => 'inactive'));

    $data = array();
    foreach ($modlist as $modid => $itemtypes) {
        $modinfo = xarModGetInfo($modid);
        // Get the list of all item types for this module (if any)
        //Psspl:Commneted codew for resolving error.
        //$mytypes = xarMod::apiFunc($modinfo['name'],'user','getitemtypes',
                                 // don't throw an exception if this function doesn't exist
          //                       array(), 0);
        foreach ($itemtypes as $itemtype => $stats) {
            $moditem = array();
            $moditem['modid'] = $modid;
            $moditem['pages'] = $stats['items'];
            $moditem['total'] = $stats['comments'];
            if (isset($inactive[$modid]) && isset($inactive[$modid][$itemtype])) {
                $moditem['inactive'] = $inactive[$modid][$itemtype]['comments'];
            } else {
                $moditem['inactive'] = 0;
            }
            if ($itemtype == 0) {
                $moditem['modname'] = ucwords($modinfo['displayname']);
            //    $moditem['modlink'] = xarModURL($modinfo['name'],'user','main');
            } else {
                if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                    $moditem['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                //    $moditem['modlink'] = $mytypes[$itemtype]['url'];
                } else {
                    $moditem['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                //    $moditem['modlink'] = xarModURL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
                }
            }
            $moditem['module_url'] = xarModURL('comments','admin','module_stats',
                                               array('modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype));
            $moditem['delete_url'] = xarModURL('comments','admin','delete',
                                               array('dtype' => 'module',
                                                     'modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype));
            $data[] = $moditem;
            $output['gt_pages'] += $moditem['pages'];
            $output['gt_total'] += $moditem['total'];
        }
    }
    $output['data']             = $data;
    $output['delete_all_url']   = xarModURL('comments',
                                            'admin',
                                            'delete',
                                            array('dtype' => 'all'));

    return $output;

}
?>