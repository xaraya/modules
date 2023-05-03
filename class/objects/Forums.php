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
        $check = $this->getFieldValues(array(), 1);
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
        $dbconn = xarDB::getConn();
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
                $result = $dbconn->Execute($query,array());
            }
            // remove topics
            if (!empty($tids)) {
                // first from topics table
                $query = "DELETE FROM $topicstable WHERE id IN (" . join(',', $tids) . ")";
                $result = $dbconn->Execute($query,array());
                // then from hooks table
                $query = "DELETE FROM $hookstable WHERE tid IN (" . join(',', $tids) . ")";
                $result = $dbconn->Execute($query,array());
            }
            // remove forum itemtype.
            // @TODO check for existence of topic and post components for this forum
            // in other forums (moved/merged topics and posts) and remove if none found
            $query = "DELETE FROM $itemtypestable WHERE fid = ? AND component = 'Forum'";
            $result = $dbconn->Execute($query,array($this->itemid));
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
        // @TODO: Itemtype Delete Hooks
        // and finally, remove the forum itself :)
        return parent::deleteItem($args);
    }
    /**
     * populate itemlinks for this forum
    **/
    public function getItemLinks(Array $args = array())
    {
        sys::import('modules.crispbb.class.cache.links');
        extract($args);
        $itemlinks = array();
        if (empty($this->userLevel)) return;
        $check = $this->getFieldValues();
        $check['fid'] = $this->itemid;
        $check['fprivileges'] = $this->fprivileges;
        $privs = $this->fprivileges[$this->userLevel];
        // deleteforum permissions
        if (!empty($privs['deleteforum'])) {
                $link = LinkCache::getCachedURL('crispbb', 'admin', 'delete', array('fid' => $this->itemid));
            $itemlinks['delete'] = $link;
        }
        if (!empty($privs['editforum'])) {
                $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid, 'sublink' => 'edit'));
            $itemlinks['modify'] = $link;
                $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid));
            $itemlinks['overview'] = $link;
        }
        // forum viewers
        if ($check['ftype'] != 1) {
                $link = LinkCache::getCachedURL('crispbb', 'user', 'view', array('fid' => $this->itemid));
            $itemlinks['view'] = $link;
            // forum readers
            if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel', array('check' => $check, 'priv' => 'readforum'))) {
                if (!empty($check['lasttid'])) {
                    //@TODO:
                }
                // Logged in users
                if (xarUser::isLoggedIn()) {
                        $link = LinkCache::getCachedURL('crispbb', 'user', 'view', array('fid' => $this->itemid, 'action' => 'read'));
                    $itemlinks['read'] = $link;
                    // forum posters
                    if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                        array('check' => $check, 'priv' => 'newtopic'))) {
                            $link = LinkCache::getCachedURL('crispbb', 'user', 'newtopic', array('fid' => $this->itemid));
                        $itemlinks['newtopic'] = $link;
                    }
                    // forum moderators
                    if (xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
                        array('check' => $check, 'priv' => 'ismoderator'))) {
                            $link = LinkCache::getCachedURL('crispbb', 'user', 'moderate', array('component' => 'topics', 'fid' => $this->itemid));
                        $itemlinks['moderate'] = $link;
                    }
                    if (!empty($privs['editforum'])) {
                            $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid, 'sublink' => 'forumhooks'));
                        $itemlinks['forumhooks'] = $link;
                            $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid, 'sublink' => 'topichooks'));
                        $itemlinks['topichooks'] = $link;
                            $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid, 'sublink' => 'posthooks'));
                        $itemlinks['posthooks'] = $link;
                            $link = LinkCache::getCachedURL('crispbb', 'admin', 'modify', array('fid' => $this->itemid, 'sublink' => 'privileges'));
                        $itemlinks['privileges'] = $link;
                    }
                }
            }
        } else {
            $redirecturl = !empty($this->fsettings['redirected']) ? $this->fsettings['redirected'] : '';
            if (!empty($redirecturl)) {
                $itemlinks['view'] = $redirecturl;
            }
        }
        $this->itemlinks = $itemlinks;
    }

    // update a forum, we don't call parent here, otherwise nohooks will be ignored
    public function updateItem(Array $args = array())
    {
        $itemid = parent::updateItem($args);

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
        $itemid = parent::createItem($args);
        
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
    public function joinCategories(Array $cids=array()) 
    {
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
    public function getViewOptions($itemid = null)
    {
        if (empty($itemid)) return array();
        $data['itemid'] = $itemid;
        // import our own class to handle link cache (less code required)
        sys::import('modules.crispbb.class.cache.links');
        // insist on using fid in urlargs (for now)
        $data['param'] = 'fid';
        $urlargs = array();
        $urlargs[$data['param']] = $data['itemid'];
        $check = $this->items[$data['itemid']];
        $check['fid'] = $data['itemid'];
        $check['fprivileges'] = unserialize($this->items[$data['itemid']]['fprivileges']);
        $userLevel = xarMod::apiFunc('crispbb', 'user', 'checkseclevel',
            array('check' => $check, 'priv' => 'viewforum'));
        $numforums = count($this->items);
        $fids = !empty($numforums) ? array_keys($this->items) : array();
        $currentindex = 0;
        foreach ($fids as $i => $fid) {
            if ($fid == $data['itemid']) {
                $currentindex = $i;
                break;
            }
        }
        $itemlinks = array();
        if (empty($userLevel)) return $itemlinks;
        $privs = $check['fprivileges'][$userLevel];
        // deleteforum permissions
        if (!empty($privs['deleteforum'])) {
                 // if $linktype == 'object' use getObjectURL()
                if ($this->linktype == 'object') {
                    $link = xarServer::getObjectURL($data['objectname'], 'delete', $urlargs);
                } else {
                    $link = LinkCache::getCachedURL('crispbb','admin','delete',$urlargs);
                }
            // make the links a little more friendly than the dd ones
            $itemlinks['delete'] = array(
                'link' => $link,
                'title' => xarML('Delete this forum'),
                'label' => xarML('Delete'),
            );
        }
        if (!empty($privs['editforum'])) {
            $itemargs = $urlargs;
            static $authid;
                // if $linktype == 'object' use getObjectURL()
                if ($this->linktype == 'object') {
                    $link = xarServer::getObjectURL($data['objectname'], 'modify', $itemargs);
                } else {
                    $link = LinkCache::getCachedURL('crispbb','admin','modify',$itemargs);
                }
            // make the links a little more friendly than the dd ones
            $itemlinks['overview'] = array(
                'link' => $link,
                'title' => xarML('View information about this forum'),
                'label' => xarML('Overview'),
            );
            $itemargs = $urlargs;
            $itemargs['sublink'] = 'edit';
                // if $linktype == 'object' use getObjectURL()
                if ($this->linktype == 'object') {
                    $link = xarServer::getObjectURL($data['objectname'], 'modify', $itemargs);
                } else {
                    $link = LinkCache::getCachedURL('crispbb','admin','modify',$itemargs);
                }
            // make the links a little more friendly than the dd ones
            $itemlinks['modify'] = array(
                'link' => $link,
                'title' => xarML('Edit this forum'),
                'label' => xarML('Modify'),
            );
            if (empty($authid)) $authid = xarSec::genAuthKey();
            $itemargs = $urlargs;
            $itemargs['catid'] = isset($data['catid']) ? $data['catid'] : null;
            $itemargs['direction'] = 'up';
            $itemargs['authid'] = $authid;
            if ($currentindex > 0) {
                    // if $linktype == 'object' use getObjectURL()
                    if ($this->linktype == 'object') {
                        $link = xarServer::getObjectURL($data['objectname'], 'order', $itemargs);
                    } else {
                        $link = LinkCache::getCachedURL('crispbb','admin','order',$itemargs);
                    }
                // make the links a little more friendly than the dd ones
                $itemlinks['moveup'] = array(
                    'link' => $link,
                    'title' => xarML('Move this forum up'),
                    'label' => xarML('Up'),
                );
            }
            $itemargs = $urlargs;
            $itemargs['catid'] = isset($data['catid']) ? $data['catid'] : null;
            $itemargs['direction'] = 'down';
            $itemargs['authid'] = $authid;
            if ($currentindex < $numforums-1) {
                    // if $linktype == 'object' use getObjectURL()
                    if ($this->linktype == 'object') {
                        $link = xarServer::getObjectURL($data['objectname'], 'order', $itemargs);
                    } else {
                        $link = LinkCache::getCachedURL('crispbb','admin','order',$itemargs);
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
                // if $linktype == 'object' use getObjectURL()
                if ($this->linktype == 'object') {
                    $link = xarServer::getObjectURL($data['objectname'], 'view', $urlargs);
                } else {
                    $link = LinkCache::getCachedURL('crispbb','user','view',$urlargs);
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
                if (xarUser::isLoggedIn()) {
                    $itemargs = $urlargs;
                    $itemargs['action'] = 'read';
                        // if $linktype == 'object' use getObjectURL()
                        if ($this->linktype == 'object') {
                            $link = xarServer::getObjectURL($data['objectname'], 'view', $itemargs);
                        } else {
                            $link = LinkCache::getCachedURL('crispbb','user','view',$itemargs);
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
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($data['objectname'], 'newtopic', $urlargs);
                            } else {
                                $link = LinkCache::getCachedURL('crispbb','user','newtopic',$urlargs);
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
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($data['objectname'], 'moderate', $itemargs);
                            } else {
                                $link = LinkCache::getCachedURL('crispbb','user','moderate',$itemargs);
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
                                    // if $linktype == 'object' use getObjectURL()
                                    if ($this->linktype == 'object') {
                                        $link = xarServer::getObjectURL($data['objectname'], 'moderate', $urlargs);
                                    } else {
                                        $link = LinkCache::getCachedURL('crispbb','user','moderate',$urlargs);
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
                                    // if $linktype == 'object' use getObjectURL()
                                    if ($this->linktype == 'object') {
                                        $link = xarServer::getObjectURL($data['objectname'], 'moderate', $itemargs);
                                    } else {
                                        $link = LinkCache::getCachedURL('crispbb','user','moderate',$itemargs);
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
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($data['objectname'], 'modify', $itemargs);
                            } else {
                                $link = LinkCache::getCachedURL('crispbb','admin','modify',$itemargs);
                            }
                        // make the links a little more friendly than the dd ones
                        $itemlinks['forumhooks'] = array(
                            'link' => $link,
                            'title' => xarML('Modify forum hooks for this forum'),
                            'label' => xarML('Forum Hooks'),
                        );
                        $itemargs = $urlargs;
                        $itemargs['sublink'] = 'topichooks';
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($data['objectname'], 'modify', $itemargs);
                            } else {
                                $link = LinkCache::getCachedURL('crispbb','admin','modify',$itemargs);
                            }
                        // make the links a little more friendly than the dd ones
                        $itemlinks['topichooks'] = array(
                            'link' => $link,
                            'title' => xarML('Modify topic hooks for this forum'),
                            'label' => xarML('Topic Hooks'),
                        );
                        $itemargs = $urlargs;
                        $itemargs['sublink'] = 'posthooks';
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($data['objectname'], 'modify', $itemargs);
                            } else {
                                $link = LinkCache::getCachedURL('crispbb','admin','modify',$itemargs);
                            }
                        // make the links a little more friendly than the dd ones
                        $itemlinks['posthooks'] = array(
                            'link' => $link,
                            'title' => xarML('Modify post hooks for this forum'),
                            'label' => xarML('Post Hooks'),
                        );
                        $itemargs = $urlargs;
                        $itemargs['sublink'] = 'privileges';
                            // if $linktype == 'object' use getObjectURL()
                            if ($this->linktype == 'object') {
                                $link = xarServer::getObjectURL($data['objectname'], 'modify', $itemargs);
                            } else {
                                $link = LinkCache::getCachedURL('crispbb','admin','modify',$itemargs);
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
            $check['fsettings'] = unserialize($this->items[$data['itemid']]['fsettings']);
            $redirecturl = $check['fsettings']['redirected'];
            if (!empty($redirecturl)) {
                $itemlinks['view'] = array(
                    'link' => $redirecturl,
                    'title' => xarML('View this forum'),
                    'label' => 'View'
                );
            }
        }

        return $itemlinks;
    }

}
?>