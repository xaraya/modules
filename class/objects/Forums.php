<?php
sys::import('modules.dynamicdata.class.objects.master');
sys::import('modules.dynamicdata.class.objects.list');
class Forums extends DataObject
{
    function __construct(DataObjectDescriptor $descriptor)
    {
        parent::__construct($descriptor);
        $this->moduleid = xarMod::getRegID('crispbb');
        $itemtype = xarMod::apiFunc('crispbb', 'user', 'getitemtype',
            array('fid' => !isset($this->itemid) ? 0 : $this->itemid, 'component' => 'forum'));
        $this->itemtype = !empty($itemtype) ? $itemtype : 0;
        $this->tplmodule = 'crispbb';
    }
    /*
    public function createItem(Array $args=array())
    {
        parent::createItem($args);

    }

    public function setCategories(Array $cids=array()) {
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
    /*
    public function setCategories(Array $cids=array()) {
        $categoriesdef = xarMod::apiFunc(
            'categories','user','leftjoin',
            array(
                'modid' => $this->moduleid,
                'cids' => $cids
            )
        );
        foreach(array_keys($this->datastores) as $name) {
            $this->datastores[$name]->addJoin(
                $categoriesdef['table'],
                $categoriesdef['field'],
                array(),
                $categoriesdef['where'],
                'and',
                $categoriesdef['more']
            );
        }
    }

    public function getViewOptions(Array $args = array())
    {
        $links = array();
        if (crispBB::userCan('viewcrispbb', $args['itemid'])) {
            $links['view'] = xarModURL('crispbb', 'user', 'view', array('fid' => $args['itemid']));
            $lastread = crispBB::getTracker('lastread', $args['itemid']);
            $fstatus = $args['properties']['fstatus']->value;
            $ftype = $args['properties']['ftype']->value;
            if (crispBB::userCan('readcrispbb', $args['itemid'])) {
                if (!empty($lastread)) $lastupdate = crispBB::getFtracker('lastupdate', $args['itemid']);
                if (!empty($lastupdate) && $lastupdate > $lastread) {
                    $links['markread'] = xarModURL('crispbb', 'user', 'view', array('fid' => $args['itemid'], 'read' => 1));
                }

                if (crispbb::userCan('newtopic', $args['itemid'])) {
                    $links['newtopic'] = xarModURL('crispbb', 'user', 'newtopic', array('fid' => $args['itemid']));
                }

            }
        }
        return $links;
    }
    */
}
?>