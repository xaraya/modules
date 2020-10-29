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

class Publication extends DataObject
{
    public function __construct(DataObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        
        // If we allow multilanguage, then turn the locale property into type languages
        if (xarModVars::get('publications', 'multilanguage')) {
            if (isset($this->properties['locale']) && DataPropertyMaster::isAvailable('languages')) {
                $this->properties['locale']->setInputStatus(DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE);
                $args = $this->properties['locale']->getPublicProperties();
                $args['type'] = 30039;      // languages property
                $args['status'] = 34;       // display status
                $this->properties['locale'] = DataPropertyMaster::getProperty($args);
            }
        }
    }
    
    public function checkInput(array $args = array(), $suppress=0, $priority='dd')
    {
        // The access property is ignored here
        $isvalid = parent::checkInput($args, $suppress, $priority);

        // If the rest of the publication is valid, then do the access part
        // Note this is a collection of access properties; hence the complicated process of saving it
        if ($isvalid) {
            $access = DataPropertyMaster::getProperty(array('name' => 'access'));
            $access->initialization_group_multiselect = true;
            $access->validation_override = true;
            $prefix = $this->getFieldPrefix();

            // Only ignore the prefix if we are CREATING the base document
            // A translation would have a prefix of 0, which is valid
            if (empty($prefix) && $prefix !== '0') {
                $name = $this->propertyprefix . $this->properties['access']->id;
            } else {
                $name = $prefix . "_" . $this->propertyprefix . $this->properties['access']->id;
            }
            $validprop = $access->checkInput($name . "_display");
            $displayaccess = $access->getValue();
            $isvalid = $isvalid && $validprop;
            $validprop = $access->checkInput($name . "_modify");
            $modifyaccess = $access->getValue();
            $isvalid = $isvalid && $validprop;
            $validprop = $access->checkInput($name . "_delete");
            $deleteaccess = $access->getValue();
            $isvalid = $isvalid && $validprop;
            $allaccess = array(
                'display' => $displayaccess,
                'modify'  => $modifyaccess,
                'delete'  => $deleteaccess,
            );
            $this->properties['access']->setValue($allaccess);
        }
        return $isvalid;
    }

    public function createItem(array $args = array())
    {
        // Save the access property
        $this->properties['access']->setInputStatus(DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY);

        // If multilanguages is not enabled just save the default language value
        if (!xarModVars::get('publications', 'multilanguage')) {
            if (isset($this->properties['locale'])) {
                $this->properties['locale']->setInputStatus(DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY);
            }
        }

        // Ignore the position if this isn't the base document
        if (empty($this->properties['parent']->value)) {
            $this->properties['position']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISPLAYONLY);
        } else {
            $this->properties['position']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
        }
        $this->fieldlist = array();

        $id = parent::createItem($args);
        return $id;
    }

    public function updateItem(array $args = array())
    {
        if (xarModVars::get('publications', 'use_versions')) {
            $temp = $this->getFieldValues(array(), 1);
            $this->getItem(array('itemid' => $this->properties['id']->value));
            $operation = xarML('Update');
            xarMod::apiFunc('publications', 'admin', 'save_version', array('object' => $this, 'operation' => $operation));
            $this->setFieldValues($temp, 1);
            $this->properties['version']->value++;
        }

        // Save the access property
        $this->properties['access']->setInputStatus(DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY);
        
        // Ignore the position if this isn't the base document
        if (empty($this->properties['parent']->value)) {
            $this->properties['position']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISPLAYONLY);
        } else {
            $this->properties['position']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
        }
        $this->fieldlist = array();
        
        // Set the time modified
        $this->properties['modified']->value = time();
        
        // Save the item
        $id = parent::updateItem($args);
        
        return $id;
    }
}
