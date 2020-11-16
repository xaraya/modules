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
 */
/**
 * Import an object definition or an object item from XML
 *
 * @param $args['file'] location of the .xml file containing the object definition, or
 * @param $args['xml'] XML string containing the object definition
 * @param $args['keepitemid'] (try to) keep the item id of the different items (default false)
 * @param $args['entry'] optional array of external references.
 * @return array object id on success, null on failure
 */
sys::import('modules.dynamicdata.class.objects.master');
function eav_utilapi_import(array $args=array())
{
    extract($args);

    if (!isset($prefix)) {
        $prefix = xarDB::getPrefix();
    }
    $prefix .= '_';
    if (!isset($overwrite)) {
        $overwrite = false;
    }

    if (empty($xml) && empty($file)) {
        throw new EmptyParameterException('xml or file');
    } elseif (!empty($file) && (!file_exists($file) || !preg_match('/\.xml$/', $file))) {
        // check if we tried to load a file using an old path
        if (xarConfigVars::get(null, 'Site.Core.LoadLegacy') == true && strpos($file, 'modules/') === 0) {
            $file = sys::code() . $file;
            if (!file_exists($file)) {
                throw new BadParameterException($file, 'Invalid importfile "#(1)"');
            }
        } else {
            throw new BadParameterException($file, 'Invalid importfile "#(1)"');
        }
    }

    $objectcache = array();
    $objectmaxid = array();

    $proptypes = DataPropertyMaster::getPropertyTypes();
    $name2id = array();
    foreach ($proptypes as $propid => $proptype) {
        $name2id[$proptype['name']] = $propid;
    }

    if (!empty($file)) {
        $xmlobject = simplexml_load_file($file);
        xarLog::message('DD: import file ' . $file);
    } elseif (!empty($xml)) {
        // remove garbage from the end
        $xml = preg_replace('/>[^<]+$/s', '>', $xml);
        $xmlobject = new SimpleXMLElement($xml);
    }
    // No better way of doing this?
    $dom = dom_import_simplexml($xmlobject);
    $roottag = $dom->tagName;

    sys::import('xaraya.validations');
    $boolean = ValueValidations::get('bool');
    $integer = ValueValidations::get('int');
    
    if ($roottag == 'object') {
        //FIXME: this unconditionally CLEARS the incoming parameter!!
        $args = array();
        // Get the object's name
        $args['name'] = (string)($xmlobject->attributes()->name);
        $args['objectid'] = $value = (string)$xmlobject->{'object'}[0];
        xarLog::message('DD: importing ' . $args['name']);

        // check if the object exists
        $data['object'] = DataObjectMaster::getObjectList(array('name' => 'eav_entities'));
        $info = $data['object']->getObjectInfo($args);
        $dupexists = false;
        foreach ($data['object']->getItems() as $items) {
            if (in_array($info['objectid'], $items)) {
                $dupexists = true;
                break;
            }
        }
        if ($dupexists && !$overwrite) {
            $msg = 'Duplicate definition for #(1) #(2)';
            $vars = array('object',xarVar::prepForDisplay($args['name']));
            throw new DuplicateException(null, $args['name']);
        }
        //Add entities after import
        $data['object'] = DataObjectMaster::getObject(array('name' => 'eav_entities'));
        $objectproperties = array_keys($data['object']->properties);
        foreach ($objectproperties as $property) {
            if (isset($xmlobject->{$property}[0])) {
                $value = (string)$xmlobject->{$property}[0];
                $objectname = (string)$xmlobject->{'object'}[0];
                $info = $data['object']->getObjectInfo(array('name' => $objectname));
                try {
                    if ($property == "object") {
                        $integer->validate($info['objectid'], array());
                        $value = $info['objectid'];
                    } else {
                        $integer->validate($value, array());
                    }
                } catch (Exception $e) {
                    try {
                        $integer->validate($value, array());
                    } catch (Exception $e) {
                    }
                }
                $args[$property] = $value;
            }
        }

        if ($dupexists) {
            $id = $data['object']->updateItem($args);
        } else {
            //unset($args['id']);
            $id = $data['object']->createItem($args);
        }
        
        //Add attributes after import
        $dataproperty = DataObjectMaster::getObject(array('name' => 'eav_attributes_def'));
        $propertyproperties = array_keys($dataproperty->properties);
        $propertieshead = $xmlobject->properties;
        
        foreach ($propertieshead->children() as $property) {
            $propertyargs = array();
            $propertyname = (string)($property->attributes()->name);
            $propertyargs['name'] = $propertyname;
          
            foreach ($propertyproperties as $prop) {
                if (isset($property->{$prop}[0])) {
                    $value = (string)$property->{$prop}[0];
                    try {
                        $boolean->validate($value, array());
                    } catch (Exception $e) {
                        try {
                            $integer->validate($value, array());
                        } catch (Exception $e) {
                        }
                    }
                    $propertyargs[$prop] = $value;
                }
            }
            
            // Backwards Compatibility with old definitions
            if (!isset($propertyargs['configuration']) && isset($property->{'validation'}[0])) {
                $propertyargs['configuration'] = (string)$property->{'validation'}[0];
            }

            // Add some args needed to define the property
            unset($propertyargs['id']);

            // Now do some checking
            if (empty($propertyargs['name'])) {
                throw new BadParameterException(null, 'Missing keys in property definition');
            }

            // Force a new itemid to be created for this property
            $dataproperty->properties[$dataproperty->primary]->setValue(0);
            // Create the property
            //TODO - Need to check for Update attribute when Override exist selected
            if (!$dupexists) {
                $id = $dataproperty->createItem($propertyargs);
            
                // Code to import attribute defination xar_eav_attributes
                sys::import('xaraya.structures.query');
                $tables =& xarDB::getTables();
                foreach ($propertieshead->property as $property) {
                    $q = new Query('INSERT', $tables['eav_attributes']);
                    $q->addfield('object_id', $info['objectid']);
                    $q->addfield('module_id', (string)$property->{'module_id'}[0]);
                    $q->addfield('name', (string)$property->{'name'}[0]);
                    $q->addfield('label', (string)$property->{'label'}[0]);
                    $q->addfield('type', (string)$property->{'type'}[0]);
                    $q->addfield('configuration', (string)$property->{'configuration'}[0]);
                    $q->addfield('timecreated', (string)$property->{'timecreated'}[0]);
                    $q->addfield('timeupdated', (string)$property->{'timeupdated'}[0]);
                    $q->addfield('seq', (string)$property->{'seq'}[0]);
                    $q->addfield('status', (string)$property->{'status'}[0]);
    
                    if (!$q->run()) {
                        return;
                    }
                }
            }
        }
    }
    if ($id) {
        return $objectname;
    }
}
