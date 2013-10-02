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
 * Handle getconfig hook calls
 *
 */

    function eav_adminapi_register_entity($args)
    {
        extract($args);
        if (!isset($object) || !isset($module)) throw new Exception(xarML('Missing object or module for eav_adminapi_register_entity'));
        
        $module_id = xarMod::getRegID($module);
        $object = DataObjectMaster::getObject(array('name' => $object));
        $object_id = $object->objectid;
        
        $tables = xarDB::getTables();
        sys::import('xaraya.structures.query');
        $q = new Query('SELECT', $tables['entities']);
        $q->eq('object_id', $object_id);
        $q->eq('module_id', $module_id);
        $q->run();
        if (!empty($q->row())) return true;
        
        sys::import('modules.dynamicdata.class.objects.master');
        $entity = DataObjectMaster::getObject(array('name' => 'eav_entities'));
        $entity->properties['object']->value = $object_id;
        $entity->properties['module']->value = $module_id;
        $itemid = $entity->createItem();
        return true;
    }
?>