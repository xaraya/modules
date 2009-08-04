<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 */
 /**
 * Manage crispBB hooks
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @access public
 */
function crispbb_admin_modifyhooks($args)
{
    // Admin only function
    if (!xarSecurityCheck('AdminCrispBB')) return;
    if (!xarVarFetch('sublink', 'str:1:', $sublink, '', XARVAR_NOT_REQUIRED)) return;
    if(!xarVarFetch('modid',    'isset', $modid,     NULL, XARVAR_DONT_SET)) return;
    if(!xarVarFetch('itemtype', 'isset', $itemtype,  NULL, XARVAR_DONT_SET)) return;
    if(!xarVarFetch('itemid',   'isset', $itemid,    NULL, XARVAR_DONT_SET)) return;
    if(!xarVarFetch('sort',     'isset', $sort,      NULL, XARVAR_DONT_SET)) return;
    if(!xarVarFetch('startnum', 'isset', $startnum,     1, XARVAR_NOT_REQUIRED)) {return;}

    $now = time();
    $tracking = xarModAPIFunc('crispbb', 'user', 'tracking', array('now' => $now));
    // End Tracking
    if (!empty($tracking)) {
        xarVarSetCached('Blocks.crispbb', 'tracking', $tracking);
        xarModSetUserVar('crispbb', 'tracking', serialize($tracking));
    }
    $pageTitle = xarML('Manage Hooks');

    $data = array();

    $modlist = xarModAPIFunc('crispbb', 'user','gethookmodules');

    if (empty($modid)) {
        $data['moditems'] = array();
        $data['numitems'] = 0;
        $data['numlinks'] = 0;
        foreach ($modlist as $modid => $itemtypes) {
            $modinfo = xarModGetInfo($modid);
            // Get the list of all item types for this module (if any)
            $mytypes = xarModAPIFunc($modinfo['name'],'user','getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(), 0);
            foreach ($itemtypes as $itemtype => $stats) {
                $moditem = array();
                $moditem['numitems'] = $stats['numitems'];
                $moditem['numtopics'] = $stats['numtopics'];
                $moditem['numlinks'] = $stats['numlinks'];
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
                $moditem['link'] = xarModURL('crispbb','admin','modifyhooks',
                                             array('modid' => $modid,
                                                   'itemtype' => empty($itemtype) ? null : $itemtype));
                $moditem['delete'] = xarModURL('crispbb','admin','unlinkhooks',
                                               array('modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype));
                $data['moditems'][] = $moditem;
                $data['numitems'] += $moditem['numitems'];
                $data['numlinks'] += $moditem['numlinks'];
            }
        }
        $data['delete'] = xarModURL('crispbb','admin','unlinkhooks');
    } else {
        $modinfo = xarModGetInfo($modid);
        $data['module'] = $modinfo['name'];
        if (empty($itemtype)) {
            $data['itemtype'] = 0;
            $data['modname'] = ucwords($modinfo['displayname']);
            $itemtype = null;
            if (isset($modlist[$modid][0])) {
                $stats = $modlist[$modid][0];
            }
        } else {
            $data['itemtype'] = $itemtype;
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
            $data['numitems'] = $stats['numitems'];
            $data['numlinks'] = $stats['numlinks'];
        } else {
            $data['numitems'] = 0;
            $data['numlinks'] = '';
        }
        $numstats = xarModGetVar('crispbb','hooksperpage');
        if (empty($numstats)) {
            $numstats = 100;
        }

        if ($numstats < $data['numlinks']) {
            $data['pager'] = xarTplGetPager($startnum,
                                            $data['numlinks'],
                                            xarModURL('crispbb','admin','modifyhooks',
                                                      array('modid' => $modid,
                                                            'itemtype' => $itemtype,
                                                            'sort' => $sort,
                                                            'startnum' => '%%')),
                                            $numstats);
        } else {
            $data['pager'] = '';
        }
        $data['modid'] = $modid;

        $topics = xarModAPIFunc('crispbb', 'user', 'gettopics',
            array(
                'startnum' => $startnum,
                'numitems' => $numstats,
                'hookmodid' => $modid,
                'hooktype' => $itemtype,
                'tstatus' => array(0,1,2,4,5)
            ));
        $data['topics'] = $topics;
        $data['unlinkhooksurl'] = xarModURL('crispbb','admin','unlinkhooks',
                                    array('modid' => $modid,
                                          'itemtype' => $itemtype));
    }

    $data['menulinks'] = xarModAPIFunc('crispbb', 'admin', 'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'modifyhooks',
            'current_sublink' => $sublink,
        ));

    // store function name for use by admin-main as an entry point
    xarSessionSetVar('crispbb_adminstartpage', 'modifyhooks');
    xarTPLSetPageTitle(xarVarPrepForDisplay($pageTitle));

    return $data;
}
?>