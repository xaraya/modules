<?php

/**
 * Payments Module
 *
 * @package modules
 * @subpackage payments
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2016 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */

/**
 * Class that 
 */
 
sys::import('modules.dynamicdata.class.objects.base');

class ISO20022 extends DataObject
{
    public function checkInput(Array $args = array(), $suppress=0, $priority='dd')
    {
        // Run checkInput of the parent: get and check the values
        $isvalid = parent::checkInput($args,$suppress,$priority);

        // Sanitize some property values
        $string = $this->properties['address_1']->value;
        $this->properties['address_1']->value = xarMod::apiFunc('payments', 'admin', 'sanitize_swift', array('string' => $string));
        
        return $isvalid;
    }
}

?>