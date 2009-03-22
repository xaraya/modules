<?php
/**
 * Delete a response
 *
 * @package Xaraya
 * @copyright (C) 2004-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 *
 * @subpackage Xarigami SiteContact Module
 * @copyright (C) 2007,2008,2009 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
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
    $data = array();
    /* Check for confirmation. */
    if (empty($confirm)) {
        $data['scrid'] = $scrid;
        $data['scid'] = $item['scid'];
        $data['itemid'] = xarML('Response ID');
        $data['username'] = xarVarPrepForDisplay($item['username']);
    // Create filters based on publication type
    $formfilters = array();

    //common menulink
    $data['menulinks'] = xarModAPIFunc('sitecontact','admin','getmenulinks');
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

    if (!xarModAPIFunc('sitecontact','admin','delete', array('scrid' => $scrid))) {
        return; // throw back
    }
    if (!isset($scid)) $scid=xarModGetVar('sitecontact','defaultform');


    xarResponseRedirect(xarModURL('sitecontact', 'admin', 'view',array('scid'=>$scid)));

    /* Return */
    return true;
}
?>