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

    $data = xarModAPIFunc('legis', 'user', 'menu');
    /* Prepare the variable that will hold some status message if necessary */
    $data['status'] = '';

    //Get the category halls
    $halldata=xarModAPIFunc('legis','user','getsethall');
   
    $item = xarModAPIFunc('legis','user','get',array('cdid' => $cdid,
                                                     'dochall' => $halldata['defaulthall']));
    $data['dochallname']=$halldata['defaulthalldata']['name'];

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */
    
    $legistype= $item['mdid'];
    $legistypedata=xarModAPIFUnc('legis','user','getmaster',array('mdid'=>$legistype));

    $data['legistypedata']=$legistypedata;

    $labeldata=xarModAPIFunc('legis','user','getlabelinfo',array('mdid'=>$item['mdid']));
    $data['beitlabel']=$labeldata['beitlabel'];

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


        foreach ($stateoptions as $k => $v) {
          if ($item['docstatus']==$k) $documentdata['docdisplaystatus']=$v;
        }

        foreach ($voteoptions as $k => $v) {
          if ($item['votestatus']==$k) $documentdata['votedisplaystatus']=$v;
        }
        foreach ($vetooptions as $k => $v) {
          if ($item['vetostatus']==$k) $documentdata['vetodisplaystatus']=$v;
        }
     $data['documentdata']=$documentdata;
    $item['transform'] = array('cdtitle');
    $item = xarModCallHooks('item','transform',$cdid,$item);

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

    xarTplSetPageTitle(xarVarPrepForDisplay($item['cdtitle']));
    /* Return the template variables defined in this function */
    return $data;
}
?>