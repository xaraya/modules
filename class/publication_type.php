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

sys::import('modules.dynamicdata.class.objects.base');

class PublicationType extends DataObject
{
    public function checkInput(Array $args = array(), $suppress=0, $priority='dd')
    {
        // The access property is ignored here
        $isvalid = parent::checkInput($args,$suppress,$priority);

        // If the rest of the publication is valid, then do the access part
        // Note this is a collection of access properties; hence the complicated process of saving it
        if ($isvalid) {
            $access = DataPropertyMaster::getProperty(array('name' => 'access'));
            $access->initialization_group_multiselect = true;
            $access->validation_override = true;

            $validprop = $access->checkInput("access_add");
            $addaccess = $access->getValue();
            $validprop = $access->checkInput("access_display");
            $displayaccess = $access->getValue();
            $isvalid = $isvalid && $validprop;
            $validprop = $access->checkInput("access_modify");
            $modifyaccess = $access->getValue();
            $isvalid = $isvalid && $validprop;
            $validprop = $access->checkInput("access_delete");
            $deleteaccess = $access->getValue();
            $isvalid = $isvalid && $validprop;
            $allaccess = array(
                'add'     => $addaccess,
                'display' => $displayaccess,
                'modify'  => $modifyaccess,
                'delete'  => $deleteaccess,
            );
            $this->properties['access']->setValue($allaccess);
        }
        return $isvalid;
    }

    function createItem(Array $args = array())
    {
        // Make sure we can save the access property
        $this->properties['access']->setInputStatus(DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY);

        // Create the item
        $id = parent::createItem($args);
        return $id;
    }

    function updateItem(Array $args = array())
    {
        // Make sure we can save the access property
        $this->properties['access']->setInputStatus(DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY);
        
        // Save the item
        $id = parent::updateItem($args);
        return $id;
    }

    public function getLabel()
    {
        $settings = xarMod::apiFunc('publications', 'user', 'getsettings', array('ptid' => $this->properties['id']->value));
        if (!empty($settings['alias'])) {
            return $settings['alias'];
        } else {
            return $this->description;
        }
        
    }
}
?>