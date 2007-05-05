<?php
    sys::import('modules.dynamicdata.class.objects.base');

    class Member extends DataObject
    {
        function createItem(Array $args = array())
        {
            $this->checkInput();

            // find the property with the datasource that is the roles table uname field
            xarModAPILoad('roles');
            $xartable = xarDB::getTables();
            $uname = "";
            $properties = $this->getProperties();
            foreach ($properties as $property) {
                if ($property->source == $xartable['roles'] . '.uname') {
                    $uname = $property->name;
                    break;
                }
            }

            // if there is such a property give it a custom value
            if (!empty($uname)) {
               $unamevalue = xarModAPIFunc('members','user','setname');
               $this->properties[$uname]->setValue($unamevalue);
            }

            // create this item
            $id = parent::createItem();

            // add this member to the designated group
            if (!xarVarFetch('tplmodule', 'str', $tplmodule, 'members', XARVAR_NOT_REQUIRED)) {return;}
            $role = xarRoles::get($id);
            $parent = xarRoles::get(xarModGetUserVar('members','defaultgroup',xarMod::getRegID($tplmodule)));
            $parent->addMember($role);

           return $id;
        }

    }
?>
