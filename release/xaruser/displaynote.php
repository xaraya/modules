<?php

function release_user_displaynote()
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    $rnid = xarVarCleanFromInput('rnid');

    // The user API function is called.
    $item = xarModAPIFunc('release',
                          'user',
                          'getnote',
                          array('rnid' => $rnid));

    if ($item == false) return;

    // The user API function is called. 
    $id = xarModAPIFunc('release',
                         'user',
                         'getid',
                          array('rid' => $item['rid']));


    $getuser = xarModAPIFunc('roles',
                             'user',
                             'get',
                              array('uid' => $id['uid']));


        $hooks = xarModCallHooks('item',
                                        'display',
                                        $rnid,
                                        array('itemtype'  => '2',
                                              'returnurl' => xarModURL('release',
                                                                       'user',
                                                                       'displaynote',
                                                                       array('rnid' => $rnid))
                                             )
                                        );
    if (empty($hooks)) {
        $item['hooks'] = '';
    } elseif (is_array($hooks)) {
        $item['hooks'] = join('',$hooks);
    } else {
        $item['hooks'] = $hooks;
    }
    if ($item['certified'] == 2){
        $item['certifiedstatus'] = xarML('Yes');
    } else {
        $item['certifiedstatus'] = xarML('No');
    }
    $stateoptions=array();
    $stateoptions[0] = xarML('Planning');
    $stateoptions[1] = xarML('Alpha');
    $stateoptions[2] = xarML('Beta');
    $stateoptions[3] = xarML('Production/Stable');
    $stateoptions[4] = xarML('Mature');
    $stateoptions[5] = xarML('Inactive');

    foreach ($stateoptions as $key => $value) {
     if ($key==$item['rstate']) {
       $stateoption=$stateoptions[$key];
     }
    }
    $item['stateoption']=$stateoption;
    $item['desc'] = nl2br($id['desc']);
    $item['name'] = $id['name'];
    $item['type'] = $id['type'];
    $item['contacturl'] = xarModUrl('roles', 'user', 'email', array('uid' => $id['uid']));
    $item['realname'] = $getuser['name'];


    return $item;
}

// Begin Docs Portion

?>
