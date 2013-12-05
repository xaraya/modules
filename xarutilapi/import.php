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
function eav_utilapi_import(Array $args=array())
{
    extract($args);

    if (!isset($prefix)) $prefix = xarDB::getPrefix();
    $prefix .= '_';
    if (!isset($overwrite)) $overwrite = false;

    if (empty($xml) && empty($file)) {
        throw new EmptyParameterException('xml or file');
    } elseif (!empty($file) && (!file_exists($file) || !preg_match('/\.xml$/',$file)) ) {
        // check if we tried to load a file using an old path
        if (xarConfigVars::get(null, 'Site.Core.LoadLegacy') == true && strpos($file, 'modules/') === 0) {
            $file = sys::code() . $file;
            if (!file_exists($file)) {
                throw new BadParameterException($file,'Invalid importfile "#(1)"');
            }
        } else {
            throw new BadParameterException($file,'Invalid importfile "#(1)"');
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
        xarLogMessage('DD: import file ' . $file);
        
    } elseif (!empty($xml)) {
        // remove garbage from the end
        $xml = preg_replace('/>[^<]+$/s','>', $xml);
        $xmlobject = new SimpleXMLElement($xml);
    }
    // No better way of doing this?
    $dom = dom_import_simplexml ($xmlobject);
    $roottag = $dom->tagName;

    sys::import('xaraya.validations');
    $boolean = ValueValidations::get('bool');
    $integer = ValueValidations::get('int');
    
    if ($roottag == 'object') {
        //FIXME: this unconditionally CLEARS the incoming parameter!!
        $args = array();
        // Get the object's name
        $args['name'] = (string)($xmlobject->attributes()->name);
        $args['id'] = $value = (string)$xmlobject->{'id'}[0];
        xarLogMessage('DD: importing ' . $args['name']);

        // check if the object exists
        $data['object'] = DataObjectMaster::getObjectList(array('name' => 'eav_entities'));
        $data['object']->getItems();
		$dupexists = array_key_exists($args['id'], $data['object']->getItems());
 		if ($dupexists && !$overwrite) {
            $msg = 'Duplicate definition for #(1) #(2)';
            $vars = array('object',xarVarPrepForDisplay($args['name']));
            throw new DuplicateException(null,$args['name']);
        }
        
        $data['object'] = DataObjectMaster::getObject(array('name' => 'eav_entities'));
        $objectproperties = array_keys($data['object']->properties);
        foreach($objectproperties as $property) {
            if (isset($xmlobject->{$property}[0])) {
                $value = (string)$xmlobject->{$property}[0];
                $object_id = (string)$xmlobject->{'object'}[0];
                try {
                    $boolean->validate($value, array());
                } catch (Exception $e) {
                    try {
                        $integer->validate($value, array());
                    } catch (Exception $e) {}
                }
                $args[$property] = $value;
            }
        }
        if($dupexists) {
        	$id = $data['object']->updateItem($args);
        } else {
        	$id = $data['object']->createItem($args);
        }
    }
	if ($id) {
		return $object_id;
	}
}
?>