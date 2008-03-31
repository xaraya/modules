<?php
/**
 * Hitcount
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Hitcount Module
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
    if (!xarSecurityCheck('AdminHitcount')) return;

    if(!xarVarFetch('modid',    'isset', $modid,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemid',   'isset', $itemid,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sort',     'isset', $sort,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sortorder','isset', $sortorder,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('startnum', 'isset', $startnum,     1, XARVAR_NOT_REQUIRED)) {return;}

    $data = array();

    $modlist = xarModAPIFunc('hitcount','user','getmodules');

    if (empty($modid)) {
        $data['moditems'] = array();
        $data['numitems'] = 0;
        $data['numhits'] = 0;
        foreach ($modlist as $modid => $itemtypes) {
            $modinfo = xarModGetInfo($modid);
            // Get the list of all item types for this module (if any)
            $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(), 0);
            foreach ($itemtypes as $itemtype => $stats) {
                $moditem = array();
                $moditem['numitems'] = $stats['items'];
                $moditem['numhits'] = $stats['hits'];
                if ($itemtype == 0) {
                    $moditem['name'] = ucwords($modinfo['displayname']);
                //    $moditem['link'] = xarModURL($modinfo['name'],'user','main');
                } else {
                    if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                        $moditem['name'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
                    //    $moditem['link'] = $mytypes[$itemtype]['url'];
                    } else {
                        $moditem['name'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
                    //    $moditem['link'] = xarModURL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
                    }
                }
                $moditem['link'] = xarModURL('hitcount','admin','view',
                                             array('modid' => $modid,
                                                   'itemtype' => empty($itemtype) ? null : $itemtype));
                $moditem['delete'] = xarModURL('hitcount','admin','delete',
                                               array('modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype));
                $data['moditems'][] = $moditem;
                $data['numitems'] += $moditem['numitems'];
                $data['numhits'] += $moditem['numhits'];
            }
        }
        $data['delete'] = xarModURL('hitcount','admin','delete');
    } else {
        $modinfo = xarModGetInfo($modid);
        if (empty($itemtype)) {
            $data['modname'] = ucwords($modinfo['displayname']);
            $itemtype = null;
            if (isset($modlist[$modid][0])) {
                $stats = $modlist[$modid][0];
            }
        } else {
            // Get the list of all item types for this module (if any)
            $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(), 0);
            if (isset($mytypes) && !empty($mytypes[$itemtype])) {
                $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype . ' - ' . $mytypes[$itemtype]['label'];
            //    $data['modlink'] = $mytypes[$itemtype]['url'];
            } else {
                $data['modname'] = ucwords($modinfo['displayname']) . ' ' . $itemtype;
            //    $data['modlink'] = xarModURL($modinfo['name'],'user','view',array('itemtype' => $itemtype));
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
        $numstats = xarModVars::get('hitcount','numstats');
        if (empty($numstats)) {
            $numstats = 100;
        }
        if ($numstats < $data['numitems']) {
            $data['pager'] = xarTplGetPager($startnum,
                                            $data['numitems'],
                                            xarModURL('hitcount','admin','view',
                                                      array('modid' => $modid,
                                                            'itemtype' => $itemtype,
                                                            'sort' => $sort,
                                                            'sortorder' => $sortorder,
                                                            'startnum' => '%%')),
                                            $numstats);
        } else {
            $data['pager'] = '';
        }
        $data['modid'] = $modid;
        $getitems = xarModAPIFunc('hitcount','user','getitems',
                                  array('modid' => $modid,
                                        'itemtype' => $itemtype,
                                        'numitems' => $numstats,
                                        'startnum' => $startnum,
                                        'sort' => $sort,
                                        'sortorder' => $sortorder
                                        ));
        $showtitle = xarModVars::get('hitcount','showtitle');
        if (!empty($showtitle)) {
           $itemids = array_keys($getitems);
           $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                                      array('itemtype' => $itemtype,
                                            'itemids' => $itemids),
                                      0); // don't throw an exception here
        } else {
           $itemlinks = array();
        }
        $data['moditems'] = array();
        foreach ($getitems as $itemid => $numhits) {
            $data['moditems'][$itemid] = array();
            $data['moditems'][$itemid]['numhits'] = $numhits;
            $data['moditems'][$itemid]['delete'] = xarModURL('hitcount','admin','delete',
                                                             array('modid' => $modid,
                                                                   'itemtype' => $itemtype,
                                                                   'itemid' => $itemid));
            if (isset($itemlinks[$itemid])) {
                $data['moditems'][$itemid]['link'] = $itemlinks[$itemid]['url'];
                $data['moditems'][$itemid]['title'] = $itemlinks[$itemid]['label'];
            }
        }
        unset($getitems);
        unset($itemlinks);
        $data['delete'] = xarModURL('hitcount','admin','delete',
                                    array('modid' => $modid,
                                          'itemtype' => $itemtype));
        $data['sortlink'] = array();
        if (empty($sortorder) || $sortorder=='ASC') {
            $sortorder = 'DESC';
        } else {
            $sortorder = 'ASC';
        }
//        if (empty($sort) || $sort == 'itemid') {
//             $data['sortlink']['itemid'] = '';
//
//        } else {
             $data['sortlink']['itemid'] = xarModURL('hitcount','admin','view',
                                                     array('modid' => $modid,
                                                           'itemtype' => $itemtype,
                                                           'sortorder' => $sortorder
                                                           ));

//        }
 //       if (!empty($sort) && $sort == 'numhits') {
 //            $data['sortlink']['numhits'] = '';
 //       } else {
             $data['sortlink']['numhits'] = xarModURL('hitcount','admin','view',
                                                      array('modid' => $modid,
                                                            'itemtype' => $itemtype,
                                                            'sort' => 'numhits',
                                                           'sortorder' => $sortorder
                                                            ));
 //       }
 //       $data['sortorder'] = $sortorder;
    }

    return $data;
}

?>
