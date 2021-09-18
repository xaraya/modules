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

class Member extends DataObject
{
    #---------------------------------------------------------
    # Constructor
#
    public function __construct(DataObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
    }

    #---------------------------------------------------------
    # Create, update, delete, purge
#
    public function createItem(array $args = [])
    {
        // If the flag is set not to link, remove the roles source from this object
        if ($this->properties['role_link']->value) {
            $fields = [
                'name'          => $this->properties['name']->getValue(),
                'role_type'     => xarRoles::ROLES_USERTYPE,
                'uname'         => $this->properties['uname']->value,
                'email'         => $this->properties['state']->value,
                'password'      => $this->properties['password']->value,
                'regdate'       => time(),
                'valcode'       => 'createdbyprogram',
                'state'         => $this->properties['state']->value,
                'authmodule'    => xarMod::getID('realms'),
            ];
            $roleobject = DataObject::getObject(['name' => 'roles_users']);
            $roleid = $roleobject->createItem($fields);
            $this->properties['role_id']->value = $roleid;
        }
        // Create the member item
//        echo $this->properties['name']->value;exit;
        $itemid = parent::createItem($args);

        return $itemid;
    }

    public function updateItem(array $args = [])
    {
        /* For now do not let the member update a role
        if ($this->properties['role_link']->value) {
            $fields = array(
                'id'            => $this->properties['role_id']->value,
                'name'          => $this->properties['name']->getValue(),
                'uname'         => $this->properties['uname']->value,
                'email'         => $this->properties['state']->value,
                'password'      => $this->properties['password']->value,
                'state'         => $this->properties['state']->value,
            );
            $roleobject = DataObject::getObject(array('name' => 'roles_users'));
            $roleid = $roleobject->updateItem($fields);
        }
        */
        $this->properties['role_link']->value = !empty($this->properties['role_id']->value);

        // Update the member item
        $itemid = parent::updateItem($args);

        return $itemid;
    }

    public function deleteItem(array $args = [])
    {
        if (!empty($args['itemid'])) {
            $this->itemid = $args['itemid'];
        }

        /* For now do not let the member update a role
        if ($this->properties['role_link']->value) {
            $fields = array(
                'id'            => $this->properties['role_id']->value,
                'state'         => 0,
            );
            $roleobject = DataObject::getObject(array('name' => 'roles_users'));
            $roleid = $roleobject->updateItem($fields);
        }
        */

        // Delete this item
        $itemid = parent::deleteItem($args);
        return $itemid;
    }
}
