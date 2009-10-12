<?php
sys::import('modules.dynamicdata.class.objects');
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
        return parent::deleteItem($args);
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
        $this->itemtype = null; // don't set an itemtype when getting forum lists
        $this->tplmodule = 'crispbb';
    }

    /**
      * Get List to fill showView template options
      *
      * @return array
      *
      * @todo make this smarter
      */
    public function getViewOptions(Array $args = array())
    {
        // insist on using fid in urlargs (for now)
        $args['param'] = 'fid';
        $urlargs = array();
        $urlargs[$args['param']] = $args['itemid'];
        $check = $this->items[$args['itemid']];
        $check['fid'] = $args['itemid'];
        $check['fprivileges'] = unserialize($this->items[$args['itemid']]['fprivileges']);
        $userLevel = xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'viewforum'));
        $numforums = count($this->items);
        $fids = !empty($numforums) ? array_keys($this->items) : array();
        $currentindex = 0;
        foreach ($fids as $i => $fid) {
            if ($fid == $args['itemid']) {
                $currentindex = $i;
                break;
            }
        }
        $itemlinks = array();
        if (empty($userLevel)) return $itemlinks;
        $privs = $check['fprivileges'][$userLevel];
        // deleteforum permissions
        if (!empty($privs['deleteforum'])) {
            if (!empty($this->cachedlinks['delete'])) {
                $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['delete']);
            } else {
                // if $linktype == 'object' use getObjectURL()
                if ($this->linktype == 'object') {
                    $link = xarServer::getObjectURL($args['objectname'], 'delete', $urlargs);
                } else {
                    $link = xarServer::getModuleURL('crispbb','admin','delete',$urlargs);
                }
                // check if we're using short URLs here, before trying to cache the display link
                if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                    $this->cachedlinks['delete'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                    $this->cachedlinks['delete'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                }
            }
            // make the links a little more friendly than the dd ones
            $itemlinks['delete'] = array(
                'link' => $link,
                'title' => xarML('Delete this forum'),
                'label' => xarML('Delete'),
            );
        }
        if (!empty($privs['editforum'])) {
            static $authid;

            $itemargs = $urlargs;
            if (!empty($this->cachedlinks['overview'])) {
                $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['overview']);
            } else {
                // if $linktype == 'object' use getObjectURL()
                if ($this->linktype == 'object') {
                    $link = xarServer::getObjectURL($args['objectname'], 'modify', $itemargs);
                } else {
                    $link = xarServer::getModuleURL('crispbb','admin','modify',$itemargs);
                }
                // check if we're using short URLs here, before trying to cache the display link
                if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                    $this->cachedlinks['overview'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                    $this->cachedlinks['overview'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                }
            }
            // make the links a little more friendly than the dd ones
            $itemlinks['overview'] = array(
                'link' => $link,
                'title' => xarML('View information about this forum'),
                'label' => xarML('Overview'),
            );
            if (!empty($this->cachedlinks['modify'])) {
                $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['modify']);
            } else {
                $itemargs['sublink'] = 'edit';
                // if $linktype == 'object' use getObjectURL()
                if ($this->linktype == 'object') {
                    $link = xarServer::getObjectURL($args['objectname'], 'modify', $itemargs);
                } else {
                    $link = xarServer::getModuleURL('crispbb','admin','modify',$itemargs);
                }
                // check if we're using short URLs here, before trying to cache the display link
                if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                    $this->cachedlinks['modify'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                    $this->cachedlinks['modify'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                }
            }
            // make the links a little more friendly than the dd ones
            $itemlinks['modify'] = array(
                'link' => $link,
                'title' => xarML('Edit this forum'),
                'label' => xarML('Modify'),
            );
            if (empty($authid)) $authid = xarSecGenAuthKey();
            $itemargs = $urlargs;
            $itemargs['catid'] = isset($args['catid']) ? $args['catid'] : null;
            $itemargs['direction'] = 'up';
            $itemargs['authid'] = $authid;
            if ($currentindex > 0) {
                if (!empty($this->cachedlinks['moveup'])) {
                    $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['moveup']);
                } else {
                    // if $linktype == 'object' use getObjectURL()
                    if ($this->linktype == 'object') {
                        $link = xarServer::getObjectURL($args['objectname'], 'order', $itemargs);
                    } else {
                        $link = xarServer::getModuleURL('crispbb','admin','order',$itemargs);
                    }
                    // check if we're using short URLs here, before trying to cache the display link
                    if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                        $this->cachedlinks['moveup'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                    } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                        $this->cachedlinks['moveup'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                    }
                }
                // make the links a little more friendly than the dd ones
                $itemlinks['moveup'] = array(
                    'link' => $link,
                    'title' => xarML('Move this forum up'),
                    'label' => xarML('Up'),
                );
            }
            $itemargs = $urlargs;
            $itemargs['catid'] = isset($args['catid']) ? $args['catid'] : null;
            $itemargs['direction'] = 'down';
            $itemargs['authid'] = $authid;
            if ($currentindex < $numforums-1) {
                if (!empty($this->cachedlinks['movedown'])) {
                    $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['movedown']);
                } else {
                    // if $linktype == 'object' use getObjectURL()
                    if ($this->linktype == 'object') {
                        $link = xarServer::getObjectURL($args['objectname'], 'order', $itemargs);
                    } else {
                        $link = xarServer::getModuleURL('crispbb','admin','order',$itemargs);
                    }
                    // check if we're using short URLs here, before trying to cache the display link
                    if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                        $this->cachedlinks['movedown'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                    } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                        $this->cachedlinks['movedown'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                    }
                }
                // make the links a little more friendly than the dd ones
                $itemlinks['movedown'] = array(
                    'link' => $link,
                    'title' => xarML('Move this forum down'),
                    'label' => xarML('Down'),
                );
            }
        }
        // forum viewers
        if ($check['ftype'] != 1) {
            if (!empty($this->cachedlinks['view'])) {
                $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['view']);
            } else {
                // if $linktype == 'object' use getObjectURL()
                if ($this->linktype == 'object') {
                    $link = xarServer::getObjectURL($args['objectname'], 'view', $urlargs);
                } else {
                    $link = xarServer::getModuleURL('crispbb','user','view',$urlargs);
                }
                // check if we're using short URLs here, before trying to cache the display link
                if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                    $this->cachedlinks['view'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                    $this->cachedlinks['view'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                }
            }
            // make the links a little more friendly than the dd ones
            $itemlinks['view'] = array(
                'link' => $link,
                'title' => xarML('View this forum'),
                'label' => xarML('View'),
            );
            // forum readers
            if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel', array('check' => $check, 'priv' => 'readforum'))) {
                if (!empty($check['lasttid'])) {
                    //@TODO:
                }
                // Logged in users
                if (xarUserIsLoggedIn()) {
                    $itemargs = $urlargs;
                    $itemargs['action'] = 'read';
                     if (!empty($this->cachedlinks['read'])) {
                        $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['read']);
                    } else {
                        // if $linktype == 'object' use getObjectURL()
                        if ($this->linktype == 'object') {
                            $link = xarServer::getObjectURL($args['objectname'], 'view', $itemargs);
                        } else {
                            $link = xarServer::getModuleURL('crispbb','user','view',$itemargs);
                        }
                        // check if we're using short URLs here, before trying to cache the display link
                        if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                            $this->cachedlinks['read'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                        } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                            $this->cachedlinks['read'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                        }
                    }
                    // make the links a little more friendly than the dd ones
                    $itemlinks['read'] = array(
                        'link' => $link,
                        'title' => xarML('Mark forum read'),
                        'label' => xarML('Mark Read'),
                    );
                    // forum posters
                    if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                        array('check' => $check, 'priv' => 'newtopic'))) {
                        if (!empty($this->cachedlinks['newtopic'])) {
                            $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['newtopic']);
                        } else {
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($args['objectname'], 'newtopic', $urlargs);
                            } else {
                                $link = xarServer::getModuleURL('crispbb','user','newtopic',$urlargs);
                            }
                            // check if we're using short URLs here, before trying to cache the display link
                            if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                                $this->cachedlinks['newtopic'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                            } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                                $this->cachedlinks['newtopic'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                            }
                        }
                        // make the links a little more friendly than the dd ones
                        $itemlinks['newtopic'] = array(
                            'link' => $link,
                            'title' => xarML('Post a new topic in this forum'),
                            'label' => xarML('New Topic'),
                        );
                    }
                    // forum moderators
                    if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                        array('check' => $check, 'priv' => 'ismoderator'))) {
                        $itemargs = $urlargs;
                        $itemargs['component'] = 'topics';
                        if (!empty($this->cachedlinks['moderate'])) {
                            $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['moderate']);
                        } else {
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($args['objectname'], 'moderate', $itemargs);
                            } else {
                                $link = xarServer::getModuleURL('crispbb','user','moderate',$itemargs);
                            }
                            // check if we're using short URLs here, before trying to cache the display link
                            if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                                $this->cachedlinks['moderate'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                            } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                                $this->cachedlinks['moderate'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                            }
                        }
                        // make the links a little more friendly than the dd ones
                        $itemlinks['moderate'] = array(
                            'link' => $link,
                            'title' => xarML('Moderate topics in this forum'),
                            'label' => xarML('Moderate'),
                        );
                        if (!empty($item['privs']['approvetopics'])) {
                            if (!empty($check['numtopicsubs'])) {
                                $itemargs = $urlargs;
                                $itemargs['component'] = 'topics';
                                $itemargs['tstatus'] = 2;
                                if (!empty($this->cachedlinks['submitted'])) {
                                    $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['submitted']);
                                } else {
                                    // if $linktype == 'object' use getObjectURL()
                                    if ($this->linktype == 'object') {
                                        $link = xarServer::getObjectURL($args['objectname'], 'moderate', $urlargs);
                                    } else {
                                        $link = xarServer::getModuleURL('crispbb','user','moderate',$urlargs);
                                    }
                                    // check if we're using short URLs here, before trying to cache the display link
                                    if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                                        $this->cachedlinks['submitted'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                                    } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                                        $this->cachedlinks['submitted'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                                    }
                                }
                                // make the links a little more friendly than the dd ones
                                $itemlinks['submitted'] = array(
                                    'link' => $link,
                                    'title' => xarML('View topics awaiting approval in this forum'),
                                    'label' => xarML('Waiting'),
                                );
                            }
                        }
                        if (!empty($item['privs']['deletetopics'])) {
                            if (!empty($check['numtopicdels'])) {
                                $itemargs = $urlargs;
                                $itemargs['component'] = 'topics';
                                $itemargs['tstatus'] = 5;
                                if (!empty($this->cachedlinks['deleted'])) {
                                    $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['deleted']);
                                } else {
                                    // if $linktype == 'object' use getObjectURL()
                                    if ($this->linktype == 'object') {
                                        $link = xarServer::getObjectURL($args['objectname'], 'moderate', $urlargs);
                                    } else {
                                        $link = xarServer::getModuleURL('crispbb','user','moderate',$urlargs);
                                    }
                                    // check if we're using short URLs here, before trying to cache the display link
                                    if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                                        $this->cachedlinks['deleted'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                                    } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                                        $this->cachedlinks['deleted'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                                    }
                                }
                                // make the links a little more friendly than the dd ones
                                $itemlinks['deleted'] = array(
                                    'link' => $link,
                                    'title' => xarML('View deleted topics in this forum'),
                                    'label' => xarML('Deleted'),
                                );
                            }
                        }

                    }
                    if (!empty($privs['editforum'])) {
                        $itemargs = $urlargs;
                        $itemargs['sublink'] = 'forumhooks';
                        if (!empty($this->cachedlinks['forumhooks'])) {
                            $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['forumhooks']);
                        } else {
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($args['objectname'], 'modify', $itemargs);
                            } else {
                                $link = xarServer::getModuleURL('crispbb','admin','modify',$itemargs);
                            }
                            // check if we're using short URLs here, before trying to cache the display link
                            if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                                $this->cachedlinks['forumhooks'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                            } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                                $this->cachedlinks['forumhooks'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                            }
                        }
                        // make the links a little more friendly than the dd ones
                        $itemlinks['forumhooks'] = array(
                            'link' => $link,
                            'title' => xarML('Modify forum hooks for this forum'),
                            'label' => xarML('Forum Hooks'),
                        );
                        $itemargs = $urlargs;
                        $itemargs['sublink'] = 'topichooks';
                        if (!empty($this->cachedlinks['topichooks'])) {
                            $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['topichooks']);
                        } else {
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($args['objectname'], 'modify', $itemargs);
                            } else {
                                $link = xarServer::getModuleURL('crispbb','admin','modify',$itemargs);
                            }
                            // check if we're using short URLs here, before trying to cache the display link
                            if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                                $this->cachedlinks['topichooks'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                            } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                                $this->cachedlinks['topichooks'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                            }
                        }
                        // make the links a little more friendly than the dd ones
                        $itemlinks['topichooks'] = array(
                            'link' => $link,
                            'title' => xarML('Modify topic hooks for this forum'),
                            'label' => xarML('Topic Hooks'),
                        );
                        $itemargs = $urlargs;
                        $itemargs['sublink'] = 'posthooks';
                        if (!empty($this->cachedlinks['posthooks'])) {
                            $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['posthooks']);
                        } else {
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($args['objectname'], 'modify', $itemargs);
                            } else {
                                $link = xarServer::getModuleURL('crispbb','admin','modify',$itemargs);
                            }
                            // check if we're using short URLs here, before trying to cache the display link
                            if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                                $this->cachedlinks['posthooks'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                            } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                                $this->cachedlinks['posthooks'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                            }
                        }
                        // make the links a little more friendly than the dd ones
                        $itemlinks['posthooks'] = array(
                            'link' => $link,
                            'title' => xarML('Modify post hooks for this forum'),
                            'label' => xarML('Post Hooks'),
                        );
                        $itemargs = $urlargs;
                        $itemargs['sublink'] = 'privileges';
                        if (!empty($this->cachedlinks['privileges'])) {
                            $link = str_replace('<fid>',$args['itemid'],$this->cachedlinks['privileges']);
                        } else {
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($args['objectname'], 'modify', $itemargs);
                            } else {
                                $link = xarServer::getModuleURL('crispbb','admin','modify',$itemargs);
                            }
                            // check if we're using short URLs here, before trying to cache the display link
                            if (!xarMod::$genShortUrls && strpos($link, $args['param'].'='.$args['itemid']) !== false) {
                                $this->cachedlinks['privileges'] = str_replace($args['param'].'='.$args['itemid'], $args['param'].'=<fid>', $link);
                            } elseif (strpos($link, 'f'.$args['itemid']) !== false) {
                                $this->cachedlinks['privileges'] = str_replace('f'.$args['itemid'], 'f<fid>', $link);
                            }
                        }
                        // make the links a little more friendly than the dd ones
                        $itemlinks['privileges'] = array(
                            'link' => $link,
                            'title' => xarML('Modify permissions for this forum'),
                            'label' => xarML('Privileges'),
                        );
                    }
                }
            }
        } else {
            /* @TODO: fix this
            $redirecturl = !empty($this->items[$args['itemid']]['fsettings']['redirecturl']) ? $this->fsettings['redirecturl'] : '';
            if (!empty($redirecturl)) {
                $itemlinks['view'] = array(
                    $link => $redirecturl,
                    'title' => xarML('View this forum'),
                    'label' => 'View'
                );
            }
            */
        }

        return $itemlinks;
    }

}
?>