<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2011 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_admin_create()
{
    if (!xarVarFetch('ptid',       'id',    $data['ptid'])) {return;}
    if (!xarVarFetch('new_cids',   'array', $cids,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('preview',    'str',   $data['preview'], NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('save',       'str',   $save, NULL, XARVAR_NOT_REQUIRED)) {return;}
    

    // Confirm authorisation code
    // This has been disabled for now
    // if (!xarSecConfirmAuthKey()) return;

    $data['items'] = array();
    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $data['ptid']));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
    
    $isvalid = $data['object']->checkInput();
    
    $data['settings'] = xarModAPIFunc('publications','user','getsettings',array('ptid' => $data['ptid']));
    
    if ($data['preview'] || !$isvalid) {
        // Show debug info if called for
        if (!$isvalid && 
            xarModVars::get('publications','debugmode') && 
            in_array(xarUserGetVar('uname'),xarConfigVars::get(null, 'Site.User.DebugAdmins'))) {
            var_dump($data['object']->getInvalids());}
        // Preview or bad data: redisplay the form
        $data['properties'] = $data['object']->getProperties();
        if ($data['preview']) $data['tab'] = 'preview';
        return xarTplModule('publications','admin','new', $data);    
    }
    
    // Create the object
    $id = $data['object']->createItem();

    // if we can edit publications, go to admin view, otherwise go to user view
    if (xarSecurityCheck('EditPublications',0,'Publication',$data['ptid'].':All:All:All')) {
        xarController::redirect(xarModURL('publications', 'admin', 'view',
                                      array('ptid' => $data['ptid'])));
    } else {
        xarController::redirect(xarModURL('publications', 'user', 'view',
                                      array('ptid' => $data['ptid'])));
    }

    return true;
}

?>
