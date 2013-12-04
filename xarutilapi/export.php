<?php
/**
 * @package modules
 * @subpackage dynamicdata module
 * @category Xaraya Web Applications Framework
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 * @link http://xaraya.com/index.php/release/182.html
 *
 * @author mikespub <mikespub@xaraya.com>
 * @todo move the xml generate code into a template based system.
 */
/**
 * Export an object definition or an object item to XML
 *
 * @author mikespub <mikespub@xaraya.com>
 */
function eav_utilapi_export(Array $args=array())
{
    $myobject = DataObjectMaster::getObject(array('name' => 'objects'));
    extract($args);
    if (isset($args['objectref'])) {
        $myobject->getItem(array('itemid' => $args['objectref']->objectid));

    } else {

        if (empty($objectid)) {
            $objectid = null;
        }
        if (empty($module_id)) {
            $module_id = xarMod::getRegID('eav');
        }
        if (empty($itemtype)) {
            $itemtype = 0;
        }
        if (empty($itemid)) {
            $itemid = null;
        }

        $myobject->getItem(array('itemid' => $itemid));
    }

    if (!isset($myobject) || empty($myobject->label)) {
        return;
    }

    // get the list of properties for a Dynamic Object
    $object_properties = DataPropertyMaster::getProperties(array('objectid' => 1));
       
	$property_properties = xarMod::apiFunc('eav','user','getattributes', array('object_id' => $objectid));
	
	$proptypes = DataPropertyMaster::getPropertyTypes();

    $prefix = xarDB::getPrefix();
    $prefix .= '_';

    $xml = '';

    $xml .= '<object name="'.$myobject->properties['name']->value.'">'."\n";
    foreach (array_keys($object_properties) as $name) {
        if ($name != 'name' && isset($myobject->properties[$name]->value)) {
            if (is_array($myobject->properties[$name]->value)) {
                $xml .= "  <$name>\n";
                foreach ($myobject->$name as $field => $value) {
                    $xml .= "    <$field>" . xarVarPrepForDisplay($value) . "</$field>\n";
                }
                $xml .= "  </$name>\n";
            } elseif ($name == 'config') {
                // don't replace anything in the serialized value
                $value = $myobject->properties[$name]->value;
                $xml .= "  <$name>" . $value . "</$name>\n";
            } else {
                $value = $myobject->properties[$name]->value;
                $xml .= "  <$name>" . xarVarPrepForDisplay($value) . "</$name>\n";
            }
        }
    }

    $xml .= "  <properties>\n";
    $properties = DataPropertyMaster::getProperties(array('objectid' => $myobject->properties['objectid']->value));
    foreach ($property_properties as $key => $value) {
        $xml .= '    <property name="'.$value['name'].'">' . "\n";
        foreach ($value as $subkey => $subvalue) {
        	$xml .= "      <$subkey>".$subvalue."</$subkey>\n";
        }
        $xml .= "    </property>\n";
    }
    $xml .= "  </properties>\n";

    $xml .= "</object>\n";
    return $xml;
}

?>
