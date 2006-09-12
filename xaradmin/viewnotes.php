<?php
/**
 * View notes
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * view notes
 * 
 * @param $startnum
 * @param $phase
 * @param $filter
 * @param $type
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_admin_viewnotes()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase',    'str:1:', $phase, 'all', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter',   'str:1:', $filter, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('exttype', 'str:1:',  $exttype, 0, XARVAR_NOT_REQUIRED)) return;
    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;

    $uid = xarUserGetVar('uid');
    $data['items'] = array();

    if (empty($phase)){
        $phase = 'unapproved';
    }

    switch(strtolower($phase)) {

        case 'unapproved':
        default:

            // The user API function is called.
            $items = xarModAPIFunc('release', 'user', 'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('roles',
                                                                  'itemsperpage'),
                                        'unapproved' => 1));
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Pending');
            break;

        case 'viewall':

            // The user API function is called.
            $items = xarModAPIFunc('release', 'user', 'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('roles',
                                                                  'itemsperpage'),
                                        'approved' => 2));
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('All Approved');
            break;

        case 'certified':

            // The user API function is called.
            $items = xarModAPIFunc('release', 'user', 'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('roles',
                                                                  'itemsperpage'),
                                        'certified'=> $filter));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }
            if ($filter == 1) {
               $phasedesc =xarML('Non-Certified');
            }else{
               $phasedesc =xarML('Certified');
            }
            break;

        case 'price':

            // The user API function is called.
            $items = xarModAPIFunc('release', 'user', 'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('roles',
                                                                  'itemsperpage'),
                                        'price'    => $filter));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }
            if ($filter == 1) {
               $phasedesc =xarML('Free');
            }else{
               $phasedesc =xarML('Commercial');
            }

            break;

        case 'supported':

            // The user API function is called.
            $items = xarModAPIFunc('release', 'user', 'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('roles',
                                                                  'itemsperpage'),
                                        'supported'=> $filter));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }
             if ($filter == 1) {
               $phasedesc =xarML('Not Supported');
            }else{
               $phasedesc =xarML('Supported');
            }

            break;
    }
    $numitems=count($items);
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < $numitems; $i++) {
        $item = $items[$i];

        if (xarSecurityCheck('EditRelease', 0)) {
            $items[$i]['editurl'] = xarModURL('release', 'admin', 'modifynote',
                                              array('rnid' => $item['rnid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteRelease', 0)) {
            $items[$i]['deleteurl'] = xarModURL('release', 'admin', 'deletenote',
                                               array('rnid' => $item['rnid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Delete');


        // The user API function is called.
        $getid = xarModAPIFunc('release', 'user', 'getid',
                               array('rid' => $items[$i]['rid']));

        $items[$i]['exttype'] = xarVarPrepForDisplay($getid['exttype']);
        $items[$i]['regname'] = xarVarPrepForDisplay($getid['regname']);
        $items[$i]['displaylink'] =  xarModURL('release', 'user', 'displaynote',
                                           array('rnid' => $item['rnid']));

        $getuser = xarModAPIFunc('roles', 'user', 'get',
                                  array('uid' => $getid['uid']));

        $items[$i]['contacturl'] = xarModURL('roles', 'user', 'display',
                                              array('uid' => $getid['uid']));


        $items[$i]['realname'] = $getuser['name'];
        $items[$i]['desc'] = xarVarPrepForDisplay($getid['desc']);

        if ($item['certified'] == 1){
            $items[$i]['certifiedstatus'] = xarML('Yes');
        } else {
            $items[$i]['certifiedstatus'] = xarML('No');
        }
        $items[$i]['changelog'] = nl2br($item['changelog']);
        $items[$i]['notes'] = nl2br($item['notes']);

     $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('release', 'user', 'countnotes',array('phase'=>$phase,'filter'=>$filter)),
        xarModURL('release', 'admin', 'viewnotes', array('startnum' => '%%','phase'=>$phase, 'filter'=>$filter)),
        xarModGetUserVar('release', 'itemsperpage', $uid));
    }

    $data['phase'] = $phasedesc;
    // Add the array of items to the template variables
    $data['items'] = $items;
    $data['numitems']=$numitems;
    $data['phasedesc']=$phasedesc;

    // Return the template variables defined in this function
    return $data;

}
?>