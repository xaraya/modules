<?php

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

?>