<?php

function release_user_viewids()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    $phase = xarVarCleanFromInput('phase');

    if (empty($phase)){
        $phase = 'all';
    }

    $data = array();

    switch(strtolower($phase)) {

        case 'all':
        default:

            // The user API function is called. 
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallids');
            break;

        case 'themes':

            // The user API function is called. 
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getthemeids');
            break;

        case 'modules':

            // The user API function is called. 
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getmoduleids');
            break;
    }


    if (empty($items)) {
        $msg = xarML('There are no items to display in the release module');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $uid = xarUserGetVar('uid');

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        // Basic Information
        $items[$i]['rid'] = xarVarPrepForDisplay($item['rid']);
        $items[$i]['name'] = xarVarPrepForDisplay($item['name']);

        $getuser = xarModAPIFunc('roles',
                                 'user',
                                 'get',
                                  array('uid' => $item['uid']));

        // Author Name and Contact URL
        $items[$i]['author'] = $getuser['name'];
        $items[$i]['contacturl'] = xarModURL('roles',
                                             'user',
                                             'display',
                                              array('uid' => $item['uid']));
        
        // InfoURL
        $items[$i]['infourl'] = xarModURL('release',
                                          'user',
                                          'display',
                                          array('rid' => $item['rid']));
        $items[$i]['infotitle'] = xarML('View');

        // Edit
        if (($uid == $item['uid']) or (xarSecurityCheck('EditRelease', 0))) {
            $items[$i]['editurl'] = xarModURL('release',
                                              'user',
                                              'modifyid',
                                               array('rid' => $item['rid']));
            $items[$i]['edittitle'] = xarML('Edit');
        } else {
            $items[$i]['edittitle'] = '';
            $items[$i]['editurl'] = '';
        }
        
        // Add Release Note URL
        if (($uid == $item['uid']) or (xarSecurityCheck('EditRelease', 0))) {
            $items[$i]['addurl'] = xarModURL('release',
                                              'user',
                                              'addnotes',
                                               array('rid' => $item['rid'],
                                                     'phase' => 'start'));
            $items[$i]['addtitle'] = xarML('Add');
        } else {
            $items[$i]['addurl'] = '';
            $items[$i]['addtitle'] = '';
        }

        // Add Docs URL
        if (($uid == $item['uid']) or (xarSecurityCheck('EditRelease', 0))) {
            $items[$i]['adddocs'] = xarModURL('release',
                                              'user',
                                              'adddocs',
                                               array('rid' => $item['rid'],
                                                     'phase' => 'start'));
            $items[$i]['adddocstitle'] = xarML('Add');
        } else {
            $items[$i]['adddocs'] = '';
            $items[$i]['adddocstitle'] = '';
        }

        $items[$i]['comments'] = '0';
        if (xarModIsAvailable('comments')){
            // Get Comments
            $items[$i]['comments'] = xarModAPIFunc('comments',
                                                   'user',
                                                   'get_count',
                                                   array('modid' => xarModGetIDFromName('release'),
                                                         'objectid' => $item['rid']));
            
            if ($items[$i]['comments'] != '0') {
                $items[$i]['comments'] .= ' ';
            }
        }

        $items[$i]['hitcount'] = '0';
        if (xarModIsAvailable('hitcount')){
            // Get Hits
            $items[$i]['hitcount'] = xarModAPIFunc('hitcount',
                                                   'user',
                                                   'get',
                                                   array('modname' => 'release',
                                                         'itemtype' => '1',
                                                         'objectid' => $item['rid']));
            
            if ($items[$i]['hitcount'] != '0') {
                $items[$i]['hitcount'] .= ' ';
            }
        }

        $items[$i]['docs'] = xarModAPIFunc('release',
                                           'user',
                                           'countdocs',
                                           array('rid' => $item['rid']));
    }

    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;

}

?>