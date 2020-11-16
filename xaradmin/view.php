<?php
/**
 * Ratings Module
 *
 * @package modules
 * @subpackage ratings module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/41.html
 * @author Jim McDonald
 */
/**
 * View statistics about ratings
 */
function ratings_admin_view()
{
    // Security Check
    if (!xarSecurity::check('AdminRatings')) {
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

    $data = array();

    if (empty($modid)) {
        $modlist = xarMod::apiFunc('ratings', 'user', 'getmodules');

        $data['moditems'] = array();
        $data['numitems'] = 0;
        $data['numratings'] = 0;
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
                $moditem['numitems'] = $stats['items'];
                $moditem['numratings'] = $stats['ratings'];
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
                    'ratings',
                    'admin',
                    'view',
                    array('modid' => $modid,
                                                   'itemtype' => empty($itemtype) ? null : $itemtype)
                );
                $moditem['delete'] = xarController::URL(
                    'ratings',
                    'admin',
                    'delete',
                    array('modid' => $modid,
                                                     'itemtype' => empty($itemtype) ? null : $itemtype)
                );
                $data['moditems'][] = $moditem;
                $data['numitems'] += $moditem['numitems'];
                $data['numratings'] += $moditem['numratings'];
            }
        }
        $data['delete'] = xarController::URL('ratings', 'admin', 'delete');
    } else {
        $modinfo = xarMod::getInfo($modid);
        if (empty($itemtype)) {
            $data['modname'] = ucwords($modinfo['displayname']);
            $itemtype = null;
        } else {
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
        }

        $data['modid'] = $modid;
        $data['moditems'] = xarMod::apiFunc(
            'ratings',
            'user',
            'getitems',
            array('modid' => $modid,
                                                'itemtype' => $itemtype,
                                                'sort' => $sort)
        );
        $data['numratings'] = 0;
        foreach ($data['moditems'] as $itemid => $moditem) {
            $data['numratings'] += $moditem['numratings'];
            $data['moditems'][$itemid]['delete'] = xarController::URL(
                'ratings',
                'admin',
                'delete',
                array('modid' => $modid,
                                                                   'itemtype' => $itemtype,
                                                                   'itemid' => $itemid)
            );
        }
        $data['delete'] = xarController::URL(
            'ratings',
            'admin',
            'delete',
            array('modid' => $modid,
                                          'itemtype' => $itemtype)
        );
        $data['sortlink'] = array();
        if (empty($sort) || $sort == 'itemid') {
            $data['sortlink']['itemid'] = '';
        } else {
            $data['sortlink']['itemid'] = xarController::URL(
                'ratings',
                'admin',
                'view',
                array('modid' => $modid,
                                                           'itemtype' => $itemtype)
            );
        }
        if (!empty($sort) && $sort == 'numratings') {
            $data['sortlink']['numratings'] = '';
        } else {
            $data['sortlink']['numratings'] = xarController::URL(
                'ratings',
                'admin',
                'view',
                array('modid' => $modid,
                                                               'itemtype' => $itemtype,
                                                               'sort' => 'numratings')
            );
        }
        if (!empty($sort) && $sort == 'rating') {
            $data['sortlink']['rating'] = '';
        } else {
            $data['sortlink']['rating'] = xarController::URL(
                'ratings',
                'admin',
                'view',
                array('modid' => $modid,
                                                           'itemtype' => $itemtype,
                                                           'sort' => 'rating')
            );
        }
    }

    return $data;
}
