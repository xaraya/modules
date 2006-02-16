<?php
/**
 * Modify an item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */

/**
 * Modify an item
 *
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @author jojodee
 * @param  $ 'cdid' the id of the item to be modified
 */
function legis_admin_modify($args)
{ 
    extract($args);

    if (!xarVarFetch('cdid',     'id',     $cdid)) return;
    if (!xarVarFetch('objectid', 'id',     $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaulthall', 'int:1:', $defaulthall, null, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarSecurityCheck('EditLegis', 1)) {
        return;
    }

    if (!empty($objectid)) {
        $cdid = $objectid;
    }
    $item=array();
    $item = xarModAPIFunc('legis','user','get',array('cdid' => $cdid));

    $itemhall=$item['dochall'];
  /* Check for exceptions */
    $item['menu'] = xarModAPIFunc('legis', 'admin', 'menu');
    //Get common status information
    $statusdata=xarModAPIFunc('legis','user','getstatusinfo');
    $stateoptions=$statusdata['stateoptions'];
        $item['stateoptions']=$stateoptions;
    $voteoptions= $statusdata['voteoptions'];
        $item['voteoptions']=$voteoptions;
    $vetooptions= $statusdata['vetooptions'];
        $item['vetooptions']=$vetooptions;
    $authortypes= $statusdata['authortypes'];
        $item['authortypes']=$authortypes;

        //Get the category halls
        $hallsparent=xarModGetVar('legis','mastercids');
        $halls=xarModApiFunc('categories','user','getchildren',array('cid'=>$hallsparent));
        $item['halls']=$halls;
        $item['defaulthalldata']=$halls[$item['dochall']];
        $item['dochallname']=ucfirst($item['defaulthalldata']['name']);


    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    $legisnum = (int)$item['mdid'];


    $legistype=xarModAPIFUnc('legis','user','getmaster',array('mdid'=>$legisnum));
    $legisname=strtolower($legistype['mdname']);
    $item['legisname']=$legisname;
    $item['legistypedata']=$legistype;


    $documentdata= unserialize($item['doccontent']);
    $item['documentdata']=$documentdata;
    $item['contributordata']=unserialize($item['contributors']);
    $item['authornum']=count($item['contributordata']);

    //get the doclets list
    $docletlist=xarModAPIFunc('legis','user','getdoclets');

    $documentclauses=$legistype['mddef'];
    $documentclauses=unserialize($documentclauses);

    $currentdoclets=array();
    foreach ($documentclauses as $k => $v) {
               $docletno=(int)$v;
                  $currentdoclets[$docletno]=array('clausename'=>$docletlist[$docletno]['dname'],
                                                   'label1'    =>$docletlist[$docletno]['dlabel'],
                                                   'label2'    =>$docletlist[$docletno]['dlabel2']);
           }


    foreach ($currentdoclets as $doclet=>$d) {
      $currentdoclets[$doclet]['data']=$documentdata[$doclet];
      $currentdoclets[$doclet]['clauseno']=count($documentdata[$doclet]);
    }

    $data['currentdoclets']=$currentdoclets;
    $item['currentdoclets']=$currentdoclets;

    xarSessionSetVar('Legisdoclets',$currentdoclets);

    $isexec=xarModAPIFunc('legis','user','checkexecstatus');
    if (!xarUserIsLoggedIn() || !$isexec) {
      $item['cansethall']=true;
    } else {
      $item['cansethall']=false;
    }
    //do some security check on halls (that don't have security by normal privs)
    if ($isexec) {
        $userhall=xarModGetUserVar('legis','defaulthall');
        if ($userhall != $itemhall) {
        //no privilege to edit
            return;
        }
    }

    if ((xarSecurityCheck('EditLegis',0,'Item',"$item[cdtitle]:All:$item[cdid]") && $item['vetostatus']==0) ||
       (xarSecurityCheck('AdminLegis',0,'Item',"$item[cdtitle]:All:$item[cdid]") && $item['vetostatus']==1)
       ) 
    {
       $editlink=xarModURL('legis','admin','modify',array('cdid'=>$cdid));
    }else {
       $editlink='';
    }
    if (xarSecurityCheck('AdminLegis',0,'Item',"$item[cdtitle]:All:$item[cdid]")){
       $vetoedit=1;
    }else {
       $vetoedit=0;
    }
    $item['vetoedit']=$vetoedit;
    $item['editlink']=$editlink;
    $item['module'] = 'legis';
    $hooks = xarModCallHooks('item', 'modify', $cdid, $item);

    /* Return the template variables defined in this function */
    $item['authid']=xarSecGenAuthKey();
    $item['invalid']=$invalid;
    $item['hookoutput']=$hooks;

    return $item;
}
?>