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


    //get the doclets list
    $docletlist=xarModAPIFunc('legis','user','getdoclets');

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
    if (isset($defaulthall) && $data['cansethall']) {
        $halldata=xarModAPIFunc('legis','user','getsethall',array('defaulthall'=>$defaulthall));
    //Get the category halls
    }else {
        $halldata=xarModAPIFunc('legis','user','getsethall');
    }
    $defaulthalldata=$halldata['defaulthalldata'];
    $defaulthall=$halldata['defaulthall'];

    $halls=$halldata['halls'];

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
                                                                           'cansethall' => $cansethall,
                                                                           'legistypes' => $legistypes));

            break;

        case 'start':
           if (!xarVarFetch('legistype', 'int:1:', $legistype,1, XARVAR_NOT_REQUIRED)) {return;}

           xarSessionSetVar('legishall',$defaulthall);

           //$defaulthalldata=$halls[$defaulthall];
           $contributornumbers=array(1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10',12=>'12',15=>'15',20=>'20');
           if (!isset($contributorno)) $contributorno=0;


           $clausepresets=array(1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10',12=>'12',15=>'15',20=>'20');
           //get clauses for this legislation type

           $documentdata=xarModAPIFunc('legis','user','getmaster',array('mdid'=>$legistype));
           $documentclauses=$documentdata['mddef'];
           $documentclauses=unserialize($documentclauses);
           $currentdoclets=array();
           foreach ($documentclauses as $k => $v) {
                   $docletno=(int)$v;
                   if (!isset($currentdoclets[$docletno]['clauseno'])){
                       $currentdoclets[$docletno]['clauseno']=0;
                   }
                   $currentdoclets[$docletno]=array('clauseno'  =>$currentdoclets[$docletno]['clauseno'],
                                                    'clausename'=>$docletlist[$docletno]['dname'],
                                                    'label1'    =>$docletlist[$docletno]['dlabel'],
                                                    'label2'    =>$docletlist[$docletno]['dlabel2']);
           }

           //Fix later
            if (empty($data['defaulthall'])){
                $message = xarML('There is no assigned default hall for this legislation.');
            }

            xarSessionSetVar('Legisdoclets',$currentdoclets);
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
                                                                         'clausepresets'      => $clausepresets,
                                                                         'defaulthall'        => $defaulthall,
                                                                         'defaulthalldata'    => $defaulthalldata,
                                                                         'authid'              => $authid,
                                                                         'cansethall'           => $cansethall,
                                                                         'legistype'            => $legistype,
                                                                         'currentdoclets'       =>$currentdoclets));

            break;

        case 'getdetails':
           $currentdoclets=xarSessionGetVar('Legisdoclets');
            foreach ($currentdoclets as $k=>$doc) {
              if (!xarVarFetch("clauseno$k", 'int:0:', ${'clauseno'.$k},1, XARVAR_NOT_REQUIRED )) {return;};
              $currentdoclets[$k]['clauseno']=${'clauseno'.$k};
             }
              xarSessionSetVar('Legisdoclets',$currentdoclets);
           if (!xarVarFetch('contributorno', 'int:1:', $contributorno, 1, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('legistype', 'int:1:', $legistype,1, XARVAR_NOT_REQUIRED)) {return;}
           if (!xarVarFetch('cdtitle', 'str:1:', $cdtitle,'', XARVAR_NOT_REQUIRED)) {return;}

            //if (!xarSecConfirmAuthKey()) return;
           if (!isset($legistype))$legistype=xarModGetVar('legis','defaultmaster');

           $legistypedata=xarModAPIFUnc('legis','user','getmaster',array('mdid'=>$legistype));
      
            xarTplSetPageTitle(xarVarPrepForDisplay($defaulthalldata['name']."::Submit Legislation"));

           //prepare the document contributor data
           $contributordata=array('Name Me');
           for ($i=0;$i<$contributorno;$i++) {
                $authordata[]=0;
           }

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
                                                                          'defaulthalldata'  => $defaulthalldata,
                                                                          'authordata'  => $authordata,
                                                                          'authortypes'      => $authortypes,
                                                                          'authid'           => $authid,
                                                                          'contributordata'=>$contributordata,
                                                                          'halls'          =>$halls,
                                                                          'defaulthall'    =>$defaulthall,
                                                                          'cdtitle'         => $cdtitle,
                                                                          'cansethall'      => $cansethall,
                                                                          'currentdoclets'  => $currentdoclets));

            break;

        case 'preview':
          $currentdoclets=xarSessionGetVar('Legisdoclets');
            foreach ($currentdoclets as $k=>$doc) {
                for ($i = 1; $i <= $doc['clauseno']; $i++) {
                     if (!xarVarFetch("clause{$k}_{$i}", 'str:1:',
                      ${'clause'.$k.'_'.$i},'', XARVAR_NOT_REQUIRED )) {return;};
                       $currentdoclets[$k]['data'][$i]=${'clause'.$k.'_'.$i};
                }
            }
              xarSessionSetVar('Legisdoclets',$currentdoclets);
          if (!xarVarFetch('contributorno', 'int:0:', $contributorno, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('authortype', 'int:0:', $authortype, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('legistype', 'int:0:', $legistype, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('contributordata', 'array', $contributordata, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('cdtitle', 'str:1:', $cdtitle,'', XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('authorhallname', 'array', $authorhallname,'', XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('authordata', 'array', $authordata,'', XARVAR_NOT_REQUIRED)) {return;}
           //if (!xarSecConfirmAuthKey()) return;

           if (!isset($legistype))$legistype=xarModGetVar('legis','defaultmaster');

           $defaulthalldata=$halls[$defaulthall];
           xarTplSetPageTitle(xarVarPrepForDisplay($defaulthalldata['name']."::Submit Legislation"));

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
                                                                          'defaulthalldata'  => $defaulthalldata,
                                                                          'authortypes'      => $authortypes,
                                                                          'authid'           => $authid,
                                                                          'contributordata'=>$contributordata,
                                                                          'halls'          =>$halls,
                                                                          'defaulthall'    => $defaulthall,
                                                                          'cdtitle'         => $cdtitle,
                                                                          'authorhallname'=> $authorhallname,
                                                                          'authordata'       => $authordata,
                                                                          'cansethall'       => $cansethall,
                                                                          'currentdoclets'   => $currentdoclets));



            break;

        case 'update':
        $doccontent=array();
        $currentdoclets=xarSessionGetVar('Legisdoclets');
            foreach ($currentdoclets as $k=>$doc) {
                for ($i = 1; $i <= $doc['clauseno']; $i++) {
                     if (!xarVarFetch("clause{$k}_{$i}", 'str:1:',
                      ${'clause'.$k.'_'.$i},'', XARVAR_NOT_REQUIRED )) {return;};
                       $doccontent[$k][$i]=${'clause'.$k.'_'.$i};
                }
            }
              xarSessionDelVar('Legisdoclets',$currentdoclets);

          if (!xarVarFetch('contributorno', 'int:0:', $contributorno, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('legistype', 'int:0:', $legistype, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('contributordata', 'array', $contributordata, null, XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('cdtitle', 'str:1:', $cdtitle,'', XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('authorhallname', 'array', $authorhallname,'', XARVAR_NOT_REQUIRED)) {return;}
          if (!xarVarFetch('authordata', 'array', $authordata,'', XARVAR_NOT_REQUIRED)) {return;}
           //if (!xarSecConfirmAuthKey()) return;

          if (is_array($authordata)) {
              foreach ($authordata as $k=>$v) { //force to start at 1
              $contributors[$k+1]=array('authorname'=>$contributordata[$k],
                                    'authordata'=>$authordata[$k],
                                    'authorhallname'=>$authorhallname[$k]);
              }
          }
          $contributorcompiled=serialize($contributors);
          $doccompiled=serialize($doccontent);
            // The user API function is called.
          $newcdid=xarModAPIFunc('legis',
                               'user',
                               'createlegis',
                                array('mdid'        => $legistype,
                                      'cdtitle'     => $cdtitle,
                                      'cdnum'       => 0,
                                      'docstatus'   => 1,
                                      'votestatus'  => 0,
                                      'vetostatus'  => 0,
                                      'submitdate'  => time(),
                                      'contributors'=> $contributorcompiled,
                                      'doccontent'    => $doccompiled,
                                      'pubnotes'      => '',
                                      'dochall'       => $defaulthall));
          if (!$newcdid) return;

            xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Thank You')));
           $isexec=xarModAPIFunc('legis','user','checkexecstatus');
            if (!xarUserIsLoggedIn() || !$isexec) {
                $cansethall=true;
            } else {
                $cansethall=false;
            }
           $data = xarTplModule('legis','user', 'addlegis_thanks',  array('cansethall' => $cansethall,
                                                                          'currentdoclets'=>$currentdoclets));

           //Let's tell all hall members that we have a new document
           if (!xarModAPIFunc('legis','user','notify',
                           array('notifytype'   => 1,
                                 'cdid'         => $newcdid))) return; //notifytype 1 = new document
            break;
    }
    
    return $data;
}

?>