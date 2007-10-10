<?php
/**
 * Delete a response
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Standard function to Delete an item
 *
 * @param  $ 'scrid' the id of the item to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function sitecontact_admin_delete($args)
{
    extract($args);

    if (!xarVarFetch('scrid',    'id', $scrid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',  'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $exid = $objectid;
    }
    $item = xarModAPIFunc('sitecontact','user','get', array('scrid' => $scrid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('DeleteSiteContact', 1, 'ContactForm', "$item[scid]:All:All")) {
        return;
    }
    $lastview = xarSessionGetVar('Sitecontact.LastView');
    if (!empty($lastview)) {
        $lastview= unserialize($lastview);
    }
    $scformtypes = xarModAPIFunc('sitecontact','user','getcontacttypes');

    /* Check for confirmation. */
    if (empty($confirm)) {
        $data['scrid'] = $scrid;
        $data['scid'] = $item['scid'];
        $data['itemid'] = xarML('Response ID');
        $data['username'] = xarVarPrepForDisplay($item['username']);
    // Create filters based on publication type
    $formfilters = array();
    foreach ($scformtypes as $id => $formtype) {
        if (!xarSecurityCheck('EditSiteContact',0,'ContactForm',"$formtype[scid]:All:All")) {
            continue;
        }
        $responseitem = array();
        if ($formtype['scid'] != $item['scid']) {
            $responseitem['flink'] = xarModURL('sitecontact','admin','view',
                                         array('scid' => $formtype['scid']));
            $responseitem['current']=false;
        }else{
            $responseitem['flink'] = xarModURL('sitecontact','admin','view',
                                         array('scid' => $lastview['scid'],
                                               'startnum'=> $lastview['startnum']));
            $responseitem['current']=true;
        }
        $responseitem['ftitle'] = $formtype['sctypename'];
        $formfilters[] = $responseitem;
    }
        $data['formfilters'] = $formfilters;
        /* Generate a one-time authorisation code for this operation */
        $data['authid'] = xarSecGenAuthKey();
        $data['returnurl']=xarModURL('sitecontact','admin','view',
                                         array('scid' => $lastview['scid'],
                                               'startnum'=> $lastview['startnum']));
        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    
    $item = xarModAPIFunc('sitecontact','user','getcontacttypes', array('scid'=>$item['scid']));
    $forminfo=$item[0];
    $info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('name'=> $forminfo['sctypename']));
    $thisobject = xarModAPIFunc('dynamicdata','user','getobject', array('objectid' => $info['objectid']));

    $args['module']= 'dynamicdata';
    $args['itemtype']= $thisobject->itemtype;
    $args['itemid']= $scrid;

    //delete the DD item
    $itemid = $thisobject->deleteItem($args);

    if (empty($itemid)) return;


    if (!xarModAPIFunc('sitecontact','admin','delete', array('scrid' => $scrid))) {
        return; // throw back
    }
    if (!isset($scid)) $scid=xarModVars::get('sitecontact','defaultform');


    xarResponseRedirect(xarModURL('sitecontact', 'admin', 'view',array('scid'=>$scid)));

    /* Return */
    return true;
}
?>