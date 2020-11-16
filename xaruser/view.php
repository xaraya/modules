<?php
/**
 * Main view for Releases
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * @author niceguyeddie
 * @author jojodee
 * @param int $exttypes
 * @param enum sort - sort criteria
 * @TODO : sort ok but need to make sticky over categories etc ...and vice versa
 */
function release_user_view()
{
    return array();
    if (!xarVar::fetch('startnum', 'int:1:', $startnum, 1, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('phase', 'str:1:', $phase, 'all', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('catid', 'int', $catid, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('sort', 'str', $sort, 'id', xarVar::NOT_REQUIRED)) {
        return;
    }
    // Default parameters
    if (!isset($startnum)) {
        $startnum = 1;
    }
    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    $uid = xarUser::getVar('id');


    $xarexttypes = xarMod::apiFunc('release', 'user', 'getexttypes');
    foreach ($xarexttypes as $k=>$v) {
        $testv = strtolower($v);
        if ($phase == $testv) {
            $exttype=$k;
        }
    }

    $data = array();
    if (empty($sort)) {
        $sort = 'id';
    }

    // The user API function is called to get all extension IDs.
    $items = xarMod::apiFunc(
        'release',
        'user',
        'getallrids',
        array('exttype'  => $exttype,
                         'catid'     => $catid,
                         'sort'      => $sort,
                         'startnum'  => $startnum,
                         'numitems'  => xarModUserVars::get(
                             'release',
                             'itemsperpage',
                             $uid
                         ),
                          )
    );


    $numitems = count($items);

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < ($numitems); $i++) {
        $item = $items[$i];
        $items[$i]['author'] = xarVar::prepForDisplay($item['author']);
        // Basic Information
        $items[$i]['rid'] = xarVar::prepForDisplay($item['rid']);
        $items[$i]['regname'] = xarVar::prepForDisplay($item['regname']);
        $items[$i]['displname'] = xarVar::prepForDisplay($item['displname']);
        $items[$i]['rstate'] = xarVar::prepForDisplay($item['rstate']);
        /* use the xarUser::getVar func as we only want name
         * TODO: Where is this user taken to?
         */
        $getuser = xarMod::apiFunc(
            'roles',
            'user',
            'get',
            array('uid' => $item['uid'])
        );

        // Author Name and Contact URL

        $items[$i]['contacturl'] = xarController::URL(
            'roles',
            'user',
            'display',
            array('uid' => $item['uid'])
        );

        // InfoURL
        $items[$i]['infourl'] = xarController::URL(
            'release',
            'user',
            'display',
            array('eid' => $item['eid'],
                                                'phase' => 'view',
                                                'tab'  => 'basic'
                                          )
        );
        $items[$i]['infotitle'] = xarML('View');

        // Edit
        if (($uid == $item['uid']) or (xarSecurity::check('EditRelease', 0))) {
            $items[$i]['editurl'] = xarController::URL(
                'release',
                'user',
                'modifyid',
                array('eid' => $item['eid'])
            );
            $items[$i]['edittitle'] = xarML('Edit');
        } else {
            $items[$i]['edittitle'] = '';
            $items[$i]['editurl'] = '';
        }
        // Delete
        if (($uid == $item['uid']) or (xarSecurity::check('ManageRelease', 0))) {
            $items[$i]['delurl'] = xarController::URL(
                'release',
                'admin',
                'deleteid',
                array('eid' => $item['eid'])
            );
            $items[$i]['deltitle'] = xarML('Delete');
        } else {
            $items[$i]['deltitle'] = '';
            $items[$i]['delurl'] = '';
        }
        // Add Release Note URL
        if (($uid == $item['uid']) or (xarSecurity::check('EditRelease', 0))) {
            $items[$i]['addurl'] = xarController::URL(
                'release',
                'user',
                'addnotes',
                array('eid' => $item['eid'],
                                                     'phase' => 'start')
            );
            $items[$i]['addtitle'] = xarML('Add');
        } else {
            $items[$i]['addurl'] = '';
            $items[$i]['addtitle'] = '';
        }

        // Add Docs URL
        if (($uid == $item['uid']) or (xarSecurity::check('EditRelease', 0))) {
            $items[$i]['adddocs'] = xarController::URL(
                'release',
                'user',
                'adddocs',
                array('eid' => $item['eid'],
                                                     'phase' => 'start')
            );
            $items[$i]['adddocstitle'] = xarML('Add');
        } else {
            $items[$i]['adddocs'] = '';
            $items[$i]['adddocstitle'] = '';
        }

        $items[$i]['comments'] = '0';
        if (xarMod::isAvailable('comments')) {
            // Get Comments
            $items[$i]['comments'] = xarMod::apiFunc(
                'comments',
                'user',
                'get_count',
                array('modid' => xarMod::getRegId('release'),
                                                        'itemtype' => $item['exttype'],
                                                         'objectid' => (int)$item['eid'])
            );

            if ($items[$i]['comments'] != '0') {
                $items[$i]['comments'] .= ' ';
            }
        }

        $items[$i]['hitcount'] = '0';
        if (xarMod::isAvailable('hitcount')) {
            // Get Hits
            $items[$i]['hitcount'] = xarMod::apiFunc(
                'hitcount',
                'user',
                'get',
                array('modid' => xarMod::getRegId('release'),
                                                         'itemtype' => $item['exttype'],
                                                         'objectid' => (int)$item['eid'])
            );

            if ($items[$i]['hitcount'] != '0') {
                $items[$i]['hitcount'] .= ' ';
            }
        }

        $items[$i]['docs'] = xarMod::apiFunc(
            'release',
            'user',
            'countdocs',
            array('eid' => $item['eid'])
        );

        //Get some info for the extensions state
        foreach ($stateoptions as $key => $value) {
            if ($key==$items[$i]['rstate']) {
                $items[$i]['extstate']=$stateoptions[$key];
            }
        }

        $allitems = xarMod::apiFunc('release', 'user', 'countitems', array('exttype'=>$exttype,'catid'=>$catid));

        $data['pager'] = xarTplPager::getPager(
            $startnum,
            $allitems,
            xarController::URL(
               'release',
               'user',
               'view',
               array('startnum' => '%%',
                     'exttype'=>$exttype,
                     'catid'=>$catid,
                     'sort'=>$sort)
           ),
            xarModUserVars::get('release', 'itemsperpage', $uid)
        );
    }
    if (!isset($allitems)) {
        $allitems=0;
    }
    $data['sort'] = $sort;
    $data['numitems']=$allitems;
    $data['phase']=$phase;
    $data['catid'] = $catid;
    $data['exttype'] = $exttype;
    // Add the array of items to the template variables
    $data['items'] = $items;
    
    return $data;
}
