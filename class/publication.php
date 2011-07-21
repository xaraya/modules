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
    public function checkInput(Array $args = array(), $suppress=0, $priority='dd')
    {
        $isvalid = parent::checkInput($args,$suppress,$priority);

        if ($isvalid) {
            $access = DataPropertyMaster::getProperty(array('name' => 'access'));
            $prefix = $this->getFieldPrefix();
            
            // Only ignore the prefix if we are creating the base document
            // A translation would have a prefix of 0, which is valid
            if (empty($prefix)) {
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
                'modify' => $modifyaccess,
                'delete' => $deleteaccess,
            );
            $this->properties['access']->setValue($allaccess);
       }
        return $isvalid;
    }

    function createItem(Array $args = array())
    {
        $this->properties['access']->setInputStatus(DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY);
        return parent::createItem($args);
    }

    function updateItem(Array $args = array())
    {
        $this->properties['access']->setInputStatus(DataPropertyMaster::DD_INPUTSTATE_ADDMODIFY);
        return parent::updateItem($args);
    }
}
?>