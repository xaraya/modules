<?php
/**
 * View a list of items
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
 * View a list of items
 *
 * This is a standard function to provide an overview of all of the items
 * available from the module.
 *
 * @author jojodee
 */
function legis_user_view($args)
{ 
    extract($args);

    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('docstatus', 'int:0:', $docstatus, 2, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('legis', 'user', 'menu');
    if (!isset($docstatus) || empty($docstatus)) $docstatus=2; //default to Valid
    $data['status'] = '';
  //Get common status information
    $statusdata=xarModAPIFunc('legis','user','getstatusinfo');
    $stateoptions=$statusdata['stateoptions'];
    $voteoptions= $statusdata['voteoptions'];
    $vetooptions= $statusdata['vetooptions'];
    $authortypes= $statusdata['authortypes'];

    $data['items'] = array();
    $data['pager'] = '';

    $halldata=xarModAPIFunc('legis','user','getsethall');

    if (!xarSecurityCheck('ViewLegis')) return;

    /* Lets get the UID of the current user to check for overridden defaults */
    $uid = xarUserGetVar('uid');

    $items = xarModAPIFunc('legis','user','getall',
                              array('startnum' => $startnum,
                                    'docstatus'  => $docstatus,
                                    'dochall'=>$halldata['defaulthall'],
                                    'numitems' => xarModGetUserVar('legis','itemsperpage',$uid)));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    foreach ($items as $item) {
        /* Let any transformation hooks know that we want to transform some text
         * list($item['name']) = xarModCallHooks('item','transform',$item['exid'],array($item['name']));
         */
        if (xarSecurityCheck('ReadLegis', 0, 'Item', "$item[cdtitle]:All:$item[cdid]")) {
            $item['link'] = xarModURL('legis','user','display',
                array('cdid' => $item['cdid']));
        } else {
            $item['link'] = '';
        }

        /* Set the status for each item */

        foreach ($stateoptions as $k => $v) {
          if ($item['docstatus']==$k) $item['docdisplaystatus']=$v;
        }

        foreach ($voteoptions as $k => $v) {
          if ($item['votestatus']==$k) $item['votedisplaystatus']=$v;
        }
        foreach ($vetooptions as $k => $v) {
          if ($item['vetostatus']==$k) $item['vetodisplaystatus']=$v;
        }

        /* Clean up the item text before display */
        $item['cdtitle'] = xarVarPrepForDisplay($item['cdtitle']);
        /* Add this item to the list of items to be displayed */
        $data['items'][] = $item;
    }
    $data['legistypes']=xarModAPIFunc('legis','user','getmastertypes');

    $totalitems=count($items);
    $data['docstatus']=$docstatus;
    $data['totalitems']=$totalitems;
    $data['hallname']=ucfirst($halldata['defaulthalldata']['name']);
    $selectarray =array('startnum' => '%%',
                        'docstatus'  => $docstatus,
                        'dochall'=>$halldata['defaulthall']);
    $uid = xarUserGetVar('uid');
    $data['pager'] = xarTplGetPager($startnum,
        xarModAPIFunc('legis', 'user', 'countitems',$selectarray),
        xarModURL('legis', 'user', 'view',$selectarray),
        xarModGetUserVar('legis', 'itemsperpage', $uid));

    $isexec=xarModAPIFunc('legis','user','checkexecstatus');
    if (!xarUserIsLoggedIn() || !$isexec) {
      $data['cansethall']=true;
    } else {
      $data['cansethall']=false;
    }

    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('View legis')));
    /* Return the template variables defined in this function */

    return $data;
}
?>