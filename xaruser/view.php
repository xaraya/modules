<?php
/**
 * Main view for Releases
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * @author niceguyeddie
 * @author jojodee
 * @param int $idtypes: 1- all, 2-themes, 3-modules
 * @param enum sort - sort criteria
 * @TODO : sort ok but need to make sticky over categories etc ...and vice versa
 */
function release_user_view()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('phase',    'str:1:', $phase,    'all', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('catid',    'int',    $catid,    NULL,  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('sort',     'enum:pre:trim:lower:alnum', $sort, NULL, XARVAR_NOT_REQUIRED)) {return;}
   // Default parameters
    if (!isset($startnum)) {
        $startnum = 1;
    }
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    $uid = xarUserGetVar('uid');
    // Set the type of extension
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
    if (empty($sort)) {
        $sort = 'id';
    }

    // The user API function is called to get all extension IDs.
    $items = xarModAPIFunc('release', 'user', 'getallrids',
                   array('idtypes'  => $idtypes,
                         'catid'    => $catid,
                         'sort'     => $sort,
                         'startnum' => $startnum,
                         'numitems' => xarModGetUserVar('release',
                                                        'itemsperpage',$uid),
                          ));
    //Add common definition of the extension state array
    //TODO: <jojodee> This needs to be extensible ..not hard coded here ...
    $stateoptions=array();
    $stateoptions[0] = xarML('Planning');
    $stateoptions[1] = xarML('Alpha');
    $stateoptions[2] = xarML('Beta');
    $stateoptions[3] = xarML('Production/Stable');
    $stateoptions[4] = xarML('Mature');
    $stateoptions[5] = xarML('Inactive');

    $numitems = count($items);

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < ($numitems); $i++) {
        $item = $items[$i];
        $items[$i]['author'] = xarVarPrepForDisplay($item['author']);
        // Basic Information
        $items[$i]['rid'] = xarVarPrepForDisplay($item['rid']);
        $items[$i]['regname'] = xarVarPrepForDisplay($item['regname']);
        $items[$i]['displname'] = xarVarPrepForDisplay($item['displname']);
        /* use the xarUserGetVar func as we only want name 
         * TODO: Where is this user taken to?
         */
        $getuser = xarModAPIFunc('roles', 'user', 'get',
                                  array('uid' => $item['uid']));

        // Author Name and Contact URL

        $items[$i]['contacturl'] = xarModURL('roles', 'user', 'display',
                                              array('uid' => $item['uid']));

        // InfoURL
        $items[$i]['infourl'] = xarModURL('release', 'user', 'display',
                                          array('rid' => $item['rid'],
                                                'phase' => 'version',
                                                'tab' => 'version'));
        $items[$i]['infotitle'] = xarML('View');

        // Edit
        if (($uid == $item['uid']) or (xarSecurityCheck('EditRelease', 0))) {
            $items[$i]['editurl'] = xarModURL('release', 'user', 'modifyid',
                                               array('rid' => $item['rid']));
            $items[$i]['edittitle'] = xarML('Edit');
        } else {
            $items[$i]['edittitle'] = '';
            $items[$i]['editurl'] = '';
        }

        // Add Release Note URL
        if (($uid == $item['uid']) or (xarSecurityCheck('EditRelease', 0))) {
            $items[$i]['addurl'] = xarModURL('release', 'user', 'addnotes',
                                               array('rid' => $item['rid'],
                                                     'phase' => 'start'));
            $items[$i]['addtitle'] = xarML('Add');
        } else {
            $items[$i]['addurl'] = '';
            $items[$i]['addtitle'] = '';
        }

        // Add Docs URL
        if (($uid == $item['uid']) or (xarSecurityCheck('EditRelease', 0))) {
            $items[$i]['adddocs'] = xarModURL('release', 'user', 'adddocs',
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
            $items[$i]['comments'] = xarModAPIFunc('comments', 'user', 'get_count',
                                                   array('modid' => xarModGetIDFromName('release'),
                                                         'objectid' => $item['rid']));

            if ($items[$i]['comments'] != '0') {
                $items[$i]['comments'] .= ' ';
            }
        }

        $items[$i]['hitcount'] = '0';
        if (xarModIsAvailable('hitcount')){
            // Get Hits
            $items[$i]['hitcount'] = xarModAPIFunc('hitcount', 'user', 'get',
                                                   array('modname' => 'release',
                                                         'itemtype' => '1',
                                                         'objectid' => $item['rid']));

            if ($items[$i]['hitcount'] != '0') {
                $items[$i]['hitcount'] .= ' ';
            }
        }

        $items[$i]['docs'] = xarModAPIFunc('release', 'user','countdocs',
                                           array('rid' => $item['rid']));

        //Get some info for the extensions state
       foreach ($stateoptions as $key => $value) {
           if ($key==$items[$i]['rstate']) {
              $items[$i]['extstate']=$stateoptions[$key];
           }
       }

       $allitems = xarModAPIFunc('release', 'user', 'countitems',array('idtypes'=>$idtypes,'catid'=>$catid));

           $data['pager'] = xarTplGetPager($startnum,
           $allitems,
           xarModURL('release', 'user', 'view', array('startnum' => '%%','idtypes'=>$idtypes,'catid'=>$catid, 'sort'=>$sort)),
          xarModGetUserVar('release', 'itemsperpage', $uid));

    }
    if (!isset($allitems)) {
        $allitems=0;
    }
    $data['sort'] = $sort;
    $data['numitems']=$allitems;
    $data['phase']=$phase;
    $data['catid'] = $catid;
    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;

}
?>