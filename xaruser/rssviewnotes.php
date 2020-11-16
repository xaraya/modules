<?php
/**
 * Get extension releases
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */

function release_user_rssviewnotes()
{
    if (!xarVar::fetch('releaseno', 'int:0:', $releaseno, null, xarVar::DONT_SET)) {
        return;
    }
    // Security Check
    if (!xarSecurity::check('OverviewRelease')) {
        return;
    }

    $data['items'] = array();


    // The user API function is called.
    $items = xarMod::apiFunc('release', 'user', 'getallrssextnotes', array('releaseno'=>$releaseno));

    $totalitems=count($items);
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < $totalitems; $i++) {
        $item = $items[$i];

        // The user API function is called.
        $getid = xarMod::apiFunc(
            'release',
            'user',
            'getid',
            array('rid' => $items[$i]['rid'])
        );

        $items[$i]['regname'] = xarVar::prepForDisplay($getid['regname']);

        $items[$i]['displaylink'] =  xarController::URL(
            'release',
            'user',
            'displaynote',
            array('rnid' => $item['rnid']),
            '1'
        );

        $items[$i]['desc'] = nl2br(xarVar::prepForDisplay($getid['desc']));
    }

    // Add the array of items to the template variables
    $data['items'] = $items;

    // Return the template variables defined in this function
    return $data;
}
