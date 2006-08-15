<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Comments Module
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
    if(!xarSecurityCheck('Comments-Admin'))
        return;

    $output['gt_pages']     = 0;
    $output['gt_total']     = 0;
    $output['gt_inactive']  = 0;

    // get statistics for all comments (excluding root nodes)
    $modlist = xarModAPIFunc('comments','user','getmodules');

    // get statistics for all inactive comments
    $inactive = xarModAPIFunc('comments','user','getmodules',
                              array('status' => 'inactive'));

    $data = array();
    foreach ($modlist as $modid => $itemtypes) {
        $modinfo = xarModGetInfo($modid);
        // Get the list of all item types for this module (if any)
        $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                 // don't throw an exception if this function doesn't exist
                                 array(), 0);
        foreach ($itemtypes as $itemtype => $stats) {
            $moditem = array();
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