<?php

function xarbb_user_main()
{
    // Security Check
    if(!xarSecurityCheck('ViewxarBB',1,'Forum')) return;

    // Get parameters from whatever input we need
    $data = array();
    $data['catid'] = xarVarCleanFromInput('catid');

    $data['items'] = array();

    // The user API function is called
    $forums = xarModAPIFunc('xarbb',
                            'user',
                            'getallforums',
                             array('catid' => $data['catid']));

    for ($i = 0; $i < count($forums); $i++) {
        $forum = $forums[$i];

        $getname = xarModAPIFunc('roles',
                                 'user',
                                 'get',
                                 array('uid' => $forum['fposter']));

        $forums[$i]['name'] = $getname['name'];
    }

    // TODO, need to check if new topics have been updated since last visit.
    $data['folder']       = '<img src="' . xarTplGetImage('folder.gif') . '" alt="'.xarML('Folder').'"/>';

    // Add the array of items to the template variables
    $data['items'] = $forums;

    // Return the template variables defined in this function
    return $data;
}

?>
