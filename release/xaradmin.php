<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: John Cox via phpMailer Team
// Purpose of file: standard mail output
// ----------------------------------------------------------------------

function release_admin_main()
{
    if (!xarSecAuthAction(0, 'Reccomend::', '::', ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }
    
    $theme = xarVarCleanFromInput('theme');
        
    return array();

}

function release_admin_viewnotes()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get parameters
    list($startnum,
         $phase,
         $filter,
         $type) = xarVarCleanFromInput('startnum',
                                       'phase',
                                       'filter',
                                       'type');
    
    $uid = xarUserGetVar('uid');
    $data['items'] = array();

    if (empty($phase)){
        $phase = 'unapproved';
    }

    switch(strtolower($phase)) {

        case 'unapproved':
        default:

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('users',
                                                                  'itemsperpage'),
                                        'approved' => 1));
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

            break;

        case 'viewall':
        default:

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('users',
                                                                  'itemsperpage'),
                                        'approved' => 2));
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

            break;

        case 'certified':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('users',
                                                                  'itemsperpage'),
                                        'certified'=> $filter));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

            break;

        case 'price':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('users',
                                                                  'itemsperpage'),
                                        'price'    => $filter));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

            break;

        case 'supported':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('users',
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

        if (xarSecAuthAction(0, 'release::', "::", ACCESS_EDIT)) {
            $items[$i]['editurl'] = xarModURL('release',
                                              'admin',
                                              'modify',
                                              array('rid' => $item['rid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecAuthAction(0, 'release::', "::", ACCESS_DELETE)) {
            $items[$i]['deleteurl'] = xarModURL('release',
                                               'admin',
                                               'delete',
                                               array('rid' => $item['rid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Delete');


        // The user API function is called.
        $getid = xarModAPIFunc('release',
                               'user',
                               'getid',
                               array('rid' => $items[$i]['rid']));

        $items[$i]['type'] = xarVarPrepForDisplay($getid['type']);
        $items[$i]['name'] = xarVarPrepForDisplay($getid['name']);
        $items[$i]['displaylink'] =  xarModURL('release',
                                          'user',
                                          'displaynote',
                                           array('rid' => $item['rid']));

        $getuser = xarModAPIFunc('users',
                                 'user',
                                 'get',
                                  array('uid' => $getid['uid']));

        $items[$i]['contacturl'] = xarModURL('users',
                                             'user',
                                             'display',
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