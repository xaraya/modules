<?php
/**
 * Get extension releases
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */

function release_user_rssviewnotes()
{
     if(!xarVarFetch('releaseno',   'int:0:', $releaseno,  NULL, XARVAR_DONT_SET)) {return;}
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    $data['items'] = array();


    // The user API function is called.
    $items = xarModAPIFunc('release', 'user','getallrssextnotes',array('releaseno'=>$releaseno));

    $totalitems=count($items);
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < $totalitems; $i++) {
        $item = $items[$i];

        // The user API function is called.
        $getid = xarModAPIFunc('release', 'user', 'getid',
                               array('rid' => $items[$i]['rid']));

        $items[$i]['regname'] = xarVarPrepForDisplay($getid['regname']);

        $items[$i]['displaylink'] =  xarModURL('release', 'user', 'displaynote',
                                                array('rnid' => $item['rnid']),
                                                '1');

        $items[$i]['desc'] = nl2br(xarVarPrepForDisplay($getid['desc']));

    }


    // Add the array of items to the template variables
    $data['items'] = $items;

    // Return the template variables defined in this function
    return $data;

}

?>