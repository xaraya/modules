<?php
/**
 * RSS feed with themes
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
 * Add an extension and request an ID
 *
 * @param enum phase Phase we are at
 *
 * @return array
 * @author Release module development team
 */
function release_user_rssviewthemes()
{
    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    $data['items'] = [];

    // The user API function is called.
    $items = xarMod::apiFunc(
        'release',
        'user',
        'getallrssmodsnotes',
        ['exttype' => 2]
    ); //themes

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        // The user API function is called.
        $getid = xarMod::apiFunc(
            'release',
            'user',
            'getid',
            ['eid' => $items[$i]['eid']]
        );

        $items[$i]['regname'] = xarVar::prepForDisplay($getid['regname']);

        $items[$i]['displname'] = xarVar::prepForDisplay($getid['displname']);

        $items[$i]['displaylink'] =  xarController::URL(
            'release',
            'user',
            'displaynote',
            ['rnid' => $item['rnid']],
            '1'
        );

        $items[$i]['desc'] = nl2br(xarVar::prepForDisplay($getid['desc']));
    }


    // Add the array of items to the template variables
    $data['items'] = $items;

    // Return the template variables defined in this function
    return $data;
}
