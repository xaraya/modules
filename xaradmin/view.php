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
 * View statistics about hitcount
 * @return array
 */
function hitcount_admin_view()
{
    // Security Check
    if (!xarSecurity::check('AdminHitcount')) {
        return;
    }

    if (!xarVar::fetch('modid', 'isset', $modid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('itemtype', 'isset', $itemtype, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'isset', $itemid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('sort', 'isset', $sort, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('sortorder', 'isset', $sortorder, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('startnum', 'isset', $startnum, 1, xarVar::NOT_REQUIRED)) {
        return;
    }

    $data = [];

    $modlist = xarMod::apiFunc('hitcount', 'user', 'getmodules');

    if (empty($modid)) {
        $data['moditems'] = [];
        $data['numitems'] = 0;
        $data['numhits'] = 0;
        foreach ($modlist as $modid => $itemtypes) {
            $modinfo = xarMod::getInfo($modid);
            // Get the list of all item types for this module (if any)
            $mytypes = xarMod::apiFunc(
                $modinfo['name'],
                'user',
                'getitemtypes',
                // don't throw an exception if this function doesn't exist
                [],
                0
            );
            foreach ($itemtypes as $itemtype => $stats) {
                $moditem = [];
                $moditem['numitems'] = $stats['items'];
                $moditem['numhits'] = $stats['hits'];
                if ($itemtype == 0) {
                    $moditem['name'] = ucwords($modinfo['displayname']);
                //    $moditem['link'] = xarController::URL($modinfo['name'],'user','main');
                } else {
                    if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                        $moditem['name'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                    //    $moditem['link'] = $mytypes[$itemtype]['url'];
                    } else {
                        $moditem['name'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                        //    $moditem['link'] = xarController::URL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
                    }
                }
                $moditem['link'] = xarController::URL(
                    'hitcount',
                    'admin',
                    'view',
                    ['modid' => $modid,
                                                   'itemtype' => empty($itemtype) ? null : $itemtype, ]
                );
                $moditem['delete'] = xarController::URL(
                    'hitcount',
                    'admin',
                    'delete',
                    ['modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype, ]
                );
                $data['moditems'][] = $moditem;
                $data['numitems'] += $moditem['numitems'];
                $data['numhits'] += $moditem['numhits'];
            }
        }
        $data['delete'] = xarController::URL('hitcount', 'admin', 'delete');
    } else {
        $modinfo = xarMod::getInfo($modid);
        if (empty($itemtype)) {
            $data['modname'] = ucwords($modinfo['displayname']);
            $itemtype = null;
            if (isset($modlist[$modid][0])) {
                $stats = $modlist[$modid][0];
            }
        } else {
            // Get the list of all item types for this module (if any)
            $mytypes = xarMod::apiFunc(
                $modinfo['name'],
                'user',
                'getitemtypes',
                // don't throw an exception if this function doesn't exist
                [],
                0
            );
            if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
            //    $data['modlink'] = $mytypes[$itemtype]['url'];
            } else {
                $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                //    $data['modlink'] = xarController::URL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
            }
            if (isset($modlist[$modid][$itemtype])) {
                $stats = $modlist[$modid][$itemtype];
            }
        }
        if (isset($stats)) {
            $data['numitems'] = $stats['items'];
            $data['numhits'] = $stats['hits'];
        } else {
            $data['numitems'] = 0;
            $data['numhits'] = '';
        }
        $numstats = xarModVars::get('hitcount', 'numstats');
        if (empty($numstats)) {
            $numstats = 100;
        }
        // pager
        $data['startnum'] = $startnum;
        $data['total'] = $data['numitems'];
        $data['urltemplate'] = xarController::URL(
            'hitcount',
            'admin',
            'view',
            ['modid' => $modid,
                                                            'itemtype' => $itemtype,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'startnum' => '%%', ]
        );
        $data['itemsperpage'] = $numstats;

        $data['modid'] = $modid;
        $getitems = xarMod::apiFunc(
            'hitcount',
            'user',
            'getitems',
            ['modid' => $modid,
                                        'itemtype' => $itemtype,
                                        'numitems' => $numstats,
                                        'startnum' => $startnum,
                                        'sort' => $sort,
                                        'sortorder' => $sortorder,
                                        ]
        );
        $showtitle = xarModVars::get('hitcount', 'showtitle');
        if (!empty($showtitle)) {
            $itemids = array_keys($getitems);
            $itemlinks = xarMod::apiFunc(
                $modinfo['name'],
                'user',
                'getitemlinks',
                ['itemtype' => $itemtype,
                                            'itemids' => $itemids, ],
                0
            ); // don't throw an exception here
        } else {
            $itemlinks = [];
        }
        $data['moditems'] = [];
        foreach ($getitems as $itemid => $numhits) {
            $data['moditems'][$itemid] = [];
            $data['moditems'][$itemid]['numhits'] = $numhits;
            $data['moditems'][$itemid]['delete'] = xarController::URL(
                'hitcount',
                'admin',
                'delete',
                ['modid' => $modid,
                                                                   'itemtype' => $itemtype,
                                                                   'itemid' => $itemid, ]
            );
            if (isset($itemlinks[$itemid])) {
                $data['moditems'][$itemid]['link'] = $itemlinks[$itemid]['url'];
                $data['moditems'][$itemid]['title'] = $itemlinks[$itemid]['label'];
            }
        }
        unset($getitems);
        unset($itemlinks);
        $data['delete'] = xarController::URL(
            'hitcount',
            'admin',
            'delete',
            ['modid' => $modid,
                                          'itemtype' => $itemtype, ]
        );
        $data['sortlink'] = [];
        if (empty($sortorder) || $sortorder=='ASC') {
            $sortorder = 'DESC';
        } else {
            $sortorder = 'ASC';
        }
//        if (empty($sort) || $sort == 'itemid') {
//             $data['sortlink']['itemid'] = '';
//
//        } else {
        $data['sortlink']['itemid'] = xarController::URL(
            'hitcount',
            'admin',
            'view',
            ['modid' => $modid,
                                                           'itemtype' => $itemtype,
                                                           'sortorder' => $sortorder,
                                                           ]
        );

//        }
        //       if (!empty($sort) && $sort == 'numhits') {
        //            $data['sortlink']['numhits'] = '';
        //       } else {
        $data['sortlink']['numhits'] = xarController::URL(
            'hitcount',
            'admin',
            'view',
            ['modid' => $modid,
                                                            'itemtype' => $itemtype,
                                                            'sort' => 'numhits',
                                                           'sortorder' => $sortorder,
                                                            ]
        );
        //       }
 //       $data['sortorder'] = $sortorder;
    }

    return $data;
}
