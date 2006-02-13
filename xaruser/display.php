<?php
/**
 * Display an item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */
/**
 * Display an item
 *
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 * 
 * @author the legis module development team
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a genelegis object id (if called by other modules)
 * @param  $args ['exid'] the item id used for this legis module
 */
function legis_user_display($args)
{
   extract($args);

    if (!xarVarFetch('cdid', 'id', $cdid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $cdid = $objectid;
    }
    if (!xarSecurityCheck('ReadLegis', 1)) return;
  //  $data = xarModAPIFunc('legis', 'user', 'menu');
    /* Prepare the variable that will hold some status message if necessary */
    $data['status'] = '';

    //Get the category halls
    $halldata=xarModAPIFunc('legis','user','getsethall');

    $item = xarModAPIFunc('legis','user','get',array('cdid' => $cdid,
                                                     'dochall' => $halldata['defaulthall']));


    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */
    
    $legistype= $item['mdid'];
    $legistypedata=xarModAPIFUnc('legis','user','getmaster',array('mdid'=>$legistype));

   $data['halls']=$halldata['halls'];

   // $labeldata=xarModAPIFunc('legis','user','getlabelinfo',array('mdid'=>$item['mdid']));
    //$data['beitlabel']=$labeldata['beitlabel'];

   //Get common status information
    $statusdata=xarModAPIFunc('legis','user','getstatusinfo');
    $stateoptions=$statusdata['stateoptions'];
        $data['stateoptions']=$stateoptions;
    $voteoptions= $statusdata['voteoptions'];
        $data['voteoptions']=$voteoptions;
    $vetooptions= $statusdata['vetooptions'];
        $data['vetooptions']=$vetooptions;
    $authortypes= $statusdata['authortypes'];
        $data['authortypes']=$authortypes;

    $documentdata = unserialize($item['doccontent']);
    $contributors=unserialize($item['contributors']);

    //get the doclets list
    $docletlist=xarModAPIFunc('legis','user','getdoclets');
    
    //get the options
    foreach ($stateoptions as $k => $v) {
      if ($item['docstatus']==$k) $documentdata['docdisplaystatus']=$v;
    }

    foreach ($voteoptions as $k => $v) {
      if ($item['votestatus']==$k) $documentdata['votedisplaystatus']=$v;
    }
    foreach ($vetooptions as $k => $v) {
      if ($item['vetostatus']==$k) $documentdata['vetodisplaystatus']=$v;
    }

    $documentclauses=$legistypedata['mddef'];
    $documentclauses=unserialize($documentclauses);

    $currentdoclets=array();
    foreach ($documentclauses as $k => $v) {
                  $docletno=(int)$v;
                  $currentdoclets[$docletno]=array('clausename'=>$docletlist[$docletno]['dname'],
                                                   'label1'    =>$docletlist[$docletno]['dlabel'],
                                                   'label2'    =>$docletlist[$docletno]['dlabel2']);
           }

  //  $citem['module'] = 'legis';
  //  $citem['itemtype'] = $legistype;
    foreach ($currentdoclets as $doclet=>$dclause) {
      $currentdoclets[$doclet]['data']=$documentdata[$doclet];
   //   foreach ($documentdata[$doclet] as $v=>$k) {
  //        $citem['transform']=array($k);
   //       $citem=xarModCallHooks('item','transform',$cdid,$citem);
   //       $currentdoclets[$doclet]['datatransform'][$v]=$citem['transform'][0];
    //   }
      $currentdoclets[$doclet]['clauseno']=count($documentdata[$doclet]);

    }

    xarTplSetPageTitle(xarVarPrepForDisplay($item['cdtitle']));
    $data['currentdoclets']=$currentdoclets;

    $data['cdtitle_value'] = $item['cdtitle'];

    $data['cdid'] = $cdid;
    $data['item']=$item;

    xarVarSetCached('Blocks.legis', 'cdid', $cdid);

    $item['returnurl'] = xarModURL('legis','user','display',array('cdid' => $cdid));
    $hooks = xarModCallHooks('item','display',$cdid,$item);
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } else {
        $data['hookoutput'] = $hooks;
    }
    $isexec=xarModAPIFunc('legis','user','checkexecstatus');
    if (!xarUserIsLoggedIn() || !$isexec) {
      $data['cansethall']=true;
    } else {
      $data['cansethall']=false;
    }

    if (xarSecurityCheck('EditLegis',0,'Item',"$item[cdtitle]:All:$item[cdid]") && $item['vetostatus']==0) { //fix this - refine the edit check later
       $editlink=xarModURL('legis','admin','modify',array('cdid'=>$cdid));
    }else {
       $editlink='';
    }
    $data['editlink']=$editlink;
    $data['dochallname']=$halldata['defaulthalldata']['name'];
    $data['documentdata']=$documentdata;
    $data['contributors']=$contributors;
    $data['legistypedata']=$legistypedata;
    $data['halls']=$halldata['halls'];
    $data['authortypes']=$authortypes;

    /* Return the template variables defined in this function */
    return $data;
}
?>