<?php
/**
 * Display a response
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

    //$data = xarModAPIFunc('sitecontact', 'admin', 'menu');
    
    $data['status'] = '';
    
    $item = xarModAPIFunc('sitecontact','user','get',array('scrid' => $scrid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */
    $scid=$item['scid'];
    $item['itemtype'] = $scid;

    if (!xarSecurityCheck('EditSiteContact',0,'ContactForm',"$scid:All:All")) {
        return; // todo: something
    }
    
    $thisform = xarModAPIFunc('sitecontact','user','getcontacttypes',array('scid'=>$scid));
    $thisform=$thisform[0];

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
    
    xarVarSetCached('Blocks.sitecontact', 'scrid', $scrid);

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
    return $data;
}
?>