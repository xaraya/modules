<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/1015.html
 * @author potion <potion@xaraya.com>
 */ 
 /** 
 * @param $args['file'] location of the .xml file containing the object definition, or
 * @param $args['xml'] XML string containing the object definition
 * @param $args['keepitemid'] (try to) keep the item id of the different items (default false)
 * @param $args['entry'] optional array of external references.
 * @param $args['overwrite']
 */

function content_utilapi_import($args) {

    $data = $args;

    extract($args);

    // for now, prohibit importation of items
    if (!empty($file)) {
        $xmlobject = simplexml_load_file($file);
        xarLogMessage('DD: import file ' . $file); 
    } elseif (!empty($xml)) { 
        $xml = preg_replace('/>[^<]+$/s','>', $xml);
        $xmlobject = new SimpleXMLElement($xml);
    } 
    $dom = dom_import_simplexml ($xmlobject);
    $roottag = $dom->tagName;
    if ($roottag == 'items') die("We don't want to import items here");
  
    $objectid = xarMod::apiFunc('dynamicdata','util','import',$data);
  
    if (empty($objectid)) return false;

    sys::import('modules.dynamicdata.class.objects.master');

    $object = DataObjectMaster::getObject(array('name' => 'objects'));

    $object->getItem(array('itemid' => $objectid));
    $name = $object->properties['name']->value;
    $label = $object->properties['label']->value;

    $ctobject = DataObjectMaster::getObject(array('name' => 'content_types'));
    $ctobject->properties['label']->setValue($label);
    $ctobject->properties['content_type']->setValue($name);
    $ctobject->properties['model']->setValue('imported');
    $itemid = $ctobject->createItem(array('itemid' => $objectid));

    return array('objectid' => $objectid, 'name' => $name);    
    
}
?>