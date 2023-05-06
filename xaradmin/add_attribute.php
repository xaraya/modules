<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Add attributes to an obnject
 */
function eav_admin_add_attribute(Array $args=array())
{
    if (!xarSecurity::check('ManageEAV')) return;

	if(!xarVar::fetch('objectname', 'isset', $data['objectname'],   NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('objectid',   'isset', $data['objectid'],   NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('details',    'isset', $details,  NULL, xarVar::DONT_SET)) {return;}
    if(!xarVar::fetch('layout',     'str:1', $data['layout'],   'default', xarVar::NOT_REQUIRED)) {return;}

    //if (empty($data['objectid'])) return xarResponse::NotFound();
    sys::import('modules.dynamicdata.class.objects.master');
	
	if (!empty($data['objectname'])) {
    	$info = DataObjectMaster::getObjectInfo(array('name' => $data['objectname']));
    	$data['objectid'] = $info['objectid'];
	}
	
    sys::import('modules.dynamicdata.class.objects.master');
    $data['object'] = DataObjectMaster::getObject(array('objectid' => $data['objectid']));

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSec::genAuthKey();

    xarTpl::setPageTitle(xarML('Modify DataProperties #(1)', $data['object']->label));

    $data['fields'] = xarMod::apiFunc('eav','user','getattributes',
                                   array('object_id' => $data['objectid']));

    $isprimary = 0;
    foreach (array_keys($data['fields']) as $field) {
        // replace newlines with [LF] for textbox
        if (!empty($data['fields'][$field]['defaultvalue']) && preg_match("/\n/",$data['fields'][$field]['defaultvalue'])) {
            // Note : we could use addcslashes here, but that could lead to a whole bunch of other issues...
            $data['fields'][$field]['defaultvalue'] = preg_replace("/\r?\n/",'[LF]',$data['fields'][$field]['defaultvalue']);
        }
    }

    $data['fieldtypeprop'] =& DataPropertyMaster::getProperty(array('type' => 'fieldtype'));
    $data['fieldstatusprop'] =& DataPropertyMaster::getProperty(array('type' => 'fieldstatus'));

    // We have to specify this here, the js expects non xml urls and the => makes the template invalied
    $data['urlform'] = xarController::URL('dynamicdata','admin','form',array('objectid' => $data['objectid'], 'theme' => 'print'),false);

    return $data;
}

?>
