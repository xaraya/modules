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

sys::import('modules.dynamicdata.class.objects.base');

class Publication extends DataObject
{
    public function __construct(DataObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        
        // If we allow multilanguage, then turn the locale property into type languages
        if (xarModVars::get('publications', 'multilanguage')) {
            if (isset($this->properties['locale'])) {
                $this->properties['locale']->setInputStatus(DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE);
                $this->properties['locale']->type = 30039;      // languages property
            }
        }
    }
    
    public function checkInput(Array $args = array(), $suppress=0, $priority='dd')
    {
        $isvalid = parent::checkInput($args,$suppress,$priority);

        // If the rest of the publication is valid, then do the access part
        // Note this is a collection of access properties; hence the complicated process of saving it
        if ($isvalid) {
            $access = DataPropertyMaster::getProperty(array('name' => 'access'));
            $prefix = $this->getFieldPrefix();
            
            // Only ignore the prefix if we are creating the base document
            // A translation would have a prefix of 0, which is valid
            if (empty($prefix) && $prefix !== '0') {
                $name = "dd_" . $this->properties['access']->id;
            } else {
                $name = $this->getFieldPrefix() . "_dd_" . $this->properties['access']->id;
            }
            $validprop = $access->checkInput($name . "_display");
            $displayaccess = $access->value;
            $isvalid = $isvalid && $validprop;
            $validprop = $access->checkInput($name . "_modify");
            $modifyaccess = $access->value;
            $isvalid = $isvalid && $validprop;
            $validprop = $access->checkInput($name . "_delete");
            $deleteaccess = $access->value;
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

    function createItem(Array $args = array())
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

        return parent::createItem($args);
    }

    function updateItem(Array $args = array())
    {
        // Save the access property
        $this->properties['access']->setInputStatus(DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY);
        
        // Ignore the position if this isn't the base document
        if (empty($this->properties['parent']->value)) {
            $this->properties['position']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISPLAYONLY);
        } else {
            $this->properties['position']->setDisplayStatus(DataPropertyMaster::DD_DISPLAYSTATE_DISABLED);
        }
        $this->fieldlist = array();
        
        return parent::updateItem($args);
    }
}
?>