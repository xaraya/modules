<?php
/**
 * jojodee: added pager ability
 * param $idtypes: 1- all, 2-themes, 3-modules
 */
function release_user_view()
{
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase', 'str:1:', $phase, 'all', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid', 'int', $catid, null,  XARVAR_NOT_REQUIRED)) {return;}


    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    $phase = xarVarCleanFromInput('phase');

    $uid = xarUserGetVar('uid');

    if (!isset($idtypes)) {
       $idtypes=1;
    }
    if ($phase == 'modules') {
        $idtypes=3;
    }elseif ($phase =='themes') {
        $idtypes=2;
    }else{
     $idtypes=1;
    }
    $data = array();

    //jojodee: I've put all this into one function now userapi getallrids.

    /*
     switch(strtolower($phase)) {

        case 'all':
        default:

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallids',
                                   array('idtypes' => $idtypes,
                                         'startnum' => $startnum,
                                         'numitems' => xarModGetUserVar('release',
                                                                    'itemsperpage',$uid),
                                        ));
            $idtypes=1;
            break;

        case 'themes':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getthemeids',
                                  array('startnum' => $startnum,
                                         'numitems' => xarModGetUserVar('release',
                                                                    'itemsperpage',$uid)
                                    ));

            $idtypes=2;
            break;

        case 'modules':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getmoduleids',
                             array('startnum' => $startnum,
                                   'numitems' => xarModGetUserVar('release',
                                                              'itemsperpage',$uid)
                                   ));
            $idtypes=3;
            break;
    }

*/ 

      // The user API function is called.
      $items = xarModAPIFunc('release',
                             'user',
                             'getallrids',
                       array('idtypes'  => $idtypes,
                             'catid'    => $catid,
                             'startnum' => $startnum,
                             'numitems' => xarModGetUserVar('release',
                                                            'itemsperpage',$uid),
                              ));

    //Add common definition of the extension state array
    $stateoptions=array();
    $stateoptions[0] = xarML('Planning');
    $stateoptions[1] = xarML('Alpha');
    $stateoptions[2] = xarML('Beta');
    $stateoptions[3] = xarML('Production/Stable');
    $stateoptions[4] = xarML('Mature');
    $stateoptions[5] = xarML('Inactive');


    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        // Basic Information
        $items[$i]['rid'] = xarVarPrepForDisplay($item['rid']);
        $items[$i]['regname'] = xarVarPrepForDisplay($item['regname']);
        $items[$i]['displname'] = xarVarPrepForDisplay($item['displname']);

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

        //Get some info for the extensions state
       foreach ($stateoptions as $key => $value) {
           if ($key==$items[$i]['rstate']) {
              $items[$i]['extstate']=$stateoptions[$key];
           }
       }

    }
    $data['phase']=$phase;
    $data['catid'] = $catid;
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('release', 'user', 'countitems',array('idtypes'=>$idtypes)),
        xarModURL('release', 'user', 'view', array('startnum' => '%%','phase'=>$phase)),
        xarModGetUserVar('release', 'itemsperpage', $uid));

    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;

}

?>