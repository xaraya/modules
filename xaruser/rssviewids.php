<?php
/*
 * View RSS ids
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 * @TODO - get to get rid of the duplication and hard coded stuff in this
 */
function release_user_rssviewids()
{
    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    xarVar::fetch('phase', 'enum:all:themes:modules', $phase, 'all', xarVar::NOT_REQUIRED);

    if (empty($phase)) {
        $phase = 'all';
    }

    $data = [];

    switch (strtolower($phase)) {
        case 'all':
        default:

            // The user API function is called.
            $items = xarMod::apiFunc('release', 'user', 'getallids');
            break;

        case 'themes':

            // The user API function is called.
            $items = xarMod::apiFunc('release', 'user', 'getthemeids');
            break;

        case 'modules':

            // The user API function is called.
            $items = xarMod::apiFunc('release', 'user', 'getmoduleids');
            break;
    }



    if (empty($items)) {
        throw new EmptyParameterException(null, xarML('There are no items to display in the release module'));
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        // Basic Information
        $items[$i]['eid'] = xarVar::prepForDisplay($item['eid']);
        $items[$i]['rid'] = xarVar::prepForDisplay($item['rid']);
        $items[$i]['regname'] = xarVar::prepForDisplay($item['regname']);
        $items[$i]['displname'] = xarVar::prepForDisplay($item['displname']);
        $items[$i]['desc'] = xarVar::prepForDisplay($item['desc']);

        $getuser = xarMod::apiFunc(
            'roles',
            'user',
            'get',
            ['uid' => $item['uid']]
        );

        // Author Name and Contact URL
        $items[$i]['author'] = $getuser['name'];
        $items[$i]['contacturl'] = xarController::URL(
            'roles',
            'user',
            'display',
            ['uid' => $item['uid']]
        );

        // InfoURL
        $items[$i]['infourl'] = xarController::URL(
            'release',
            'user',
            'display',
            ['rid' => $item['rid']]
        );
    }

    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;
}
