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

function publications_admin_modify_pubtype($args)
{
    if (!xarSecurityCheck('AdminPublications')) return;

    extract($args);

    // Get parameters
    if (!xarVarFetch('itemid',     'isset', $data['itemid'], NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('returnurl',  'str:1', $data['returnurl'], 'view', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('name',       'str:1', $name, '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('tab',        'str:1', $data['tab'], '', XARVAR_NOT_REQUIRED)) {return;}
    
    if (empty($name) && empty($itemid)) return xarResponse::NotFound();

    // Get our object
    $data['object'] = DataObjectMaster::getObject(array('name' => 'publications_types'));
    if(!empty($data['itemid'])) {
        $data['object']->getItem(array('itemid' => $data['itemid']));
    } else {
        $type_list = DataObjectMaster::getObjectList(array('name' => 'publications_types'));
        $where = 'name = ' . $name;
        $items = $type_list->getItems(array('where' => $where));
        $item = current($items);
        $data['object']->getItem(array('itemid' => $item['id']));
    }

    // Send the publication type and the object properties to the template 
    $data['properties'] = $data['object']->getProperties();
    
    // Get the settings of the publication type we are using
    $data['settings'] = xarModAPIFunc('publications','user','getsettings',array('ptid' => $data['itemid']));
    
    return $data;
}

?>