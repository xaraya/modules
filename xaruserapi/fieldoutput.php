<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
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
    if (!isset($object) || !isset($itemid) || !isset($field)) {
        return '';
    }
    sys::import('modules.dynamicdata.class.objects.master');
    $object = DataObjectMaster::getObject(['name' => $object]);
    $itemid = xarMod::apiFunc('publications', 'user', 'gettranslationid', ['id' => $itemid]);
    $object->getItem(['itemid' => $itemid]);
    $field = $object->properties[$field]->getValue();
    return $field;
}
