<?php
/**
 * Change Log Module version information
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage changelog
 * @link http://xaraya.com/index.php/release/185.html
 * @author mikespub
 */
/**
 * View changelog entries
 */
function changelog_admin_view()
{
    // Security Check
    if (!xarSecurityCheck('AdminChangeLog')) return;

    if(!xarVarFetch('modid',    'isset', $modid,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemid',   'isset', $itemid,    NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sort',     'isset', $sort,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('startnum', 'isset', $startnum,     1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('editor',   'isset', $editor,    NULL, XARVAR_DONT_SET)) {return;}

    if (empty($editor) || !is_numeric($editor)) {
        $editor = null;
    }

    $data = array();
    $data['editor'] = $editor;

    $modlist = xarModAPIFunc('changelog','user','getmodules',
                             array('editor' => $editor));

    if (empty($modid)) {
        $data['moditems'] = array();
        $data['numitems'] = 0;
        $data['numchanges'] = 0;
        foreach ($modlist as $modid => $itemtypes) {
            $modinfo = xarModGetInfo($modid);
            // Get the list of all item types for this module (if any)
            $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(), 0);
            foreach ($itemtypes as $itemtype => $stats) {
                $moditem = array();
                $moditem['numitems'] = $stats['items'];
                $moditem['numchanges'] = $stats['changes'];
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
                $moditem['link'] = xarModURL('changelog','admin','view',
                                             array('modid' => $modid,
                                                   'itemtype' => empty($itemtype) ? null : $itemtype,
                                                   'editor' => $editor));
                $moditem['delete'] = xarModURL('changelog','admin','delete',
                                               array('modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype,
                                                     'editor' => $editor));
                $data['moditems'][] = $moditem;
                $data['numitems'] += $moditem['numitems'];
                $data['numchanges'] += $moditem['numchanges'];
            }
        }
        $data['delete'] = xarModURL('changelog','admin','delete',
                                    array('editor' => $editor));
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
            $data['numchanges'] = $stats['changes'];
        } else {
            $data['numitems'] = 0;
            $data['numchanges'] = '';
        }
        $numstats = xarModGetVar('changelog','numstats');
        if (empty($numstats)) {
            $numstats = 100;
        }
        if ($numstats < $data['numitems']) {
            $data['pager'] = xarTplGetPager($startnum,
                                            $data['numitems'],
                                            xarModURL('changelog','admin','view',
                                                      array('modid' => $modid,
                                                            'itemtype' => $itemtype,
                                                            'editor' => $editor,
                                                            'sort' => $sort,
                                                            'startnum' => '%%')),
                                            $numstats);
        } else {
            $data['pager'] = '';
        }
        $data['modid'] = $modid;
        $getitems = xarModAPIFunc('changelog','user','getitems',
                                  array('modid' => $modid,
                                        'itemtype' => $itemtype,
                                        'editor' => $editor,
                                        'numitems' => $numstats,
                                        'startnum' => $startnum,
                                        'sort' => $sort));
        $showtitle = xarModGetVar('changelog','showtitle');
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
        foreach ($getitems as $itemid => $numchanges) {
            $data['moditems'][$itemid] = array();
            $data['moditems'][$itemid]['numchanges'] = $numchanges;
            $data['moditems'][$itemid]['showlog'] = xarModURL('changelog','admin','showlog',
                                                              array('modid' => $modid,
                                                                    'itemtype' => $itemtype,
                                                                    'itemid' => $itemid));
            $data['moditems'][$itemid]['delete'] = xarModURL('changelog','admin','delete',
                                                             array('modid' => $modid,
                                                                   'itemtype' => $itemtype,
                                                                   'itemid' => $itemid,
                                                                   'editor' => $editor));
            if (isset($itemlinks[$itemid])) {
                $data['moditems'][$itemid]['link'] = $itemlinks[$itemid]['url'];
                $data['moditems'][$itemid]['title'] = $itemlinks[$itemid]['label'];
            }
        }
        unset($getitems);
        unset($itemlinks);
        $data['delete'] = xarModURL('changelog','admin','delete',
                                    array('modid' => $modid,
                                          'itemtype' => $itemtype,
                                          'editor' => $editor));
        $data['sortlink'] = array();
        if (empty($sort) || $sort == 'itemid') {
             $data['sortlink']['itemid'] = '';
        } else {
             $data['sortlink']['itemid'] = xarModURL('changelog','admin','view',
                                                     array('modid' => $modid,
                                                           'itemtype' => $itemtype,
                                                           'editor' => $editor));
        }
        if (!empty($sort) && $sort == 'numchanges') {
             $data['sortlink']['numchanges'] = '';
        } else {
             $data['sortlink']['numchanges'] = xarModURL('changelog','admin','view',
                                                         array('modid' => $modid,
                                                               'itemtype' => $itemtype,
                                                               'editor' => $editor,
                                                               'sort' => 'numchanges'));
        }
    }

    return $data;
}

?>
