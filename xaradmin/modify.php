<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
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
    if (!xarSecurityCheck('EditPublications')) return;

    extract($args);

    // Get parameters
    if (!xarVar::fetch('itemid',     'isset', $data['itemid'], NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVar::fetch('ptid',       'isset', $ptid, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVar::fetch('returnurl',  'str:1', $data['returnurl'], 'view', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVar::fetch('name',       'str:1', $name, '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVar::fetch('tab',        'str:1', $data['tab'], '', XARVAR_NOT_REQUIRED)) {return;}
    
    if (empty($name) && empty($ptid)) return xarResponse::NotFound();

    if(!empty($ptid)) {
        $publication_type = DataObjectMaster::getObjectList(array('name' => 'publications_types'));
        $where = 'id = ' . $ptid;
        $items = $publication_type->getItems(array('where' => $where));
        $item = current($items);
        $name = $item['name'];
    }

# --------------------------------------------------------
#
# Get our object
#
    $data['object'] = DataObjectMaster::getObject(array('name' => $name));
    $data['object']->getItem(array('itemid' => $data['itemid']));
    $data['ptid'] = $data['object']->properties['itemtype']->value;
    
    // Send the publication type and the object properties to the template 
    $data['properties'] = $data['object']->getProperties();
    
    // Get the settings of the publication type we are using
    $data['settings'] = xarMod::apiFunc('publications','user','getsettings',array('ptid' => $data['ptid']));
    
# --------------------------------------------------------
#
# If creating a new translation get an empty copy
#
    if ($data['tab'] == 'newtranslation') {
        $data['object']->properties['id']->setValue(0);
        $data['object']->properties['parent']->setValue($data['itemid']);
        $data['items'][0] = $data['object']->getFieldValues(array(),1);
        $data['tab'] = '';
    } else {
        $data['items'] = array();
    }

# --------------------------------------------------------
#
# Get the base document. If this itemid is not the base doc,
# then first find the correct itemid
#
    $data['object']->getItem(array('itemid' => $data['itemid']));
    $fieldvalues = $data['object']->getFieldValues(array(),1);
    if (!empty($fieldvalues['parent'])) {
        $data['itemid'] = $fieldvalues['parent'];
        $data['object']->getItem(array('itemid' => $data['itemid']));
        $fieldvalues = $data['object']->getFieldValues(array(),1);
    }
    $data['items'][$data['itemid']] = $fieldvalues;

# --------------------------------------------------------
#
# Get any translations of the base document
#
    $data['objectlist'] = DataObjectMaster::getObjectList(array('name' => $name));
    $where = "parent = " . $data['itemid'];
    $items = $data['objectlist']->getItems(array('where' => $where));
    foreach ($items as $key => $value) {
        // Clear the previous values before starting the next round
        $data['object']->clearFieldValues();
        $data['object']->getItem(array('itemid' => $key));
        $data['items'][$key] = $data['object']->getFieldValues(array(),1);
    }

# --------------------------------------------------------
#
# Get information on next and previous items
#
    $data['prevpublication'] = xarMod::apiFunc('publications','user','getprevious',
                                 array('id' => $data['itemid'],
                                       'ptid' => $ptid,
                                       'sort' => 'tree',));
    $data['nextpublication'] = xarMod::apiFunc('publications','user','getnext',
                                 array('id' => $data['itemid'],
                                       'ptid' => $ptid,
                                       'sort' => 'tree',));
    return $data;
}

?>