<?php
/**
 * View the documentation
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 */
/**
 * View the Documentation
 * 
 * @param 
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_admin_viewdocs()
{
    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;

    // Get parameters
    if (!xarVarFetch('phase', 'enum:unapproved:viewall:certified:price:supported', $phase, 'unapproved', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'int', $starnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('filter', 'str', $filter, $filter, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('type', 'str', $type, $type, XARVAR_NOT_REQUIRED)) return;

    $data['items'] = array();

    if (empty($phase)){
        $phase = 'unapproved';
    }

    switch(strtolower($phase)) {

        case 'unapproved':
        default:

            $items = xarModAPIFunc('release',  'user', 'getdocs',
                                    array('approved' => 1));

            if ($items == false){
                $data['message'] = xarML('There are no pending release notes');
            }

            break;

        case 'viewall':
        default:

            // The user API function is called.
            $items = xarModAPIFunc('release',  'user', 'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('roles',
                                                                  'itemsperpage'),
                                        'approved' => 2));
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

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

            break;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        if (xarSecurityCheck('EditRelease', 0)) {
            $items[$i]['editurl'] = xarModURL('release', 'admin','modifynote',
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

        $items[$i]['type'] = xarVarPrepForDisplay($getid['type']);
        $items[$i]['regname'] = xarVarPrepForDisplay($getid['regname']);
        $items[$i]['displaylink'] =  xarModURL('release',
                                          'user',
                                          'displaynote',
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
    }


    // Add the array of items to the template variables
    $data['items'] = $items;

    // TODO : add a pager (once it exists in BL)
    $data['pager'] = '';

    // Return the template variables defined in this function
    return $data;

}
?>