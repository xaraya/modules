<?php
/**
 * Realms Module
 *
 * @package modules
 * @subpackage realms module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

sys::import('modules.dynamicdata.class.objects.base');
sys::import('modules.dynamicdata.class.objects.list');

class Realm extends DataObject
{

#---------------------------------------------------------
# Constructor
#
    function __construct(DataObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
    }

#---------------------------------------------------------
# Create, update, delete, purge
#
    function createItem(Array $args = array())
    {
/*
        // Create a group in roles for this realm
        $groupobject = DataObject::getObject(array('name' => 'roles_groups'));
        if (!isset($args['name'])) $name = $this->properties['name']->value;
        if (empty($name)) throw new Exception('The realm to be created does not have a name');
        $name = $name . '_' . xarML('Users');
        $itemtype = xarRoles::ROLES_GROUPTYPE;

        $args['usergroup'] = $groupobject->createItem(array('name' => $name, 'role_type' => $itemtype));
*/        
        if(!empty($args['itemid'])) $this->itemid = $args['itemid'];

        // Create the mandant item 
        $itemid = parent::createItem($args);

/*
        // Remove it from the default users' group
        xarRemoveRoleMemberByID($args['usergroup'], xarModVars::get('roles', 'defaultgroup'));

        // Add it to the Everybody group
        xarMakeRoleMemberByName($name, 'Everybody');     
*/            
        return $itemid;
    }

    function deleteItem(Array $args = array())
    {
        if(!empty($args['itemid'])) $this->itemid = $args['itemid'];

        if(empty($this->itemid))
        {
            $msg = xarML('Invalid item id in method #(1)() for dynamic object [#(2)] #(3)','deleteItem',$this->objectid,$this->name);
            throw new Exception($msg);
        }

/*
        $itemid = $this->getItem(array('itemid' => $this->itemid));
        $item = $this->getFieldValues();
        $groupobject = DataObject::getObject(array('name' => 'roles_groups'));
        $groupobject->getItem(array('itemid'=> $item['usergroup']));
        $name = $groupobject->properties['name']->getValue() . '_' . time();
        $args['usergroup'] = $groupobject->updateItem(array('name' => $name, 'state' => 0));
*/        

        // Delete this item
        $itemid = parent::deleteItem($args);
        return $itemid;
    }
}
class RealmList extends DataObjectList
{
}
?>