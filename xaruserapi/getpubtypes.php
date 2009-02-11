<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 */
/**
 * get the name and description of all publication types
 *
 * @param $args['ptid'] int publication type ID (optional) OR
 * @param $args['name'] string publication type name (optional)
 * @return array(id => array('name' => name, 'description' => description)), or false on
 *         failure
 */
 
 sys::import('modules.dynamicdata.class.objects.master');
 
function publications_userapi_getpubtypes($args)
{
    $object = DataObjectMaster::getObjectList(array('name' => 'publications_types'));
    $items = $object->getItems();
    return $items;
}

?>