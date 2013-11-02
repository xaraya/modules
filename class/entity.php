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

sys::import('modules.dynamicdata.class.objects.base');

class Entity extends DataObject
{
    public $parent_id;

    public function loader(DataObjectDescriptor $descriptor)
    {
        parent::loader($descriptor);
        $propertyargs = xarMod::apiFunc('eav', 'user', 'getattributes', array('object_id' => $this->parent_id));
        foreach ($propertyargs as $row) {
            $row['propertyprefix'] = 'eav_';
            DataPropertyMaster::addProperty($row, $this);
        }
    }

    public function getItem(Array $args = array())
    {
        $itemid = parent::getItem($args);
    }
    
    public function createItem(Array $args = array())
    {
        if (!isset($args['itemid'])) throw new MissingParameterException('itemid');
        xarMod::apiLoad('eav');
        $tables = xarDB::getTables();
        $q = new Query('INSERT', $tables['eav_data']);
        foreach ($this->properties as $property) {
            $q->addfield('item_id', $args['itemid']);
            $valuefield = 'value_' . $property->basetype;
            $q->addfield($valuefield, $property->value);
            $q->addfield('attribute_id', $property->id);
            if (!$q->run()) return false;
            $q->clearfields();
        }
        return true;
    }
    
    public function updateItem(Array $args = array())
    {
        if (!isset($args['itemid'])) throw new MissingParameterException('itemid');
        xarMod::apiLoad('eav');
        $tables = xarDB::getTables();
        foreach ($this->properties as $property) {
            $q = new Query('UPDATE', $tables['eav_data']);
            $q->addfield('item_id', $args['itemid']);
            $valuefield = 'value_' . $property->basetype;
            $q->addfield($valuefield, $property->value);
            $q->addfield('attribute_id', $property->id);
            if (!$q->run()) {
                $q = new Query('INSERT', $tables['eav_data']);
                $q->addfield('item_id', $args['itemid']);
                $valuefield = 'value_' . $property->basetype;
                $q->addfield($valuefield, $property->value);
                $q->addfield('attribute_id', $property->id);
                if (!$q->run()) return false;
            }
        }
        return true;
    }
    
}
?>
