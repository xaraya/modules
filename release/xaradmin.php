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

    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;
        
    return array();

}

function release_admin_viewids()
{
    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;

    $data = array();

    // The user API function is called. 
    $items = xarModAPIFunc('release',
                           'user',
                           'getallids');

    if (empty($items)) {
        $msg = xarML('There are no items to display in the release module');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        $items[$i]['rid'] = xarVarPrepForDisplay($item['rid']);
        $items[$i]['name'] = xarVarPrepForDisplay($item['name']);

        if (xarSecurityCheck('EditRelease', 0)) {
            $items[$i]['editurl'] = xarModURL('release',
                                              'user',
                                              'modifyid',
                                              array('rid' => $item['rid']));
        } else {
            $items[$i]['editurl'] = '';
        }

        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteRelease', 0)) {
            $items[$i]['deleteurl'] = xarModURL('release',
                                               'admin',
                                               'deleteid',
                                               array('rid' => $item['rid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        $items[$i]['deletetitle'] = xarML('Delete');

    }

    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;
}

function release_admin_viewnotes()
{
    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;

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
                                        'numitems' => xarModGetVar('roles',
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
                                        'numitems' => xarModGetVar('roles',
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
                                        'numitems' => xarModGetVar('roles',
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
                                        'numitems' => xarModGetVar('roles',
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
            $items[$i]['editurl'] = xarModURL('release',
                                              'admin',
                                              'modifynote',
                                              array('rnid' => $item['rnid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteRelease', 0)) {
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

        $getuser = xarModAPIFunc('roles',
                                 'user',
                                 'get',
                                  array('uid' => $getid['uid']));

        $items[$i]['contacturl'] = xarModURL('roles',
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
    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;

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
                $data['message'] = xarML('There are no pending release notes');
            }

            break;

        case 'viewall':
        default:

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
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
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
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
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
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
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
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
            $items[$i]['editurl'] = xarModURL('release',
                                              'admin',
                                              'modifynote',
                                              array('rnid' => $item['rnid']));
        } else {
            $items[$i]['editurl'] = '';
        }
        $items[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteRelease', 0)) {
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

        $getuser = xarModAPIFunc('roles',
                                 'user',
                                 'get',
                                  array('uid' => $getid['uid']));

        $items[$i]['contacturl'] = xarModURL('roles',
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
    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;

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
            $user = xarModAPIFunc('roles',
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

    // Security Check
    if(!xarSecurityCheck('DeleteRelease')) return;

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

function release_admin_deleteid()
{
    // Get parameters
    list($rid,
         $confirmation) = xarVarCleanFromInput('rid',
                                              'confirmation');

    // The user API function is called.
    $data = xarModAPIFunc('release',
                          'user',
                          'getid',
                          array('rid' => $rid));

    if ($data == false) return;

    // Security Check
    if(!xarSecurityCheck('DeleteRelease')) return;

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
		               'deleteid', 
                        array('rid' => $rid))) return;

    // Redirect
    xarResponseRedirect(xarModURL('release', 'admin', 'viewids'));

    // Return
    return true;
}

?>