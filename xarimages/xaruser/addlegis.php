<?php
/**
 * Add Legislation
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/legis/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
function legis_user_addlegis($args)
{
    extract($args);
    // Security Check
    if(!xarSecurityCheck('SubmitLegis',0)) return;
    xarVarFetch('phase', 'enum:gethall:start:getdetails:preview:update',$phase,'gethall', XARVAR_NOT_REQUIRED);
    if (!xarVarFetch('defaulthall', 'int:0:', $defaulthall, null, XARVAR_NOT_REQUIRED)) {return;}

    //Set the stateoptions array for status fields
    $data = xarModAPIFunc('legis', 'user', 'menu');

    //Get common status information
    $statusdata=xarModAPIFunc('legis','user','getstatusinfo');
    $stateoptions=$statusdata['stateoptions'];
    $voteoptions= $statusdata['voteoptions'];
    $vetooptions= $statusdata['vetooptions'];
    $authortypes= $statusdata['authortypes'];

    //Get the category halls
    $halldata=xarModAPIFunc('legis','user','getsethall');
    $defaulthalldata=$halldata['defaulthalldata'];
    $defaulthall=$halldata['defaulthall'];
    $halls=$halldata['halls'];

    //Get the types of legislation
    $legistypes=xarModAPIFunc('legis','user','getmastertypes');
    if (empty($phase)){
        $phase = 'gethall';
    }
    //Check whether they can set their own hall 
    $isexec=xarModAPIFunc('legis','user','checkexecstatus');
    if (!xarUserIsLoggedIn() || !$isexec) {
      $data['cansethall']=true;
    } else {
      $data['cansethall']=false;
    }

    switch(strtolower($phase)) {
        case 'gethall':
        default:
            // First we need to get the hall that we are adding the legislation to
            $authid = xarSecGenAuthKey();
    
           //Check whether they can set their own hall
          $isexec=xarModAPIFunc('legis','user','checkexecstatus');
          if (!xarUserIsLoggedIn() || !$isexec) {
             $cansethall=true;
          } else {
             $cansethall=false;
           }
            $data = xarTplModule('legis','user', 'addlegis_gethall', array('authid'    => $authid,
                                                                           'halls'      => $halls,
                                                                           'defaulthalldata'=>$defaulthalldata,
                                                                           'cansethall' => $cansethall));

            break;

        case 'start':
          //  if (!xarVarFetch('defaulthall', 'int:1:', $defaulthall, null, XARVAR_NOT_REQUIRED)) {return;}
          /* if (xarUserIsLoggedIn()){
               $uid=xarUserGetVar('uid');
               xarModSetUserVar('legis','defaulthall',$defaulthall);
           }
           */
           xarSessionSetVar('legishall',$defaulthall);

           //$defaulthalldata=$halls[$defaulthall];
           $contributornumbers=array(1=>'1',2=>'2',3=>'3',4=>'4-5',5=>'6-10');
           if (!isset($contributorno)) $contributorno=0;

           $whereasnumber=array(1=>'1',2=>'2',3=>'3',4=>'4-5',5=>'6-10',6=>'11-20');
           if (!isset($whereasno)) $whereasno=0;

           $beitnumber=array(1=>'1',2=>'2',3=>'3',4=>'4-5',5=>'6-10',6=>'11-20');
           if (!isset($beitno)) $beitno=0;
           //Fix later
            if (empty($data['defaulthall'])){
                $message = xarML('There is no assigned default hall for this legislation.');
            }
          //  $defaulthallname=xarVarGetCached('Legis.values','defaulthallname');
            xarTplSetPageTitle(xarVarPrepForDisplay($defaulthalldata['name']."::Submit Legislation"));
            //Check whether they can set their own hall
            $isexec=xarModAPIFunc('legis','user','checkexecstatus');
            if (!xarUserIsLoggedIn() || !$isexec) {
                $cansethall=true;
            } else {
                $cansethall=false;
            }
            $authid = xarSecGenAuthKey();
            $data = xarTplModule('legis','user', 'addlegis_start', array('legistypes'         => $legistypes,
                                                                         'contributornumbers' => $contributornumbers,
                                                                         'contributorno'      => $contributorno,
                                                                         'whereasnumber'      => $whereasnumber,
                                                                         'whereasno'          => $whereasno,
                                                                         'beitnumber'         => $beitnumber,
                                                                         'beitno'             => $beitno,
                                                                         'defaulthall'        => $defaulthall,
                                                                         'defaulthalldata'    => $defaulthalldata,
                                                                        'authid'    => $authid,
                                                                        'cansethall'           => $cansethall));

            break;

        case 'getdetails':
        //   if (!xarVarFetch('defaulthall', 'int:1:', $defaulthall, null, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('beitno', 'int:0:', $beitno,1, XARVAR_NOT_REQUIRED )) {return;};
           if (!xarVarFetch('whereasno', 'int:0:', $whereasno, 1, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('contributorno', 'int:1:', $contributorno, 1, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('legistype', 'int:1:', $legistype,1, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('cdtitle', 'str:1:', $cdtitle,'', XARVAR_NOT_REQUIRED)) {return;}
            //if (!xarSecConfirmAuthKey()) return;
           if (!isset($legistype))$legistype=xarModGetVar('legis','defaultmaster');

           $legistypedata=xarModAPIFUnc('legis','user','getmaster',array('mdid'=>$legistype));
          // $defaulthalldata=$halls[$defaulthall];
            xarTplSetPageTitle(xarVarPrepForDisplay($defaulthalldata['name']."::Submit Legislation"));

           //prepare the document contributor data
           $contributordata=array('Name Me');
           for ($i=0;$i<$contributorno;$i++) {
                $authordata[]=0;
           }
          // $whereasdata=array();
           $beitdata=array();
           $beitlabel=array();
            //setup the various labels
           $labeldata=xarModAPIFunc('legis','user','getlabelinfo',array('mdid'=>$legistype));

           $beitlabel=$labeldata['beitlabel'];
           //$legisname=$labeldata['legisname'];
            $isexec=xarModAPIFunc('legis','user','checkexecstatus');
            if (!xarUserIsLoggedIn() || !$isexec) {
                $cansethall=true;
            } else {
                $cansethall=false;
            }
           $authid = xarSecGenAuthKey();
           $data = xarTplModule('legis','user', 'addlegis_getdetails',   array('legistype' => $legistype,
                                                                          'legistypes'     => $legistypes,
                                                                          'contributorno'    => $contributorno,
                                                                          'whereasno'        => $whereasno,
                                                                          'beitno'           => $beitno,
                                                                          'defaulthalldata'  => $defaulthalldata,
                                                                          'authordata'  => $authordata,
                                                                          'authortypes'      => $authortypes,
                                                                          'authid'           => $authid,
                                                                          'contributordata'=>$contributordata,
                                                                        //  'whereasdata'    =>$whereasdata,
                                                                         // 'beitdata'       =>$beitdata,
                                                                          'halls'          =>$halls,
                                                                          'defaulthall'    =>$defaulthall,
                                                                          'beitlabel'      => $beitlabel,
                                                                          'cdtitle'         => $cdtitle,
                                                                          'cansethall'      => $cansethall));

            break;
        
        case 'preview':
       //   if (!xarVarFetch('defaulthall', 'int:1:', $defaulthall, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('beitno', 'int:0:', $beitno, NULL, XARVAR_NOT_REQUIRED)) {return;};
          if (!xarVarFetch('whereasno', 'int:0:', $whereasno, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('contributorno', 'int:0:', $contributorno, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('authortype', 'int:0:', $authortype, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('legistype', 'int:0:', $legistype, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('contributordata', 'array', $contributordata, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('whereasdata', 'array', $whereasdata, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('beitdata', 'array', $beitdata, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('cdtitle', 'str:1:', $cdtitle,'', XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('authorhallname', 'array', $authorhallname,'', XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('authordata', 'array', $authordata,'', XARVAR_NOT_REQUIRED)) {return;}          
           //if (!xarSecConfirmAuthKey()) return;
          
           if (!isset($legistype))$legistype=xarModGetVar('legis','defaultmaster');

           //$legistypedata=xarModAPIFUnc('legis','user','getmaster',array('mdid'=>$legistype));
           $labeldata=xarModAPIFunc('legis','user','getlabelinfo',array('mdid'=>$legistype));

           $beitlabel=$labeldata['beitlabel'];

          $defaulthalldata=$halls[$defaulthall];
           xarTplSetPageTitle(xarVarPrepForDisplay($defaulthalldata['name']."::Submit Legislation"));

          // $notesf = nl2br($notes);
          // $changelogf = nl2br($changelog);
            $isexec=xarModAPIFunc('legis','user','checkexecstatus');
            if (!xarUserIsLoggedIn() || !$isexec) {
                $cansethall=true;
            } else {
                $cansethall=false;
            }
           $authid = xarSecGenAuthKey();
           $data = xarTplModule('legis','user', 'addlegis_preview',   array('legistype' => $legistype,
                                                                          'legistypes'     => $legistypes,
                                                                          'contributorno'    => $contributorno,
                                                                          'whereasno'        => $whereasno,
                                                                          'beitno'           => $beitno,
                                                                          'defaulthalldata'  => $defaulthalldata,
                                                                          'authortypes'      => $authortypes,
                                                                          'authid'           => $authid,
                                                                          'contributordata'=>$contributordata,
                                                                          'whereasdata'    =>$whereasdata,
                                                                          'beitdata'       =>$beitdata,
                                                                          'halls'          =>$halls,
                                                                          'defaulthall'    => $defaulthall,
                                                                          'beitlabel'      => $beitlabel,
                                                                          'cdtitle'         => $cdtitle,
                                                                          'authorhallname'=> $authorhallname,
                                                                          'authordata'       => $authordata,
                                                                          'cansethall'       => $cansethall));



            break;

        case 'update':
    //      if (!xarVarFetch('defaulthall', 'int:1:', $defaulthall, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('beitno', 'int:0:', $beitno, NULL, XARVAR_NOT_REQUIRED)) {return;};
          if (!xarVarFetch('whereasno', 'int:0:', $whereasno, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('contributorno', 'int:0:', $contributorno, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('legistype', 'int:0:', $legistype, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('contributordata', 'array', $contributordata, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('whereasdata', 'array', $whereasdata, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('beitdata', 'array', $beitdata, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('cdtitle', 'str:1:', $cdtitle,'', XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('authorhallname', 'array', $authorhallname,'', XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('authordata', 'array', $authordata,'', XARVAR_NOT_REQUIRED)) {return;}
           //if (!xarSecConfirmAuthKey()) return;
          $doccontent=array();
          $doccontent['cdtitle']=$cdtitle;
          $doccontent['contributors']=$contributordata;
          $doccontent['authordata']=$authordata;
          $doccontent['authorhallname']=$authorhallname;
          $doccontent['whereasdata']=$whereasdata;
          $doccontent['beitdata']=$beitdata;
          $doccompiled=serialize($doccontent);
            // The user API function is called.
          if (!xarModAPIFunc('legis',
                               'user',
                               'createlegis',
                                array('mdid'        => $legistype,
                                      'cdtitle'     => $cdtitle,
                                      'cdnum'       => 0,
                                      'docstatus'   => 1,
                                      'votestatus'  => 0,
                                      'vetostatus'  => 0,
                                      'submitdate'  => time(),
                                      'doccontent'    => $doccompiled,
                                      'pubnotes'      => '',
                                      'dochall'       => $defaulthall))) return;

            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Thank You')));

           $data = xarTplModule('legis','user', 'addlegis_thanks');

            break;
    }   
    
    return $data;
}

?>