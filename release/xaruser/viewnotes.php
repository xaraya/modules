<?php

function release_user_viewnotes()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

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
        $phase = 'viewall';
    }

    switch(strtolower($phase)) {

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
                                        'certified'=> 2));
            
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
                                        'price'    => 2));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

            break;

        case 'free':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('roles',
                                                                  'itemsperpage'),
                                        'price'    => 1));
            
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
                                        'supported'=> 2));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

            break;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        // The user API function is called.
        $getid = xarModAPIFunc('release',
                               'user',
                               'getid',
                               array('rid' => $items[$i]['rid']));


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

        $items[$i]['type'] = xarVarPrepForDisplay($getid['type']);
        $items[$i]['name'] = xarVarPrepForDisplay($getid['name']);
        $items[$i]['realname'] = $getuser['name'];
        $items[$i]['desc'] = nl2br(xarVarPrepHTMLDisplay($getid['desc']));
        $items[$i]['notes'] = nl2br(xarVarPrepHTMLDisplay($item['notes']));

    }


    // Add the array of items to the template variables
    $data['items'] = $items;

    // TODO : add a pager (once it exists in BL)
    $data['pager'] = '';

    // Return the template variables defined in this function
    return $data;

}

?>