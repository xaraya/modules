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
 * create a new attribute for an object
 */
function eav_adminapi_create_attribute(array $args=array())
{
    extract($args);

    // Required arguments
    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($type) || !is_numeric($type)) {
        $invalid[] = 'type';
    }

    if (count($invalid) > 0) {
        $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
        $vars = array(join(', ', $invalid), 'admin', 'createproperty', 'DynamicData');
        throw new BadParameterException($vars, $msg);
    }

    $itemid = 0;

    // get the properties of the 'properties' object
    $fields = xarMod::apiFunc(
        'dynamicdata',
        'user',
        'getprop',
        array('objectid' => 2)
    ); // the properties

    $values = array();
    // the acceptable arguments correspond to the property names !
    foreach ($fields as $name => $field) {
        if (isset($args[$name])) {
            $values[$name] = $args[$name];
        }
    }

    sys::import('modules.dynamicdata.class.objects.master');
    $propertyobject = DataObjectMaster::getObject(array('name' => 'properties'));
    $propid = $propertyobject->createItem($values);
    return $propid;
}
