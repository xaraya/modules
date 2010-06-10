<?php
/**
 * Publications module
 *
 */
/**
 * Show some predefined form field in a template
 *
 * @param $args array containing the definition of the field (object, itemid, property, value, ...)
 * @return string containing the HTML (or other) text to output in the BL template
 */
function publications_userapi_fieldoutput($args)
{
    extract($args);
    if (!isset($object) || !isset($itemid) || !isset($field)) return '';
    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(array('name' => $object));
    $itemid = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $itemid));
    $object->getItem(array('itemid' => $itemid));
    $field = $object->properties[$field]->getValue();
    return $field;
}

?>