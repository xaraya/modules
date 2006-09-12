<?php
/**
 * Display a release
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
 * Display a release
 *
 * @param rid ID
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_user_display($args)
{
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    extract($args);

    if (!xarVarFetch('rid', 'int:1:', $rid, null)) {return;}
    if (!xarVarFetch('startnum', 'int', $startnum, 0, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('phase', 'str:1:7', $phase, 'view', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'basic', XARVAR_NOT_REQUIRED)) return;
    // The user API function is called. 
    $id = xarModAPIFunc('release', 'user', 'getid',
                          array('rid' => $rid));

    $cats = xarModAPIFunc('categories','user','getitemcats',
                           array('module'=>'release','item'=>$rid)
                         );

    $getuser = xarModAPIFunc('roles', 'user','get',
                              array('uid' => $id['uid']));
    //set the type
    if ($id['type']==0) {
        $exttype = xarML('Module');
    }elseif ($id['type']==1) {
        $exttype = xarML('Theme');
    }
    $data['exttype']=$exttype;
    //determine edit link
    if ((xarUserGetVar('uid') == $id['uid']) || xarSecurityCheck('EditRelease',0)) {
        $data['editlink']=xarModURL('release','user','modifyid',array('rid'=>$rid));
    } else {
        $data['editlink']='';
    }

    $stateoptions=array();
    $stateoptions[0] = xarML('Planning');
    $stateoptions[1] = xarML('Alpha');
    $stateoptions[2] = xarML('Beta');
    $stateoptions[3] = xarML('Production/Stable');
    $stateoptions[4] = xarML('Mature');
    $stateoptions[5] = xarML('Inactive');

    $memberlist = array();
    $members = trim($id['members']);
    $memberstring='';
    if (isset($members) && !empty($members)) {
        $memberdata = unserialize($members);
        if (count($memberdata)>0) {
            foreach ($memberdata as $k => $v) {
                $memberlist[]=array($v=>xarUserGetVar('uname',$v));
            }
        }


        foreach ($memberlist as $key=>$iid) {
            foreach($iid as $userid=>$username) {
               if ($key == 0) {
                    $memberstring = "<a href=\"".xarModURL('roles','user','display',array('uid'=>$userid))."\">".$username."</a>";
                }else{
                    $memberstring .=", <a href=\"".xarModURL('roles','user','display',array('uid'=>$userid))."\">".$username."</a>";
                }
            }
        }
    }

    switch(strtolower($phase)) {

        case 'view':
        default:

            $hooks = xarModCallHooks('item', 'display',$rid,
                                     array('module' => 'release',
                                           'itemtype'  => '1',
                                           'returnurl' => xarModURL('release', 'user','display',
                                                                     array('rid' => $rid))
                                          ),
                                     'release'
                                     );


            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }
            $data['version'] = 0;
            $data['docs'] = 0;
            $data['general'] = 2;
            break;

        case 'version':
            // The user API function is called.
            $items = array();
            $items = xarModAPIFunc('release', 'user','getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('release',
                                                                  'itemsperpage'),
                                        'rid' => $rid));

            if (empty($items)){
                $data['message'] = xarML('There is no version history on this module');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                // The user API function is called.
                $getid = xarModAPIFunc('release', 'user','getid',
                                       array('rid' => $items[$i]['rid']));

                $items[$i]['type'] = xarVarPrepForDisplay($getid['type']);
                $items[$i]['regname'] = xarVarPrepForDisplay($getid['regname']);
                $items[$i]['displname'] = xarVarPrepForDisplay($getid['displname']);
                $items[$i]['class'] = xarVarPrepForDisplay($getid['class']);
                $items[$i]['displaylink'] =  xarModURL('release', 'user','displaynote',
                                                   array('rnid' => $items[$i]['rnid']));
                if (xarSecurityCheck('AdminRelease',0)) {
                    $items[$i]['editlink'] =  xarModURL('release', 'admin','modifynote',
                                                   array('rnid' => $items[$i]['rnid']));
                }else{
                    $items[$i]['editlink'] =  '';
                }

                $getuser = xarModAPIFunc('roles','user','get',
                                          array('uid' => $getid['uid']));

                $items[$i]['contacturl'] = xarModURL('roles','user','display',
                                                      array('uid' => $getid['uid']));


                $items[$i]['realname'] = $getuser['name'];
                $items[$i]['desc'] = xarVarPrepForDisplay($getid['desc']);

                if ($items[$i]['certified'] == 2){
                    $items[$i]['certifiedstatus'] = xarML('Yes');
                } else {
                   $items[$i]['certifiedstatus'] = xarML('No');
                }
                $items[$i]['changelog'] = nl2br($items[$i]['changelog']);
                $items[$i]['notes'] = nl2br($items[$i]['notes']);

                $items[$i]['comments'] = xarModAPIFunc('comments', 'user','get_count',
                                                       array('modid' => xarModGetIDFromName('release'),
                                                             'objectid' => $item['rnid']));
                
                if (!$items[$i]['comments']) {
                    $items[$i]['comments'] = '0';
                } elseif ($items[$i]['comments'] == 1) {
                    $items[$i]['comments'] .= ' ';
                } else {
                    $items[$i]['comments'] .= ' ';
                }

               $items[$i]['hitcount'] = xarModAPIFunc('hitcount', 'user','get',
                                                       array('modname' => 'release',
                                                             'objectid' => $item['rnid']));

                if (!$items[$i]['hitcount']) {
                    $items[$i]['hitcount'] = '0';
                } elseif ($items[$i]['hitcount'] == 1) {
                    $items[$i]['hitcount'] .= ' ';
                } else {
                    $items[$i]['hitcount'] .= ' ';
                }

                //Get the status update of each release
                foreach ($stateoptions as $key => $value) {
                    if ($key==$items[$i]['rstate']) {
                       $rstatesel=$stateoptions[$key];
                    }
                }
                $items[$i]['rstatesel']=$rstatesel;


            }

            $data['version'] = 2;
            $data['items'] = $items;
            $data['general'] = 2;
            $data['tab'] = 'versions';
            break;


        case 'docsmodule':
            $data['mtype'] = 'mgeneral';
            // The user API function is called. 

            $items = xarModAPIFunc('release', 'user','getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no general module documentation defined');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release', 'user','getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;
       
            break;
        
        case 'docstheme':

            $data['mtype'] = 'tgeneral';
            // The user API function is called. 

            $items = xarModAPIFunc('release', 'user','getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no general theme documentation defined');
            }
             $numitems=count($items);
            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < $numitems; $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release', 'user','getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;
            break;

        case 'docsblockgroups':

            $data['mtype'] = 'bgroups';
            // The user API function is called. 

            $items = xarModAPIFunc('release', 'user','getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no block groups documentation defined');
            }

            
            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release', 'user','getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;

            break;
        
        case 'docsblocks':

            $data['mtype'] = 'mblocks';
            // The user API function is called. 

            $items = xarModAPIFunc('release', 'user', 'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no blocks documentation defined');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release', 'user', 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;
            return $data;
            break;

        case 'docshooks':

            $data['mtype'] = 'mhooks';
            // The user API function is called. 

            $items = xarModAPIFunc('release', 'user', 'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no hooks documentation defined');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release', 'user', 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;

            break;

    }
    foreach ($stateoptions as $key => $value) {
         if ($key==$id['rstate']) {
             $rstatesel=$stateoptions[$key];
         }
    }

     $data['rstatesel']=$rstatesel;
     $data['stateoptions']=$stateoptions;

// Version History
// View Docs
// Comment on docs
    $time=time();
    $data['time']=$time;
    $data['desc'] = nl2br($id['desc']);
    $data['regname'] = $id['regname'];
    $data['regtime'] = $id['regtime'];    
    $data['displname'] = $id['displname'];
    $scmlink = str_replace('http://','',$id['scmlink']);
    $data['scmlink']= !empty($scmlink) ? $id['scmlink'] : '';
    $data['type'] = $id['type'];
    $data['class'] = $id['class'];
    $data['modified'] = $id['modified'];
    $data['memberstring']= $memberstring;
    $data['contacturl'] = xarModUrl('roles', 'user', 'email', array('uid' => $id['uid']));
    $data['realname'] = $getuser['name'];
    $data['rid'] = $rid;
    $data['startnum']=$startnum;

return $data;
}

?>