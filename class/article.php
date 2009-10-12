<?php

sys::import('modules.dynamicdata.class.objects.base');

class ArticleObject extends DataObject
{
}

sys::import('modules.dynamicdata.class.objects.list');

class ArticleObjectList extends DataObjectList
{
    public function setArguments(Array $args = array())
    {
        if (empty($args)) return true;
/* TODO: getall args
            array(
                'startnum' => $startnum,
                'cids' => $cids,
                'andcids' => $andcids,
                'ptid' => (isset($ptid) ? $ptid : null),
                'authorid' => $authorid,
                'status' => $status,
                'sort' => $sort,
                'extra' => $extra,
                'where' => $where,
                'search' => $q,
                'numitems' => $numitems,
                'pubdate' => $pubdate,
                'startdate' => $startdate,
                'enddate' => $enddate
            )
*/
    }

    public function &getItems(Array $args = array())
    {
        // initialize the items array
        $this->items = array();

        // set/override the different arguments (item ids, sort, where, numitems, startnum, ...)
        $this->setArguments($args);

        if(empty($args['numitems'])) {
            $args['numitems'] = $this->numitems;
        }
        if(empty($args['startnum'])) {
            $args['startnum'] = $this->startnum;
        }

        // count the items first if we haven't done so yet, but only on demand (args['count'] = 1)
        if (!empty($this->count) && !isset($this->itemcount)) {
            $this->countItems($args);
        }

        // get the items from the articles API
        $items = xarMod::apiFunc('articles','user','getall', $args);

        // set the items and itemids in the object
        foreach ($items as $item) {
            $this->items[$item['aid']] = $item;
            $this->itemids[] = $item['aid'];
        }
//echo var_dump($this);

        // return the items
        return $this->items;
    }

    public function countItems(Array $args = array())
    {
        // set/override the different arguments (item ids, sort, where, numitems, startnum, ...)
        $this->setArguments($args);

        // initialize the itemcount
        $this->itemcount = null;

        // get the itemcount from the articles API
        $this->itemcount = xarMod::apiFunc('articles','user','countitems', $args);

        // return the itemcount
        return $this->itemcount;
    }

    function archive()
    {
        echo "hello archive";
    }

    function viewmap()
    {
        echo "hello viewmap";
    }
}

?>
