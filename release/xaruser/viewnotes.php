<?php

function release_user_viewnotes()
{
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'str:1:', $phase, 'all', XARVAR_NOT_REQUIRED)) return;

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
                                        'numitems' => xarModGetVar('release',
                                                                  'itemsperpage'),
                                        'approved' => 2));
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('All');
            break;

        case 'certified':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('release',
                                                                  'itemsperpage'),
                                        'certified'=> 2));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Certified');
            break;

        case 'price':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('release',
                                                                  'itemsperpage'),
                                        'price'    => 2));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Commercial');
            break;

        case 'free':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('release',
                                                                  'itemsperpage'),
                                        'price'    => 1));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Free');
            break;

        case 'supported':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('release',
                                                                  'itemsperpage'),
                                        'supported'=> 2));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }
            $phasedesc =xarML('Supported');
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
        $items[$i]['class'] = xarVarPrepForDisplay($getid['class']);
        $items[$i]['regname'] = xarVarPrepForDisplay($getid['regname']);
        $items[$i]['displname'] = xarVarPrepForDisplay($getid['displname']);
        $items[$i]['realname'] = $getuser['name'];
        $items[$i]['desc'] = nl2br(xarVarPrepHTMLDisplay($getid['desc']));
        $items[$i]['notes'] = nl2br(xarVarPrepHTMLDisplay($item['notes']));
    }


    $phase=strtolower($phase);
    $data['phase'] = $phasedesc;
    // Add the array of items to the template variables
    $data['items'] = $items;

    // TODO : add a pager (once it exists in BL)
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('release', 'user', 'countnotes',array('phase'=>$phase)),
        xarModURL('release', 'user', 'viewnotes', array('startnum' => '%%','phase'=>$phase,
                                                                           'filter'=>$filter,
                                                                            'type' =>$type)),
        xarModGetUserVar('release', 'itemsperpage', $uid));

    // Return the template variables defined in this function
    return $data;

}

?>