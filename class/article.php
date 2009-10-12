<?php

sys::import('modules.dynamicdata.class.objects.base');

class ArticleObject extends DataObject
{

// TODO: check which ones are relevant in the context of dd object
    /**
     * get a specific article by aid, or by a combination of other fields
     *
     * @param id $args['aid'] id of article to get, or
     * @param id $args['pubtypeid'] pubtype id of article to get, and optional
     * @param string $args['title'] title of article to get, and optional
     * @param string $args['summary'] summary of article to get, and optional
     * @param string $args['body'] body of article to get, and optional
     * @param int $args['authorid'] id of the author of article to get, and optional
     * @param $args['pubdate'] pubdate of article to get, and optional
     * @param string $args['notes'] notes of article to get, and optional
     * @param int $args['status'] status of article to get, and optional
     * @param string $args['language'] language of article to get
     * @param bool $args['withcids'] (optional) if we want the cids too (default false)
     * @param array $args['fields'] array with all the fields to return per article
     *                        Default list is : 'aid','title','summary','authorid',
     *                        'pubdate','pubtypeid','notes','status','body'
     *                        Optional fields : 'cids','author','counter','rating','dynamicdata'
     * @param array $args['extra'] array with extra fields to return per article (in addition
     *                       to the default list). So you can EITHER specify *all* the
     *                       fields you want with 'fields', OR take all the default
     *                       ones and add some optional fields with 'extra'
     * @param id $args['ptid'] same as 'pubtypeid'
     */
    public function getItem(Array $args = array())
    {
    // FIXME: this actually only supports an itemid in DD, not any other combination of fields
        if (!empty($args['aid'])) {
            $args['itemid'] = $args['aid'];
            $itemid = parent::getItem($args);
//echo var_dump($this);
            if (!empty($args['withcids'])) {
                // TODO
/*
        $articlecids = xarMod::apiFunc('categories',
                                    'user',
                                    'getlinks',
                                    array('iids' => Array($aid),
                                          'itemtype' => $pubtypeid,
                                          'modid' => $modid,
                                          'reverse' => 0
                                         )
                                   );
*/
            }
            return $itemid;
        } else {
            // TODO: create an object list and retrieve the first article(s) that match ?
            if (!empty($args['withcids'])) {
                // TODO
            }
            $list = new ArticleObjectList($args);
            $items = $list->getItems();
            if (!empty($items)) {
//echo var_dump($items[0]);
            }
        }
    }
}

sys::import('modules.dynamicdata.class.objects.list');

class ArticleObjectList extends DataObjectList
{

// TODO: check which ones are relevant in the context of dd lists
    /**
     * get overview of all articles
     * Note : the following parameters are all optional - TODO: find other "hidden" arguments
     *
     * @param $args['numitems'] number of articles to get
     * @param $args['sort'] sort order ('pubdate','title','hits','rating','author','aid','summary','notes',...)
     * @param $args['startnum'] starting article number
     * @param $args['aids'] array of article ids to get
     * @param $args['authorid'] the ID of the author
     * @param $args['ptid'] publication type ID (for news, sections, reviews, ...), or
     * @param $args['ptids'] list of publication type IDs (for news, sections, reviews, ...)
     * @param $args['status'] array of requested status(es) for the articles
     * @param $args['search'] search parameter(s)
     * @param $args['searchfields'] array of fields to search in
     * @param $args['searchtype'] start, end, like, eq, gt, ... (TODO)
     * @param $args['cids'] array of category IDs for which to get articles (OR/AND)
     *                      (for all categories don?t set it)
     * @param $args['andcids'] true means AND-ing categories listed in cids
     * @param $args['pubdate'] articles published in a certain year (YYYY), month (YYYY-MM) or day (YYYY-MM-DD)
     * @param $args['startdate'] articles published at startdate or later
     *                           (unix timestamp format)
     * @param $args['enddate'] articles published before enddate
     *                         (unix timestamp format)
     * @param $args['fields'] array with all the fields to return per article
     *                        Default list is : 'aid','title','summary','authorid',
     *                        'pubdate','pubtypeid','notes','status','body'
     *                        Optional fields : 'cids','author','counter','rating','dynamicdata'
     * @param $args['extra'] array with extra fields to return per article (in addition
     *                       to the default list). So you can EITHER specify *all* the
     *                       fields you want with 'fields', OR take all the default
     *                       ones and add some optional fields with 'extra'
     * @param $args['where'] additional where clauses (e.g. myfield gt 1234)
     * @param $args['wheredd'] where clauses for hooked dd fields (e.g. myddfield gt 1234) [requires 'ptid' is defined]
     * @param $args['language'] language/locale (if not using multi-sites, categories etc.)
     */
    public function setArticleArguments(Array $args = array())
    {
        if (empty($args)) return array();

    // CHECKME: translate article arguments into dd arguments, or set*() them ourselves ?
    // + for option 1, use class method mapArticleArguments() instead of instance method setArticleArguments() ?

        return $args;
    }

    public function setArguments(Array $args = array())
    {
        if (empty($args)) return true;

// CHECKME: call before or after setArguments depending on approach chosen
        // set/override specific article arguments (see above)
        $args = $this->setArticleArguments($args);
//echo var_dump($args);

        return parent::setArguments($args);
    }

    /**
     * Set categories for an object (work in progress - do not use)
// CHECKME: This will be moved to objectlist if it actually works out ;-)
     *
     * @param cids array of category ids
     * @param andcids bool get items assigned to all the cids (AND = true) or any of the cids (OR = false)
     */
    public function setCategories($cids, $andcids = false)
    {
        if(!xarModIsAvailable('categories')) return;

        if (!empty($cids) && is_numeric($cids)) {
            $cids = array($cids);
        }

        if (!is_array($cids) || count($cids) == 0) return;

/**
 * CHECKME: what if categories, hitcount, rating etc. were automatically stored in DD for existing objects ?
 *          Would this make it any easier ... or not ?
 */

/**
 * CHECKME: for category selection, it would probably be easier to flip the join around, if we can combine that
 *          with the rest of the sort, where, groupby etc. clauses
 */

/**
 * CHECKME: what if we de-normalize the linkage table into modid,itemtype,itemid,cid1,cid2,cid3,cid4,cid5 ?
 */

        $categoriesdef = xarMod::apiFunc(
            'categories','user','leftjoin',
            array(
                'modid' => $this->moduleid,
                'itemtype' => $this->itemtype,
                'cids' => $cids,
                'andcids' => $andcids,
                // unused options - do they have any benefit for dd lists ?
                //'iids' => array(),    // only for these items - too early for dd here ?
                //'cidtree' => array(), // match any category in the tree(s) below the cid(s)
                //'groupcids' => null,  // group categories by 2 (typically) to show the items per combination in a category matrix
            )
        );
//echo var_dump($categoriesdef);

// CHECKME: only the startstore should be used (or we have the wrong one) ?!?
        // if we don't have a start store yet, but we do have a primary datastore, we'll start there
        if(empty($this->startstore) && !empty($this->primary)) {
            $this->startstore = $this->properties[$this->primary]->datastore;
            $this->datastores[$this->startstore]->addJoin(
                $categoriesdef['table'],
                $categoriesdef['field'],
                array(),
                $categoriesdef['where'],
                'and',
                $categoriesdef['more']
            );
            return;
        }

// CHECKME: pick the first one and hope for the best ? Or bail out before even trying this ;-)
        foreach(array_keys($this->datastores) as $name) {
// FIXME: we need to add a DISTINCT somewhere when dealing with articles in more than 1 cid
            $this->datastores[$name]->addJoin(
                $categoriesdef['table'],
                $categoriesdef['field'],
                array(),
                $categoriesdef['where'],
                'and',
                $categoriesdef['more']
            );
            return;
        }
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

        // get the items from the articles API (for now ?)
        $items = xarMod::apiFunc('articles','user','getall', $args);

        // set the items and itemids in the object
        foreach ($items as $item) {
            $this->items[$item['aid']] = $item;
            $this->itemids[] = $item['aid'];
        }

// CHECKME: if we let DD do this, we'll need to add the categories per article ourselves here ?
/*
        // Get the links for the Array of iids we have
        $cids = xarMod::apiFunc(
            'categories', 'user', 'getlinks',
            array(
                'iids' => $aids,
                'reverse' => 1,
                // Note : we don't need to specify the item type here for articles, since we use unique ids anyway
                'modid' => $modid));
*/

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

        // get the itemcount from the articles API (for now ?)
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
