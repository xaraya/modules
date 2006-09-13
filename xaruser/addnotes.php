<?php
/*
 * Add an extension release note
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_user_addnotes($args)
{
    extract($args);
    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;
    xarVarFetch('phase', 'enum:getmodule:start:getbasics:getdetails:preview:update',$phase, 'getmodule', XARVAR_NOT_REQUIRED);
    if (!xarVarFetch('eid', 'int:1:', $eid, null, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('rid', 'int:1:', $rid, null, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('exttype', 'int:1:', $exttype, null, XARVAR_NOT_REQUIRED)) {return;}
    
    if (isset($eid) && $eid>0) {
      $data = xarModAPIFunc('release', 'user', 'getid',
                                  array('eid' => $eid));

    }
    if (!isset($eid) && isset($rid) && isset($exttype)) {
      $data = xarModAPIFunc('release', 'user', 'getid',
                                  array('rid' => $rid, 'exttype' =>$exttype));

    }
    if (isset($data) && !empty($data['regname'])){
        $rid = $data['rid'];
        $exttype = $data['exttype'];
        $regname = $data['regname'];
        $eid =$data['eid'];
    } else {
        $regname ='';
    }


    $exttypes = xarModAPIFunc('release','user','getexttypes'); //extension types
    $data['exttypes']=$exttypes;

    if (empty($phase)){
        $phase = 'getmodule';
    }
             //Set the stateoptions array for rstate field
                $stateoptions=array();
                $stateoptions[0] = xarML('Planning');
                $stateoptions[1] = xarML('Alpha');
                $stateoptions[2] = xarML('Beta');
                $stateoptions[3] = xarML('Production/Stable');
                $stateoptions[4] = xarML('Mature');
                $stateoptions[5] = xarML('Inactive');
                $data['stateoptions']=$stateoptions;

    switch(strtolower($phase)) {
        case 'getmodule':
        default:
            // First we need to get the extension and extension type that we are adding the release note to.
            if (!isset($m)) $m ='';

            $authid = xarSecGenAuthKey();
            $data = xarTplModule('release','user', 'addnote_getmodule',
                array('authid' => $authid,
                      'rid'=>$rid, 
                      'regname'=>$regname, 
                      'exttype'=>$exttype,
                      'exttypes'=>$exttypes, 
                      'message'=>$m));

            break;

        case 'start':

            $data = xarModAPIFunc('release', 'user', 'getid',
                                  array('rid' => $rid, 'exttype' => $exttype));

            $exttypename = array_search($exttype,array_flip($exttypes));

            if (!isset($data['regname']) || empty($data['regname'])) {
                 $m = xarML('Sorry, that extension number and extension type combination does not exist');

                 xarResponseRedirect(xarModURL('release', 'user', 'addnotes',
                        array('m'=>$m,'rid'=>$rid,'exttype'=>$exttype,'phase'=>'getmodule')));
            }

            $uid = xarUserGetVar('uid');

            if (($data['uid'] == $uid) or (xarSecurityCheck('EditRelease', 0))) {
                $message = '';
            } else {
                $message = xarML('You are not allowed to add a release notification to this module');
            }

            //TODO FIX ME!!!
            if (empty($data['regname'])){
                $message = xarML('There is no assigned ID for your extension.');
            }

            xarTplSetPageTitle(xarVarPrepForDisplay($data['regname']));

            $authid = xarSecGenAuthKey();
            $data = xarTplModule('release','user', 'addnote_start', 
                                                   array('eid'       => $data['eid'],
                                                         'rid'       => $data['rid'],
                                                         'regname'   => $data['regname'],
                                                         'displname'=>   $data['displname'],
                                                         'desc'      => $data['desc'],
                                                         'message'   => $message,
                                                         'exttype'  => $exttype,
                                                         'authid'    => $authid));
            break;

        case 'getbasics':

           //if (!xarSecConfirmAuthKey()) return;
           $democheck=1;
           $supportcheck=1;
           $pricecheck=1;
            xarTplSetPageTitle(xarVarPrepForDisplay($regname));

           $authid = xarSecGenAuthKey();
           $data = xarTplModule('release','user', 'addnote_getbasics', array('eid'       => $eid,
                                                                             'rid'      => $rid,
                                                                             'regname'  => $regname,
                                                                             'authid'   => $authid,
                                                                             'democheck' => $democheck,
                                                                             'supportcheck' => $supportcheck,
                                                                             'exttype'  => $exttype,
                                                                             'pricecheck' => $pricecheck));
            break;

        case 'getdetails':

           if (!xarVarFetch('rid', 'int:1:', $rid, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('eid', 'int:1:', $eid, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('exttype', 'int:1:', $exttype, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('regname', 'str:1:', $regname, NULL, XARVAR_NOT_REQUIRED)) {return;};
           if (!xarVarFetch('version', 'str:1:', $version, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('pricecheck', 'int:1:2', $pricecheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('supportcheck', 'int:1:2', $supportcheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('democheck', 'int:1:2', $democheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('usefeedchecked', 'checkbox', $usefeedchecked,false, XARVAR_NOT_REQUIRED)) {return;}
           //if (!xarSecConfirmAuthKey()) return;
           $usefeed = $usefeedchecked?1:0;
            xarTplSetPageTitle(xarVarPrepForDisplay($regname));

           $authid = xarSecGenAuthKey();
           $data = xarTplModule('release','user', 'addnote_getdetails', array('eid'          => $eid,
                                                                              'rid'          => $rid,
                                                                              'regname'      => $regname,
                                                                              'authid'       => $authid,
                                                                              'version'      => $version,
                                                                              'pricecheck'   => $pricecheck,
                                                                              'supportcheck' => $supportcheck,
                                                                              'democheck'    => $democheck,
                                                                              'exttype'      => $exttype,
                                                                              'stateoptions' => $stateoptions,
                                                                              'usefeed'      => $usefeed));

            break;
        
        case 'preview':
           if (!xarVarFetch('rid', 'int:1:', $rid, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('eid', 'int:1:', $eid, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('exttype', 'int:1:', $exttype, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('regname', 'str:1:', $regname, NULL, XARVAR_NOT_REQUIRED)) {return;};
           if (!xarVarFetch('version', 'str:1:', $version, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('pricecheck', 'int:1:2', $pricecheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('supportcheck', 'int:1:2', $supportcheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('democheck', 'int:1:2', $democheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('dllink', 'str:1:', $dllink, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('price', 'str', $price, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('demolink', 'str:1:254', $demolink, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('supportlink', 'str:1:254', $supportlink, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('changelog', 'str', $changelog, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('notes', 'str', $notes, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('rstate', 'int:0:6', $rstate, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('usefeedchecked', 'checkbox', $usefeedchecked, false, XARVAR_NOT_REQUIRED)) {return;}
           $usefeed = $usefeedchecked?1:0;
           //if (!xarSecConfirmAuthKey()) return;
           //Get some info for the extensions state
           foreach ($stateoptions as $key => $value) {
               if ($key==$rstate) {
                   $extstate=$stateoptions[$key];
               }
           }
           $notesf = nl2br($notes);
           $changelogf = nl2br($changelog);

            xarTplSetPageTitle(xarVarPrepForDisplay($regname));

           $authid = xarSecGenAuthKey();
           $data = xarTplModule('release','user', 'addnote_preview',    array('rid'         => $rid,
                                                                              'eid'         => $eid,
                                                                              'regname'     => $regname,
                                                                              'authid'      => $authid,
                                                                              'version'     => $version,
                                                                              'pricecheck'  => $pricecheck,
                                                                              'supportcheck'=> $supportcheck,
                                                                              'democheck'   => $democheck,
                                                                              'dllink'      => $dllink,
                                                                              'price'       => $price,
                                                                              'demolink'    => $demolink,
                                                                              'supportlink' => $supportlink,
                                                                              'changelog'   => $changelog,
                                                                              'changelogf'  => $changelogf,
                                                                              'notesf'      => $notesf,
                                                                              'notes'       => $notes,
                                                                              'rstate'      => $rstate,
                                                                              'exttype'     => $exttype,
                                                                              'stateoptions'=> $stateoptions,
                                                                              'extstate'     => $extstate,
                                                                              'usefeed'      => $usefeed));



            break;

        case 'update':
           if (!xarVarFetch('rid', 'int:1:', $rid, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('eid', 'int:1:', $eid, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('exttype', 'int:1:', $exttype, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('regname', 'str:1:', $regname, NULL, XARVAR_NOT_REQUIRED)) {return;};
           if (!xarVarFetch('version', 'str:1:', $version, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('pricecheck', 'int:1:2', $pricecheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('supportcheck', 'int:1:2', $supportcheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('democheck', 'int:1:2', $democheck, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('dllink', 'str:1:', $dllink, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('price', 'float', $price, 0, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('demolink', 'str:1:254', $demolink, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('supportlink', 'str:1:254', $supportlink, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('changelog', 'str:1:', $changelog, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('notes', 'str:1:', $notes, '', XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('rstate', 'int:0:6', $rstate, 0, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('usefeedchecked', 'checkbox', $usefeedchecked, false, XARVAR_NOT_REQUIRED)) {return;}
           //if (!xarSecConfirmAuthKey()) return;
            // The user API function is called.
            $usefeed = $usefeedchecked?1:0;
            $data = xarModAPIFunc('release', 'user', 'getid',
                                  array('rid' => $rid));

            $exttype = $data['exttype'];
            // The user API function is called.
            if (!xarModAPIFunc('release', 'user', 'createnote',
                                array('eid'         => $eid,
                                      'rid'         => $rid,
                                      'version'     => $version,
                                      'price'       => $pricecheck,
                                      'priceterms'  => $price,
                                      'supported'   => $supportcheck,
                                      'demo'        => $democheck,
                                      'dllink'      => $dllink,
                                      'demolink'    => $demolink,
                                      'supportlink' => $supportlink,
                                      'changelog'   => $changelog,
                                      'exttype'     => $exttype,
                                      'notes'       => $notes,
                                      'rstate'      => $rstate,
                                      'usefeed'     => $usefeed))) return;

            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Thank You')));

           $data = xarTplModule('release','user', 'addnote_thanks');

            break;
    }   
    
    return $data;
}

?>