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
    public $fsettings = array(); // settings for this forum
    public $fprivileges = array(); // all permissions for this forum
    public $itemlinks = array(); // itemlinks based on permissions
    public $userLevel = 0; // maximum level for current user
    public $userAction = 'viewforum'; // minimum requirement

    function __construct(DataObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->moduleid = xarMod::getRegID('crispbb');
        $itemtype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
            array('fid' => !isset($this->itemid) ? 0 : $this->itemid, 'component' => 'forum'));
        $this->itemtype = !empty($itemtype) ? $itemtype : 0;
        $this->tplmodule = 'crispbb';
    }

    /**
     * Retrieve the values for this item
    **/
    public function getItem(Array $args = array())
    {
        $itemid = parent::getItem($args);
        if (empty($itemid)) return;
        $fsettings = unserialize($this->properties['fsettings']->value);
        $this->fsettings = $fsettings;
        $fprivileges = unserialize($this->properties['fprivileges']->value);
        $this->fprivileges = $fprivileges;
        $check = $this->getFieldValues();
        $check['fid'] = $this->itemid;
        $check['fprivileges'] = $this->fprivileges;
        $this->userLevel = xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => $this->userAction));
        return $this->itemid;
    }
    /**
     * Delete this forum (and its topics and posts)
    **/
    public function deleteItem(Array $args = array())
    {
        if(!empty($args['itemid']))
            $this->itemid = $args['itemid'];

        if(empty($this->itemid))
        {
            $msg = 'Invalid item id in method #(1)() for dynamic object [#(2)] #(3)';
            $vars = array('deleteItem',$this->objectid,$this->name);
            throw new BadParameterException($vars, $msg);
        }

        // @TODO: replace these api calls with objectlists
        $topics = xarMod::apiFunc('crispbb', 'user', 'gettopics', array('fid' => $this->itemid));
        $tids = !empty($topics) ? array_keys($topics) : array();
        $posts = xarMod::apiFunc('crispbb', 'user', 'getposts', array('fid' => $this->itemid));
        $pids = !empty($posts) ? array_keys($posts) : array();
        $dbconn =& xarDB::getConn();
        $xartable =& xarDB::getTables();
        $topicstable = $xartable['crispbb_topics'];
        $poststable = $xartable['crispbb_posts'];
        $itemtypestable = $xartable['crispbb_itemtypes'];
        $hookstable = $xartable['crispbb_hooks'];
        try {
            $dbconn->begin();
            // remove posts
            if (!empty($pids)) {
                $query = "DELETE FROM $poststable WHERE id IN (" . join(',', $pids) . ")";
                $result = &$dbconn->Execute($query,array());
            }
            // remove topics
            if (!empty($tids)) {
                // first from topics table
                $query = "DELETE FROM $topicstable WHERE id IN (" . join(',', $tids) . ")";
                $result = &$dbconn->Execute($query,array());
                // then from hooks table
                $query = "DELETE FROM $hookstable WHERE tid IN (" . join(',', $tids) . ")";
                $result = &$dbconn->Execute($query,array());
            }
            // remove forum itemtype
            $query = "DELETE FROM $itemtypestable WHERE fid = ? AND component = 'Forum'";
            $result = &$dbconn->Execute($query,array($this->itemid));
            // We're done, commit
            $dbconn->commit();
        } catch (Exception $e) {
            $dbconn->rollback();
            throw $e;
        }
        // remove forum from ftracker
        $string = xarModVars::get('crispbb', 'ftracker');
        $ftracker = (!empty($string) && is_string($string)) ? unserialize($string) : array();
        if (isset($ftracker[$this->itemid])) unset($ftracker[$this->itemid]);
        xarModVars::set('crispbb', 'ftracker', serialize($ftracker));
        // and finally, remove the forum itself :)
        $itemid = parent::deleteItem($args);
        if(empty($itemid)) return;

        return $this->itemid;
    }
    /**
     * populate itemlinks for this forum
    **/
    public function getItemLinks(Array $args = array())
    {
        extract($args);
        $itemlinks = array();
        if (empty($this->userLevel)) return;
        $check = $this->getFieldValues();
        $check['fid'] = $this->itemid;
        $check['fprivileges'] = $this->fprivileges;
        $privs = $this->fprivileges[$this->userLevel];
        // deleteforum permissions
        if (!empty($privs['deleteforum'])) {
            $itemlinks['delete'] = xarModURL('crispbb', 'admin', 'delete', array('fid' => $this->itemid));
        }
        if (!empty($privs['editforum'])) {
            $itemlinks['modify'] = xarModURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid, 'sublink' => 'edit'));
            $itemlinks['overview'] = xarModURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid));
        }
        // forum viewers
        if ($check['ftype'] != 1) {
            $itemlinks['view'] = xarModURL('crispbb', 'user', 'view', array('fid' => $this->itemid));
            // forum readers
            if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel', array('check' => $check, 'priv' => 'readforum'))) {
                if (!empty($check['lasttid'])) {
                    //@TODO:
                }
                // Logged in users
                if (xarUserIsLoggedIn()) {
                    $itemlinks['read'] = xarModURL('crispbb', 'user', 'view',
                        array('fid' => $this->itemid, 'action' => 'read'));
                    // forum posters
                    if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                        array('check' => $check, 'priv' => 'newtopic'))) {
                        $itemlinks['newtopic'] = xarModURL('crispbb', 'user', 'newtopic',
                            array('fid' => $this->itemid));
                    }
                    // forum moderators
                    if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                        array('check' => $check, 'priv' => 'ismoderator'))) {
                        $itemlinks['moderate'] = xarModURL('crispbb', 'user', 'moderate',
                            array('component' => 'topics', 'fid' => $this->itemid));
                    }
                    if (!empty($privs['editforum'])) {
                        $itemlinks['forumhooks'] = xarModURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid, 'sublink' => 'forumhooks'));
                        $itemlinks['topichooks'] = xarModURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid, 'sublink' => 'topichooks'));
                        $itemlinks['posthooks'] = xarModURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid, 'sublink' => 'posthooks'));
                        $itemlinks['privileges'] = xarModURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid, 'sublink' => 'privileges'));
                    }
                }
            }
        } else {
            $redirecturl = !empty($this->fsettings['redirecturl']) ? $this->fsettings['redirecturl'] : '';
            if (!empty($redirecturl)) {
                $itemlinks['view'] = $redirecturl;
            }
        }

        $this->itemlinks = $itemlinks;
    }

    // update a forum, we don't call parent here, otherwise nohooks will be ignored
    public function updateItem(Array $args = array())
    {
        // updating anything other than forum counts requires elevated privs
        // @TODO: wrap this in a sec check
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
        // @TODO: sec check
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
                'itemtype' => $this->itemtype,
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