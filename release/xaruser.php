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

function release_user_main()
{
    if (!xarSecAuthAction(0, 'Reccomend::', '::', ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }
    
    $theme = xarVarCleanFromInput('theme');
        
    return array();

}

function release_user_viewids()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $data = array();

    // Load API
    if (!xarModAPILoad('release', 'user')) return;

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
        $items[$i]['desc'] = nl2br(xarVarPrepHTMLDisplay($item['desc']));
        $items[$i]['type'] = xarVarPrepForDisplay($item['type']);
    }

    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;
}

function release_user_addid()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $phase = xarVarCleanFromInput('phase');

    if (empty($phase)){
        $phase = 'add';
    }

    switch(strtolower($phase)) {

        case 'add':
        default:

            $data['uid'] = xarUserGetVar('uid');
            $data['authid'] = xarSecGenAuthKey();

            break;
        
        case 'update':

            list($rid,
                 $uid,
                 $name,
                 $desc,
                 $idtype) = xarVarCleanFromInput('rid',
                                                 'uid',
                                                 'name',
                                                 'desc',
                                                 'idtype');

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) {
                $msg = xarML('Invalid authorization key for item',
                            'users');
                xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                               new SystemException($msg));
                return;
            }

            // Load API
            if (!xarModAPILoad('release', 'user')) return;

            // The user API function is called. 
            if (!xarModAPIFunc('release',
                               'user',
                               'createid',
                                array('rid' => $rid,
                                      'name' => $name,
                                      'desc' => $desc,
                                      'type' => $idtype))) return;

            xarResponseRedirect(xarModURL('release', 'user', 'viewids'));

            return true;

            break;
    }   
    
    return $data;
}

?>
