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

function publications_admin_modify($args)
{
    if (!xarSecurityCheck('ManagePublications')) return;

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
    
    //FIXME This should be configuration in the celko property itself

    $data['object']->properties['position']->initialization_celkoparent_id = 'parentpage_id';
    $data['object']->properties['position']->initialization_celkoright_id = 'rightpage_id';
    $data['object']->properties['position']->initialization_celkoleft_id  = 'leftpage_id';

    $xartable = xarDB::getTables();

    $data['object']->properties['position']->initialization_itemstable = $xartable['publications'];

    // Send the publication type and the object properties to the template 
    $data['properties'] = $data['object']->getProperties();
    
    // Get the settings of the publication type we are using
    $data['settings'] = xarModAPIFunc('publications','user','getsettings',array('ptid' => $data['ptid']));
    
    // If creating a new translation get an empty copy
    if ($data['tab'] == 'newtranslation') {
        $data['object']->properties['id']->setValue(0);
        $data['object']->properties['parent']->setValue($id);
        $data['items'][0] = $data['object']->getFieldValues();
        $data['tab'] = '';
    } else {
        $data['items'] = array();
    }

    // Get the base document
    $data['object']->getItem(array('itemid' => $id));
    $fieldvalues = $data['object']->getFieldValues();
    if (!empty($fieldvalues['parent'])) {
        $id = $fieldvalues['parent'];
        $data['object']->getItem(array('itemid' => $id));
        $fieldvalues = $data['object']->getFieldValues();
    }
    $data['items'][$id] = $fieldvalues;

    // Get any translations of the base document
    $data['objectlist'] = DataObjectMaster::getObjectList(array('name' => $name));
    $where = "parent = " . $id;
    $items = $data['objectlist']->getItems(array('where' => $where));
    foreach ($items as $key => $value) {
        // Clear the previous values before starting the next round
        $data['object']->clearFieldValues();
        $data['object']->getItem(array('itemid' => $key));
        $data['items'][$key] = $data['object']->getFieldValues();
    }

    return xarTplModule('publications', 'admin', 'modify', $data);

// ----------------------------------------------------------------------------
    $ptid = $publication['pubtype_id'];

    $data = array();
    $data['ptid'] = $ptid;
    $data['id'] = $id;

    $pubtypes = xarModAPIFunc('publications','user','get_pubtypes');

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