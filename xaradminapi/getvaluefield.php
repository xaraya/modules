<?php
/**
 * EAV Module
 *
 * @package modules
 * @subpackage eav
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2013 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Handle get the value field of an attribute
 *
 */

    function eav_adminapi_getvaluefield($args)
    {
        extract($args);
        if (!isset($property) && !isset($property_id)) throw new Exception(xarML('Missing property or property_id for eav_adminapi_getvaluefield'));
        if (isset($property_id)) {
            sys::import('modules.dynamicdata.class.properties.master');
            $property = DataPropertyMaster::getProperty(array('type' => $property_id));
        }
        $type = $property->basetype;

        switch ($type) {
            case 'string': $field = 'default_string'; break;
            case 'text'  : $field = 'default_text'; break;
            case 'decimal': $field = 'default_decimal'; break;
            case 'integer': $field = 'default_integer'; break;
            case 'number': $field = 'default_tinyint'; break;
        }
        
        return $field;
    }
?>