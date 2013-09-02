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

sys::import('modules.dynamicdata.class.objects.master');

function publications_admin_modify_pubtype($args)
{
    if (!xarSecurityCheck('AdminPublications')) return;

    extract($args);

    // Get parameters
    if (!xarVarFetch('itemid',     'isset', $data['itemid'],    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('returnurl',  'str:1', $data['returnurl'], 'view', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('name',       'str:1', $name,              '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('tab',        'str:1', $data['tab'],       '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('confirm',    'bool',  $data['confirm'],   false, XARVAR_NOT_REQUIRED)) return;
    
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

    // Unpack the access data
    $data['access'] = unserialize($data['object']->properties['access']->value);
    if (empty($data['access'])) {
        $data['access'] = array(
                            'add' => array(),
                            'display' => array(),
                            'modify' => array(),
                            'delete' => array(),
                            );
        $data['object']->properties['access']->value =serialize($data['access']);
    }
    // Get the settings of the publication type we are using
    $data['settings'] = xarMod::apiFunc('publications','user','getsettings',array('ptid' => $data['itemid']));
    
    // Send the publication type and the object properties to the template 
    $data['properties'] = $data['object']->getProperties();
    
    if ($data['confirm']) {
    
        // Check for a valid confirmation key
        if(!xarSecConfirmAuthKey()) return;

        // Get the data from the form
        $isvalid = $data['object']->checkInput();
        
        // Get the default access rules
        $access = DataPropertyMaster::getProperty(array('name' => 'access'));
        $access->initialization_group_multiselect = true;
        $access->validation_override = true;
        $validprop = $access->checkInput("access_add");
        $addaccess = $access->value;
        $isvalid = $isvalid && $validprop;
        $validprop = $access->checkInput("access_display");
        $displayaccess = $access->value;
        $isvalid = $isvalid && $validprop;
        $validprop = $access->checkInput("access_modify");
        $modifyaccess = $access->value;
        $isvalid = $isvalid && $validprop;
        $validprop = $access->checkInput("access_delete");
        $deleteaccess = $access->value;
        $isvalid = $isvalid && $validprop;
        $allaccess = array(
            'add' => $addaccess,
            'display' => $displayaccess,
            'modify' => $modifyaccess,
            'delete' => $deleteaccess,
        );
        $data['object']->properties['access']->value = serialize($allaccess);

        if (!$isvalid) {
            // Bad data: redisplay the form with error messages
            return xarTplModule('publications','admin','modify_pubtype', $data);        
        } else {
            // Good data: create the item
            $itemid = $data['object']->updateItem(array('itemid' => $data['itemid']));
            
            // Jump to the next page
            xarController::redirect(xarModURL('publications','admin','view_pubtypes'));
            return true;
        }
    }

    return $data;
}

?>