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
                                              'modifynote',
                                              array('rnid' => $item['rnid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecAuthAction(0, 'release::', "::", ACCESS_DELETE)) {
            $items[$i]['deleteurl'] = xarModURL('release',
                                               'admin',
                                               'deletenote',
                                               array('rnid' => $item['rnid']));
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
                                           array('rnid' => $item['rnid']));

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

function release_admin_viewdocs()
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
    
    $data['items'] = array();

    if (empty($phase)){
        $phase = 'unapproved';
    }

    switch(strtolower($phase)) {

        case 'unapproved':
        default:

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('approved' => 1));

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
                                              'modifynote',
                                              array('rnid' => $item['rnid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecAuthAction(0, 'release::', "::", ACCESS_DELETE)) {
            $items[$i]['deleteurl'] = xarModURL('release',
                                               'admin',
                                               'deletenote',
                                               array('rnid' => $item['rnid']));
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
                                           array('rnid' => $item['rnid']));

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

function release_admin_modifynote()
{
    // Security check
    if (!xarSecAuthAction(0, 'release::', '::', ACCESS_EDIT)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    list ($phase,
          $rnid) = xarVarCleanFromInput('phase',
                                        'rnid');

    if (empty($phase)){
        $phase = 'modify';
    }

    switch(strtolower($phase)) {

        case 'modify':
        default:
            
            // The user API function is called.
            $data = xarModAPIFunc('release',
                                  'user',
                                  'getnote',
                                  array('rnid' => $rnid));

            if ($data == false) return;

            // The user API function is called.
            $id = xarModAPIFunc('release',
                                  'user',
                                  'getid',
                                  array('rid' => $data['rid']));

            if ($id == false) return;

            // The user API function is called.
            $user = xarModAPIFunc('users',
                                  'user',
                                  'get',
                                  array('uid' => $id['uid']));

            if ($id == false) return;


            $data['name'] = $id['name'];
            $data['username'] = $user['name'];
            $data['changelogf'] = nl2br($data['changelog']);
            $data['notesf'] = nl2br($data['notes']);
            $data['authid'] = xarSecGenAuthKey();

            break;
        
        case 'update':

            list($rid,
                 $name,
                 $version,
                 $pricecheck,
                 $supportcheck,
                 $democheck,
                 $dllink,
                 $price,
                 $demolink,
                 $supportlink,
                 $changelog,
                 $enotes,
                 $certified,
                 $approved,
                 $notes) = xarVarCleanFromInput('rid',
                                                'name',
                                                'version',
                                                'pricecheck',
                                                'supportcheck',
                                                'democheck',
                                                'dllink',
                                                'price',
                                                'demolink',
                                                'supportlink',
                                                'changelog',
                                                'enotes',
                                                'certified',
                                                'approved',
                                                'notes');
            
            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // The user API function is called. 
            if (!xarModAPIFunc('release',
                               'admin',
                               'updatenote',
                                array('rid'         => $rid,
                                      'rnid'        => $rnid,
                                      'version'     => $version,
                                      'price'       => $pricecheck,
                                      'supported'   => $supportcheck,
                                      'demo'        => $democheck,
                                      'dllink'      => $dllink,
                                      'priceterms'  => $price,
                                      'demolink'    => $demolink,
                                      'supportlink' => $supportlink,
                                      'changelog'   => $changelog,
                                      'notes'       => $notes,
                                      'enotes'      => $enotes,
                                      'certified'   => $certified,
                                      'approved'    => $approved))) return;


            xarResponseRedirect(xarModURL('release', 'admin', 'viewnotes'));

            return true;

            break;
    }   
    
    return $data;
}

function release_admin_deletenote()
{
    // Get parameters
    list($rnid,
         $confirmation) = xarVarCleanFromInput('rnid',
                                              'confirmation');

    // The user API function is called.
    $data = xarModAPIFunc('release',
                          'user',
                          'getnote',
                          array('rnid' => $rnid));

    if ($data == false) return;

    // Security check
    if (!xarSecAuthAction(0, 'users::Item', "$data[rid]::$rnid", ACCESS_DELETE)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Check for confirmation.
    if (empty($confirmation)) {
        //Load Template
        $data['authid'] = xarSecGenAuthKey();
        return $data;
    }

    // If we get here it means that the user has confirmed the action

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('release',
		               'admin',
		               'deletenote', 
                        array('rnid' => $rnid))) return;

    // Redirect
    xarResponseRedirect(xarModURL('release', 'admin', 'viewnotes'));

    // Return
    return true;
}

?>