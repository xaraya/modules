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
    if (!xarVarFetch('ptid',       'isset', $ptid, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('returnurl',  'str:1', $data['returnurl'], 'view', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('name',       'str:1', $name, '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('tab',        'str:1', $data['tab'], '', XARVAR_NOT_REQUIRED)) {return;}
    
    if (empty($name) && empty($ptid)) return xarResponse::NotFound();

    if(!empty($ptid)) {
        $publication_type = DataObjectMaster::getObjectList(array('name' => 'publications_types'));
        $where = 'id = ' . $ptid;
        $items = $publication_type->getItems(array('where' => $where));
        $item = current($items);
        $name = $item['name'];
    }

    // Get our object
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $id));
    $data['ptid'] = $data['object']->properties['itemtype']->value;

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
        $template = $item['name'];
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
}

?>
