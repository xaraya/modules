<?php
sys::import('modules.dynamicdata.class.objects.master');
sys::import('modules.dynamicdata.class.objects.list');
class Forums extends DataObject
{
    // the forums get updated in many places,
    // but only updates from the modify GUI function should call hooks
    // ie in admin modify the function specifically calls
    // updateHooks(true), all other updates ignore hooks completely
    public $updatehooks = false;

    function __construct(DataObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->moduleid = xarMod::getRegID('crispbb');
        $itemtype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
            array('fid' => !isset($this->itemid) ? 0 : $this->itemid, 'component' => 'forum'));
        $this->itemtype = !empty($itemtype) ? $itemtype : 0;
        $this->tplmodule = 'crispbb';
    }

    // update a forum, we don't call parent here, otherwise nohooks will be ignored
    public function updateItem(Array $args = array())
    {
        if(count($args) > 0) {
            if(!empty($args['itemid']))
                $this->itemid = $args['itemid'];

            foreach($args as $name => $value)
                if(isset($this->properties[$name]))
                    $this->properties[$name]->setValue($value);
        }
        if(empty($this->itemid)) {
            // Try getting the id value from the item ID property if it exists
            foreach($this->properties as $property)
                if ($property->type == 21) $this->itemid = $property->value;
        }
        // add in forum specific topic and reply counts
        $counts = $this->getForumCounts();
        if (!empty($counts)) {
            foreach($counts as $name => $value)
                if(isset($this->properties[$name]))
                    $this->properties[$name]->setValue($value);
        }
        $args = $this->getFieldValues();
        $args['itemid'] = $this->itemid;
        foreach(array_keys($this->datastores) as $store)
        {
            // Execute any property-specific code first
            if ($store != '_dummy_') {
                foreach ($this->datastores[$store]->fields as $property) {
                    if (method_exists($property,'updatevalue')) {
                        $property->updateValue($this->itemid);
                    }
                }
            }

            // Now run the update routine of the this datastore
            $itemid = $this->datastores[$store]->updateItem($args);
        }

        if ($this->updatehooks == true)
            // call update hooks for this item
            $this->callHooks('update');

        return $this->itemid;
    }

    public function updateHooks($value=false)
    {
        $this->updatehooks = (bool)$value;
    }

    public function getForumCounts()
    {
        // @TODO: get these from crispbb_topics and crispbb_posts objects
        $counts = array();
        $counts['numtopics'] = xarMod::apiFunc('crispbb', 'user', 'counttopics',
            array('fid' => $this->itemid,'tstatus' => array(0,1)));
        $counts['numreplies'] = xarMod::apiFunc('crispbb', 'user', 'countposts',
            array('fid' => $this->itemid,'tstatus' => array(0,1),'pstatus' => 0));
        $counts['numreplies'] = !empty($counts['numreplies']) ? $counts['numreplies'] - $counts['numtopics'] : 0;
        $counts['numtopicsubs'] = xarMod::apiFunc('crispbb', 'user', 'counttopics',
            array('fid' => $this->itemid,'tstatus' => 2));
        $counts['numtopicdels'] = xarMod::apiFunc('crispbb', 'user', 'counttopics',
            array('fid' => $this->itemid,'tstatus' => 5));
        $counts['numreplysubs'] = xarMod::apiFunc('crispbb', 'user', 'countposts',
            array('fid' => $this->itemid,'tstatus' => array(0,1),'pstatus' => 2));
        $counts['numreplydels'] = xarMod::apiFunc('crispbb', 'user', 'countposts',
            array('fid' => $this->itemid,'tstatus' => array(0,1),'pstatus' => 5));
        return $counts;
    }
    public function createItem(Array $args = array())
    {
        // The id of the item^to be created is
        //  1. An itemid arg passed
        //  2. An id arg passed ot the primary index
        //  3. 0

        $this->properties[$this->primary]->setValue(0);
        if(count($args) > 0) {
            foreach($args as $name => $value) {
                if(isset($this->properties[$name])) {
                    $this->properties[$name]->setValue($value);
                }
            }
            if(isset($args['itemid'])) {
                $this->itemid = $args['itemid'];
            } else {
                $this->itemid = $this->properties[$this->primary]->getValue();
            }
        }
        // special case when we try to create a new object handled by dynamicdata
        if(
            $this->objectid == 1 &&
            $this->properties['module_id']->value == xarMod::getRegID('dynamicdata')
            //&& $this->properties['itemtype']->value < 2
        )
        {
            $this->properties['itemtype']->setValue($this->getNextItemtype($args));
        }

        // check that we have a valid item id, or that we can create one if it's set to 0
        if(empty($this->itemid)) {
            // no primary key identified for this object, so we're stuck
            if(!isset($this->primary)) {
                $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
                $vars = array('primary key', 'Forum DataObject', 'createItem', 'crispBB');
                throw new BadParameterException($vars,$msg);
            }
            $value = $this->properties[$this->primary]->getValue();

            // we already have an itemid value in the properties
            if(!empty($value)) {
                $this->itemid = $value;
            } elseif(!empty($this->properties[$this->primary]->datastore)) {
                // we'll let the primary datastore create an itemid for us
                $primarystore = $this->properties[$this->primary]->datastore;
                // add the primary to the data store fields if necessary
                if(!empty($this->fieldlist) && !in_array($this->primary,$this->fieldlist))
                    $this->datastores[$primarystore]->addField($this->properties[$this->primary]); // use reference to original property

                // Execute any property-specific code first
                foreach ($this->datastores[$primarystore]->fields as $property) {
                    if (method_exists($property,'createvalue')) {
                        $property->createValue($this->itemid);
                    }
                }

                $this->itemid = $this->datastores[$primarystore]->createItem($this->toArray());
            } else {
                $msg = 'Invalid #(1) for #(2) function #(3)() in module #(4)';
                $vars = array('primary key datastore', 'Forum DataObject', 'createItem', 'crispBB');
                throw new BadParameterException($vars,$msg);
            }
        }
        if(empty($this->itemid)) return;

        $args = $this->getFieldValues();
        $args['itemid'] = $this->itemid;
        foreach(array_keys($this->datastores) as $store) {
            // skip the primary store
            if(isset($primarystore) && $store == $primarystore)
                continue;

            // Execute any property-specific code first
            if ($store != '_dummy_') {
                foreach ($this->datastores[$store]->fields as $property) {
                    if (method_exists($property,'createvalue')) {
                        $property->createValue($this->itemid);
                    }
                }
            }

            // Now run the create routine of the this datastore
            $itemid = $this->datastores[$store]->createItem($args);
            if(empty($itemid))
                return;
        }
        // set the forum order, can't do this during create
        $extra = array('forder' => $this->itemid);
        // we just want to update the item here, create hooks haven't been called yet
        $this->updateHooks(false);
        $this->updateItem($extra);
        // having created the forum, we now create itemtypes for it
        // @TODO: use the crispbb_itemtypes object here
        $components = xarMod::apiFunc('crispbb', 'user', 'getoptions', array('options' => 'components'));
        $itemtypes = array();
        foreach ($components as $component => $label ) {
            $itemtypes[$component] = xarMod::apiFunc('crispbb', 'admin', 'createitemtype', array('fid' => $this->itemid, 'component' => $component));
        }
        // set the correct itemtype (for create hooks)
        $this->itemtype = $itemtypes['forum'];
        // sync hooks
        xarMod::apiFunc('crispbb', 'user', 'getitemtypes');

        // call create hooks for this item
        $this->callHooks('create');

        // let the tracker know this forum was created
        $fstring = xarModVars::get('crispbb', 'ftracking');
        $ftracking = (!empty($fstring)) ? unserialize($fstring) : array();
        $ftracking[$this->itemid] = time();
        xarModVars::set('crispbb', 'ftracking', serialize($ftracking));

        return $this->itemid;
    }
/*  // join on categories, this module, this forum id,
    // adds all categories_linkage fields as properties of the object
    // @TODO: make it just add the category_id as a property
    public function joinCategories(Array $cids=array()) {
        $categoriesdef = xarMod::apiFunc(
            'categories','user','leftjoin',
            array(
                'modid' => $this->moduleid,
                'cids' => $cids
            )
        );
        $cattable = array(
            'table' => $categoriesdef['table'],
            'key' => $categoriesdef['field'],
            'fields' => array(),
            'where' => $categoriesdef['where'],
            'andor' => 'and',
            );
        $this->joinTable($cattable);
    }
    */
}
class ForumsList extends DataObjectList
{
    function __construct(DataObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->moduleid = xarMod::getRegID('crispbb');
        $this->tplmodule = 'crispbb';
    }
}
?>