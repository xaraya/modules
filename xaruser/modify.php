<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * modify publication
 * @param int id The ID of the publication
 * @param string return_url
 * @param int preview
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_user_modify($args)
{
    extract($args);

    // Get parameters
    if (!xarVarFetch('itemid',     'isset', $id, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('return_url', 'str:1', $data['return_url'], NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('name',       'str:1', $name, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('tab',        'str:1', $data['tab'], '', XARVAR_NOT_REQUIRED)) {return;}

    // FIXME: this is too clumsy
    
    // Get our object
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));

    // If creating a new translation get an empty copy
    if ($data['tab'] == 'newtranslation') {
        $data['object']->properties['parent']->setValue($id);
        $data['items'][0] = $data['object']->getFieldValues();
        $data['tab'] = '';
    } else {
        $data['items'] = array();
    }

    // Get the base document
    $data['object']->getItem(array('itemid' => $id));
    $data['items'][$id] = $data['object']->getFieldValues();

    // Get any translations of the base document
    $data['objectlist'] = DataObjectMaster::getObjectList(array('name' => $name));
    $where = "parent = " . $id;
    $items = $data['objectlist']->getItems(array('where' => $where));
    foreach ($items as $key => $value) {
        $data['object']->getItem(array('itemid' => $key));
        $data['items'][$key] = $data['object']->getFieldValues();
    }
    
    if (!empty($ptid)) {
        $template = $pubtypes[$ptid]['name'];
    } else {
// TODO: allow templates per category ?
       $template = null;
    }

    // Send the publication type and the object properties to the tempate 
    $data['properties'] = $data['object']->getProperties();
    $data['ptid'] = $data['properties']['itemtype']->value;
    
    // Get the settings of the publication type we are using
    $data['settings'] = xarModAPIFunc('publications','user','getsettings',array('ptid' => $data['ptid']));
    
    return xarTplModule('publications', 'user', 'modify', $data, $template);



     $ptid = $publication['pubtype_id'];
    if (!isset($ptid)) {
       $ptid = '';
    }
    $data = array();
    $data['ptid'] = $ptid;
    $data['id'] = $id;

    $pubtypes = xarModAPIFunc('publications','user','getpubtypes');

    // Security check
    $input = array();
    $input['publication'] = $publication;
    $input['mask'] = 'EditPublications';
    if (!xarModAPIFunc('publications','user','checksecurity',$input)) {
        $msg = xarML('You have no permission to modify #(1) item #(2)',
                     $pubtypes[$ptid]['descr'], xarVarPrepForDisplay($id));
        throw new ForbiddenOperationException(null, $msg);
    }
    unset($input);

}

?>