<?php
/**
 * Standard function to view items
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Standard function to view items
 *
 * @author jojodee
 * @param int startnum
 * @return array
 */
function legis_admin_view()
{
   if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('docstatus', 'int:1:', $docstatus, null, XARVAR_NOT_REQUIRED)) return;
   if (!isset($docstatus)) $docstatus=1;

    $data = xarModAPIFunc('legis', 'admin', 'menu');

    $data['items'] = array();
    $data['status'] = '';
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

        $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('legis', 'user', 'countitems'),
        xarModURL('legis', 'admin', 'view', array('startnum' => '%%')),
        xarModGetVar('legis', 'itemsperpage'));
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
  
    $halldata=xarModAPIFunc('legis','user','getsethall');

    $defaulthalldata=$halldata['defaulthalldata'];
    $defaulthall=$halldata['defaulthall'];
    $halls=$halldata['halls'];


    if (!xarSecurityCheck('EditLegis')) return;

    $items = xarModAPIFunc('legis','user','getall',
                            array('startnum' => $startnum,
                                  'docstatus' => $docstatus,
                                  'dochall'=>$defaulthall,
                                  'numitems' => xarModGetVar('legis','itemsperpage')));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

     for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        //We can only edit this up to the point prior to final veto
        //Who can edit? Only Admin?
        if (xarSecurityCheck('ReadLegis', 0, 'Item', "$item[cdtitle]:All:$item[cdid]")) {
            $items[$i]['viewurl'] = xarModURL('legis','admin','modify',array('cdid' => $item['cdid']));
        } else {
            $items[$i]['viewurl'] = '';
        }

        if ($item['vetostatus']==0 &&
                  xarSecurityCheck('EditLegis', 0, 'Item', "$item[cdtitle]:All:$item[cdid]")) {
            $items[$i]['editurl'] = xarModURL('legis','admin','modify',
                          array('cdid' => $item['cdid']));
        } elseif ($item['vetostatus']>0) {
            $items[$i]['editurl'] = '';
        } else{
          $items[$i]['editurl'] = '';
        }

        if (xarSecurityCheck('DeleteLegis', 0, 'Item', "$item[cdtitle]:All:$item[cdid]")
            && ($item['docstatus']!=2)) { //Only documents that are pending can be deleted
            $items[$i]['deleteurl'] = xarModURL('legis','admin','delete',
                array('cdid' => $item['cdid']));
        } else {
            $items[$i]['deleteurl'] = '';
        }
        /* Set the status for each item */
        
        foreach ($stateoptions as $k => $v) {
          if ($item['docstatus']==$k) $items[$i]['docdisplaystatus']=$v;
        }

        foreach ($voteoptions as $k => $v) {
          if ($item['votestatus']==$k) $items[$i]['votedisplaystatus']=$v;
        }
        foreach ($vetooptions as $k => $v) {
          if ($item['vetostatus']==$k) $items[$i]['vetodisplaystatus']=$v;
        }
        foreach ($halls as $k => $v) {
          if ($item['dochall'] == $v['cid']) {
             $items[$i]['hallname']=$v['name'];
          }
        }
    }
    $data['halls']=$halls;
    $data['defaulthall']=$defaulthall;
    $data['defaulthalldata']=$defaulthalldata;

    $isexec=xarModAPIFunc('legis','user','checkexecstatus');
    if (!xarUserIsLoggedIn() || !$isexec) {
      $data['cansethall']=true;
    } else {
      $data['cansethall']=false;
    }

    $data['legistypes']=xarModAPIFunc('legis','user','getmastertypes');
    $uid = xarUserGetVar('uid');
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('legis', 'user', 'countitems'),
        xarModURL('legis', 'admin', 'view', array('startnum' => '%%')),
        xarModGetUserVar('legis', 'itemsperpage', $uid));


    /* Add the array of items to the template variables */
    $data['items'] = $items;

    /* Return the template variables defined in this function */
    return $data;

}
?>
