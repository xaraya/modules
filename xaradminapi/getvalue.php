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
 * Handle get the value of an attributeå
 *
 */

    function eav_adminapi_getvalue($args)
    {
        extract($args);
        if (!isset($property) && !isset($type)) throw new Exception(xarML('Missing property or type for eav_adminapi_getvalue'));
        if (!isset($values)) throw new Exception(xarML('Missing values for eav_adminapi_getvalue'));
        
        if (isset($property)) $type = $property->basetype;
        
        switch ($type) {
            case 'string': $value = $values['default_string']; break;
            case 'text': $value = $values['default_text']; break;
            case 'decimal': $value = $values['default_decimal']; break;
            case 'integer': $value = $values['default_integer']; break;
            case 'number': $value = $values['default_tinyint']; break;
        }
        
        return $value;
    }
?>