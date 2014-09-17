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
    public $parent_id = 0;

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
        if (!isset($args['itemid'])) throw new EmptyParameterException('itemid');
        xarMod::apiLoad('eav');
        $tables = xarDB::getTables();
        $q = new Query('SELECT', $tables['eav_data']);
        $q->eq('item_id', $args['itemid']);
        $q->eq('object_id', $this->parent_id);
        if (!$q->run()) return false;
        $result = $q->output();
        foreach ($this->properties as $key => $attribute) {
            foreach ($result as $k => $row) {
                if ($attribute->id == $row['attribute_id']) {
                    $this->properties[$key]->value = $row['value_' . $this->properties[$key]->basetype];
                    unset($result[$k]);
                }
            }
        }
        return true;
    }
    
    public function getItems_inert(Array $args = array())
    {
        if (empty($args['itemids'])) return array();
        xarMod::apiLoad('eav');
        $tables = xarDB::getTables();
        $q = new Query('SELECT', $tables['eav_data']);
        $q->in('item_id', $args['itemids']);
        $q->eq('object_id', $this->parent_id);
        if (!$q->run()) return false;
        foreach ($q->output() as $row) {
            foreach ($this->properties as $key => $attribute) {
                if ((int)$attribute->id == (int)$row['attribute_id']) {
                    $row['value'] = $row['value_' . $this->properties[$key]->basetype];
                    unset($row['value_tinyint']);
                    unset($row['value_integer']);
                    unset($row['value_decimal']);
                    unset($row['value_string']);
                    unset($row['value_text']);
                }
            }
            $this->items[$row['item_id']][$row['attribute_id']] = $row;
        }
        return $this->items;
    }
    
    public function createItem(Array $args = array())
    {
        if (!isset($args['itemid'])) throw new MissingParameterException('itemid');

        /* 
         * This is analogous to the createItem method in dynamicdata/class/objects
         */
        foreach ($this->getFieldList() as $fieldname) {
            if (!empty($this->properties[$fieldname]->source) &&
                method_exists($this->properties[$fieldname],'createvalue')) {
                $this->properties[$fieldname]->createValue($this->itemid);
            }
        }

        xarMod::apiLoad('eav');
        $tables = xarDB::getTables();
        $q = new Query('INSERT', $tables['eav_data']);
        foreach ($this->properties as $property) {
            $q->addfield('object_id', $this->parent_id);
            $q->addfield('item_id', $args['itemid']);
            $valuefield = 'value_' . $property->basetype;
            $q->addfield($valuefield, $property->value);
            $q->addfield('attribute_id', (int)$property->id);
            if (!$q->run()) return false;
            $q->clearfields();
        }
        
        foreach ($this->getFieldList() as $fieldname) {
            if (empty($this->properties[$fieldname]->source) &&
                method_exists($this->properties[$fieldname],'createvalue')) {
                $this->properties[$fieldname]->createValue($this->itemid);
            }
        }

        return true;
    }
    
    public function updateItem(Array $args = array())
    {
        if (!isset($args['itemid'])) throw new MissingParameterException('itemid');

        /* 
         * This is analogous to the updateItem method in dynamicdata/class/objects
         */
        foreach ($this->getFieldList() as $fieldname) {
            if (!empty($this->properties[$fieldname]->source) &&
                method_exists($this->properties[$fieldname],'updatevalue')) {
                $this->properties[$fieldname]->updateValue($this->itemid);
            }
        }

        xarMod::apiLoad('eav');
        $tables = xarDB::getTables();
        foreach ($this->properties as $property) {
            $q = new Query('UPDATE', $tables['eav_data']);
            $valuefield = 'value_' . $property->basetype;
            $q->addfield($valuefield, $property->value);
            $q->eq('object_id',    (int)$this->parent_id);
            $q->eq('item_id',      (int)$args['itemid']);
            $q->eq('attribute_id', (int)$property->id);
            if (!$q->run()) return false;
            if (!$q->affected()) {
                // Either the value didn't change, or the record was not found
                // CHECKME: is there a better way of doing this?
                $q = new Query('SELECT', $tables['eav_data']);
                $q->eq('object_id',    (int)$this->parent_id);
                $q->eq('item_id',      (int)$args['itemid']);
                $q->eq('attribute_id', (int)$property->id);
                if (!$q->run()) return false;
                $result = $q->row();
                if (empty($result) {
                    $q = new Query('INSERT', $tables['eav_data']);
                    $q->addfield('object_id',    (int)$this->parent_id);
                    $q->addfield('item_id',      (int)$args['itemid']);
                    $q->addfield('attribute_id', (int)$property->id);
                    $valuefield = 'value_' . $property->basetype;
                    $q->addfield($valuefield, $property->value);
                    if (!$q->run()) return false;
                }
            }
            $q->clearfields();
            $q->clearconditions();
        }

        foreach ($this->getFieldList() as $fieldname) {
            if (empty($this->properties[$fieldname]->source) &&
                method_exists($this->properties[$fieldname],'updatevalue')) {
                $this->properties[$fieldname]->updateValue($this->itemid);
            }
        }

        return true;
    }
    
    public function deleteItem(Array $args = array())
    {
        if (!isset($args['itemid'])) throw new MissingParameterException('itemid');
        xarMod::apiLoad('eav');
        $tables = xarDB::getTables();
        $q = new Query('DELETE', $tables['eav_data']);
        $q->eq('object_id', $this->parent_id);
        $q->eq('item_id', $args['itemid']);
        if (!$q->run()) return false;
        return true;
    }
}
?>
