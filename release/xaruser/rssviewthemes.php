<?php

function release_user_rssviewthemes()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    $data['items'] = array();

    // The user API function is called.
    $items = xarModAPIFunc('release',
                           'user',
                           'getallrssmodsnotes',
                            array('type' => 'theme'));
    
    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        // The user API function is called.
        $getid = xarModAPIFunc('release',
                               'user',
                               'getid',
                               array('rid' => $items[$i]['rid']));

        $items[$i]['regname'] = xarVarPrepForDisplay($getid['regname']);

        $items[$i]['displname'] = xarVarPrepForDisplay($getid['displname']);

        $items[$i]['displaylink'] =  xarModURL('release',
                                               'user',
                                               'displaynote',
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