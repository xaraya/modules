<?php
/**
 * Display a response
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Display a response
 *
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 *
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['scrid'] the item id used for this example module
 * @return array $data The array that contains all data for the template
 */
function sitecontact_admin_display($args)
{
    extract($args);

    if (!xarVarFetch('scrid', 'id', $scrid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $scrid = $objectid;
    }
    $lastview = xarSession::getVar('Sitecontact.LastView');
    if (!empty($lastview)) {
        $lastview= unserialize($lastview);
    }
    //$data = xarModAPIFunc('sitecontact', 'admin', 'menu');

    $data['status'] = '';

    $item = xarModAPIFunc('sitecontact','user','get',array('scrid' => $scrid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */
    $scid=$item['scid'];
    $item['itemtype'] = $scid;

    if (!xarSecurityCheck('EditSiteContact',0,'ContactForm',"$scid:All:All")) {
        return; // todo: something
    }

    $basicobject = DataObjectMaster::getObject(array('name'=> 'sitecontact_basicform'));
    $args['basicitemtype']= $basicobject->itemtype;
    $args['basicitemid']= $scrid;
    $data['basicobject'] = $basicobject;
    
    $thisform = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>$scid));
    $thisform=$thisform[0];

    if ($thisform['sctypename'] != 'sitecontact_basicform') {
        $thisobject = DataObjectMaster::getObject(array('name'=> $thisform['sctypename']));
    } else {
        //we only have the basic form, not as a parent
        $thisobject = $basicobject;
    }
    $data['object'] = $thisobject;
    $args['module']= 'dynamicdata';
    $args['itemtype']= $thisobject->itemtype;
    $args['itemid']= $scrid;
    //keep some data vars for backward compat in templates
    $data['args'] = $args;
    $data['username'] = $item['username'];
    $data['useremail'] = $item['useremail'];
    $data['requesttext'] = $item['requesttext'];
    $data['company'] = $item['company'];
    $data['useremail'] = $item['useremail'];
    $data['usermessage'] = $item['usermessage'];
    $data['useripaddress'] = $item['useripaddress'];
    $data['userreferer'] = $item['userreferer'];
    $data['sendcopy'] = $item['sendcopy'];
    $data['permission'] = $item['permission'];
    $data['bccrecipients'] = isset($item['bccrecipients'])?unserialize($item['bccrecipients']):'';
    $data['ccrecipients'] = isset($item['ccrecipients'])?unserialize($item['ccrecipients']):'';
    $data['responsetime'] = $item['responsetime'];
    $data['scrid'] = $scrid;
    $data['scid'] = $item['scid'];
    $data['formname']=$thisform['sctypename'];
    $data['permissioncheck']=$thisform['permissioncheck'];

    $scformtypes = xarModAPIFunc('sitecontact','user','getcontacttypes');
   // Create filters based on publication type
    $formfilters = array();
    foreach ($scformtypes as $id => $formtype) {
        if (!xarSecurityCheck('EditSiteContact',0,'ContactForm',"$formtype[scid]:All:All")) {
            continue;
        }
        $responseitem = array();
       if ($formtype['scid'] != $scid) {
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
    $data['menuscid']=    xarCore::getCached('Blocks.sitecontact','itemtype');
    $item['returnurl'] = xarModURL('sitecontact','admin','display',array('scrid' => $scrid));
    $item['module'] = 'sitecontact';
    $item['itemid'] = $scrid;
    $item['itemtype'] = $item['scid'];
    $hooks = xarModCallHooks('item','display',$scrid,$item);

    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } else {
        $data['hookoutput'] = $hooks;
    }

    xarTplSetPageTitle(xarVarPrepForDisplay($data['formname']));
    //let's also give a custom template for admin display
    try {
        $templatedata = xarTplModule('sitecontact', 'admin', 'display', $data, $data['formname']);
    } catch (Exception $e) {
        $templatedata = xarTplModule('sitecontact', 'admin', 'display', $data);
    }
    return $templatedata;
}
?>