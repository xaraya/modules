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
    if (!xarSecurity::check('AdminCrispBB')) {
        return;
    }
    if (!xarVar::fetch('sublink', 'str:1:', $sublink, '', xarVar::NOT_REQUIRED)) {
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
    if (!xarVar::fetch('startnum', 'isset', $startnum, 1, xarVar::NOT_REQUIRED)) {
        return;
    }

    $now = time();

    $pageTitle = xarML('Manage Hooks');

    $data = array();

    $modlist = xarMod::apiFunc('crispbb', 'user', 'gethookmodules');

    if (empty($modid)) {
        $data['moditems'] = array();
        $data['numitems'] = 0;
        $data['numlinks'] = 0;
        foreach ($modlist as $modid => $itemtypes) {
            $modinfo = xarMod::getInfo($modid);
            // Get the list of all item types for this module (if any)
            $mytypes = xarMod::apiFunc(
                $modinfo['name'],
                'user',
                'getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(),
                0
            );
            foreach ($itemtypes as $itemtype => $stats) {
                $moditem = array();
                $moditem['numitems'] = $stats['numitems'];
                $moditem['numtopics'] = $stats['numtopics'];
                $moditem['numlinks'] = $stats['numlinks'];
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
                    'crispbb',
                    'admin',
                    'modifyhooks',
                    array('modid' => $modid,
                                                   'itemtype' => empty($itemtype) ? null : $itemtype)
                );
                $moditem['delete'] = xarController::URL(
                    'crispbb',
                    'admin',
                    'unlinkhooks',
                    array('modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype)
                );
                $data['moditems'][] = $moditem;
                $data['numitems'] += $moditem['numitems'];
                $data['numlinks'] += $moditem['numlinks'];
            }
        }
        $data['delete'] = xarController::URL('crispbb', 'admin', 'unlinkhooks');
    } else {
        $modinfo = xarMod::getInfo($modid);
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
            $mytypes = xarMod::apiFunc(
                $modinfo['name'],
                'user',
                'getitemtypes',
                                     // don't throw an exception if this function doesn't exist
                                     array(),
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
            $data['numitems'] = $stats['numitems'];
            $data['numlinks'] = $stats['numlinks'];
        } else {
            $data['numitems'] = 0;
            $data['numlinks'] = '';
        }
        $numstats = xarModVars::get('crispbb', 'hooksperpage');
        if (empty($numstats)) {
            $numstats = 100;
        }

        if ($numstats < $data['numlinks']) {
            $data['pager'] = xarTplPager::getPager(
                $startnum,
                $data['numlinks'],
                xarController::URL(
                                                'crispbb',
                                                'admin',
                                                'modifyhooks',
                                                array('modid' => $modid,
                                                            'itemtype' => $itemtype,
                                                            'sort' => $sort,
                                                            'startnum' => '%%')
                                            ),
                $numstats
            );
        } else {
            $data['pager'] = '';
        }
        $data['modid'] = $modid;

        $topics = xarMod::apiFunc(
            'crispbb',
            'user',
            'gettopics',
            array(
                'startnum' => $startnum,
                'numitems' => $numstats,
                'hookmodid' => $modid,
                'hooktype' => $itemtype,
                'tstatus' => array(0,1,2,4,5)
            )
        );
        $data['topics'] = $topics;
        $data['unlinkhooksurl'] = xarController::URL(
            'crispbb',
            'admin',
            'unlinkhooks',
            array('modid' => $modid,
                                          'itemtype' => $itemtype)
        );
    }

    $data['menulinks'] = xarMod::apiFunc(
        'crispbb',
        'admin',
        'getmenulinks',
        array(
            'current_module' => 'crispbb',
            'current_type' => 'admin',
            'current_func' => 'modifyhooks',
            'current_sublink' => $sublink,
        )
    );

    // store function name for use by admin-main as an entry point
    xarSession::setVar('crispbb_adminstartpage', 'modifyhooks');
    xarTPLSetPageTitle(xarVar::prepForDisplay($pageTitle));

    return $data;
}
