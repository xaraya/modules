<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * get overview of all articles
 * Note : the following parameters are all optional
 *
 * @param $args['numitems'] number of articles to get
 * @param $args['sort'] sort order ('pubdate','title','hits','rating','author','id','summary','notes',...)
 * @param $args['startnum'] starting article number
 * @param $args['ids'] array of article ids to get
 * @param $args['authorid'] the ID of the author
 * @param $args['ptid'] publication type ID (for news, sections, reviews, ...)
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
 *                        Default list is : 'id','title','summary','authorid',
 *                        'pubdate','pubtypeid','notes','status','body'
 *                        Optional fields : 'cids','author','counter','rating','dynamicdata'
 * @param $args['extra'] array with extra fields to return per article (in addition
 *                       to the default list). So you can EITHER specify *all* the
 *                       fields you want with 'fields', OR take all the default
 *                       ones and add some optional fields with 'extra'
 * @param $args['where'] additional where clauses (e.g. myfield gt 1234)
 * @param $args['wheredd'] where clauses for hooked dd fields (e.g. myddfield gt 1234) [requires 'ptid' is defined]
 * @param $args['language'] language/locale (if not using multi-sites, categories etc.)
 * @return array Array of articles, or false on failure
 */
function articles_userapi_getall($args)
{
    // Get arguments from argument array
    extract($args);

    // do the wheredd bit first
    //
    // A note just so you know what is actually happening here:
    // All article IDs that match the DD where-clause are fetched and put into an array.
    // This getall function is then called up again with the array passed in as a parameter.
    // If the number of matching articles is large (and there really is no limit to this) then
    // the second call to getall would result in an extremely long query string. It is not really
    // scaleable and should probably be discouraged.
    if (isset($wheredd) && !empty($ptid) && xarModIsHooked('dynamicdata','articles',$ptid) ) {
        // (is it possible to determine ptid(s) from the other args? not easily)
        $dditems = xarModApiFunc('dynamicdata','user','getitems', array('module'=>'articles', 'itemtype'=>$ptid, 'where'=>$wheredd));
        if (empty($dditems) || !count($dditems))
            return array(); // get nothing, return nothing
        $ddids = array_keys($dditems);
        if (!empty($ids))
            $args['ids'] = array_intersect( $ids, $ddids ); // allow filter on passed in ids
        else
            $args['ids'] = $ddids;
        unset($args['wheredd']);
        return xarModApiFunc( 'articles', 'user', 'getall', $args );
    }

    // Optional argument
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (empty($cids)) {
        $cids = array();
    }
    if (!isset($andcids)) {
        $andcids = false;
    }
    if (empty($ptid)) {
        $ptid = null;
    }

    // Default fields in articles (for now)
    $columns = array('id','title','summary','authorid','pubdate','pubtypeid',
                     'notes','status','body');

    // Optional fields in articles (for now)
    // + 'cids' = list of categories an article belongs to
    // + 'author' = user name of authorid
    // + 'counter' = number of times this article was displayed (hitcount)
    // + 'rating' = rating for this article (ratings)
    // + 'dynamicdata' = dynamic data fields for this article (dynamicdata)
    // + 'relevance' = relevance for this article (MySQL full-text search only)
    // $optional = array('cids','author','counter','rating','dynamicdata','relevance');

    if (!isset($fields)) {
        $fields = $columns;
    }
    if (isset($extra) && is_array($extra) && count($extra) > 0) {
        $fields = array_merge($fields,$extra);
    }

    if (empty($sort)) {
        if (!empty($search) && !empty($searchtype) && substr($searchtype,0,8) == 'fulltext') {
            if ($searchtype == 'fulltext boolean' && !in_array('relevance',$fields)) {
                // add the relevance to the field list for sorting
                $fields[] = 'relevance';
            }
            // let the database sort by relevance (= default for fulltext)
            $sortlist = array();
        } else {
            // default sort by pubdate
            $sortlist = array('pubdate');
        }
    } elseif (is_array($sort)) {
        $sortlist = $sort;
    } else {
        $sortlist = explode(',',$sort);
    }

    $articles = array();

    // Security check
    if (!xarSecurityCheck('ViewArticles')) return;

    // Fields requested by the calling function
    $required = array();
    foreach ($fields as $field) {
        $required[$field] = 1;
    }
    // mandatory fields for security
    $required['id'] = 1;
    $required['title'] = 1;
    $required['pubtypeid'] = 1;
    $required['pubdate'] = 1;
    $required['authorid'] = 1; // not to be confused with author (name) :-)
    // force cids as required when categories are given
    if (count($cids) > 0) {
        $required['cids'] = 1;
    }

// TODO: put all this in dynamic data and retrieve everything via there (including hooked stuff)

    // Database information
    $dbconn = xarDB::getConn();

    // Get the field names and LEFT JOIN ... ON ... parts from articles
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the articles-specific columns too now
    $articlesdef = xarModAPIFunc('articles','user','leftjoin',$args);

// TODO : how to handle the case where name is empty, but uname isn't

    if (!empty($required['author'])) {
        // Load API
        if (!xarModAPILoad('roles', 'user')) return;

        // Get the field names and LEFT JOIN ... ON ... parts from users
        $usersdef = xarModAPIFunc('roles','user','leftjoin');
        if (empty($usersdef)) return;
    }

    $sysid = xarMod::getID('articles');

    if (!empty($required['cids'])) {
        // Load API
        if (!xarModAPILoad('categories', 'user')) return;

        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                      array('cids' => $cids,
                                            'andcids' => $andcids,
                                            'itemtype' => isset($ptid) ? $ptid : null,
                                            'modid' => $sysid));
        if (empty($categoriesdef)) return;
    }

    if (!empty($required['counter']) && xarModIsHooked('hitcount','articles',$ptid)) {
        // Load API
        if (!xarModAPILoad('hitcount', 'user')) return;

        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from hitcount
        $hitcountdef = xarModAPIFunc('hitcount','user','leftjoin',
                                    array('modid' => $sysid,
                                          'itemtype' => isset($ptid) ? $ptid : null));
    }

    if (!empty($required['rating']) && xarModIsHooked('ratings','articles',$ptid)) {
        // Load API
        if (!xarModAPILoad('ratings', 'user')) return;

        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from ratings
        $ratingsdef = xarModAPIFunc('ratings','user','leftjoin',
                                    array('modid' => $sysid,
                                          'itemtype' => isset($ptid) ? $ptid : null));
    }

    // Create the SELECT part
    $select = array();
    foreach ($required as $field => $val) {
        // we'll handle this later
        if ($field == 'cids') {
            continue;
        } elseif ($field == 'dynamicdata') {
            continue;
        } elseif ($field == 'author') {
            $select[] = $usersdef['name'];
        } elseif ($field == 'counter') {
            if (!empty($hitcountdef['hits'])) {
                $select[] = $hitcountdef['hits'];
            }
        } elseif ($field == 'rating') {
            if (!empty($ratingsdef['rating'])) {
                $select[] = $ratingsdef['rating'];
            }
        } else {
            $select[] = $articlesdef[$field];
        }
    }
    // FIXME: <rabbitt> PostgreSQL requires that all fields in an 'Order By' be in the SELECT
    //        this has been added to remove the error that not having it creates
    // FIXME: <mikespub> Oracle doesn't allow having the same field in a query twice if you
    //        don't specify an alias (at least in sub-queries, which is what SelectLimit uses)
    if (!in_array($articlesdef['pubdate'], $select)) {
        $select[] = $articlesdef['pubdate'];
    }

    // we need distinct for multi-category OR selects where articles fit in more than 1 category
    if (count($cids) > 0) {
        $query = 'SELECT DISTINCT ' . join(', ', $select);
    } else {
        $query = 'SELECT ' . join(', ', $select);
    }

    // Create the FROM ... [LEFT JOIN ... ON ...] part
    $from = $articlesdef['table'];
    $addme = 0;
    if (!empty($required['author'])) {
        // Add the LEFT JOIN ... ON ... parts from users
        $from .= ' LEFT JOIN ' . $usersdef['table'];
        $from .= ' ON ' . $usersdef['field'] . ' = ' . $articlesdef['authorid'];
        $addme = 1;
    }

    if (!empty($required['counter']) && isset($hitcountdef)) {
        // add this for SQL compliance when there are multiple JOINs
        // bug 4429: sqlite doesnt like the parentheses
        if ($addme && ($dbconn->databaseType != 'sqlite')) {
            $from = '(' . $from . ')';
        }
        // Add the LEFT JOIN ... ON ... parts from hitcount
        $from .= ' LEFT JOIN ' . $hitcountdef['table'];
        $from .= ' ON ' . $hitcountdef['field'] . ' = ' . $articlesdef['id'];
        $addme = 1;
    }
    if (!empty($required['rating']) && isset($ratingsdef)) {
        // add this for SQL compliance when there are multiple JOINs
        // bug 4429: sqlite doesnt like the parentheses
        if ($addme && ($dbconn->databaseType != 'sqlite')) {
            $from = '(' . $from . ')';
        }
        // Add the LEFT JOIN ... ON ... parts from ratings
        $from .= ' LEFT JOIN ' . $ratingsdef['table'];
        $from .= ' ON ' . $ratingsdef['field'] . ' = ' . $articlesdef['id'];
        $addme = 1;
    }
    if (count($cids) > 0) {
        // add this for SQL compliance when there are multiple JOINs
        // bug 4429: sqlite doesnt like the parentheses
        if ($addme && ($dbconn->databaseType != 'sqlite')) {
            $from = '(' . $from . ')';
        }
        // Add the LEFT JOIN ... ON ... parts from categories
        $from .= ' LEFT JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $articlesdef['id'];
        if (!empty($categoriesdef['more']) && ($dbconn->databaseType != 'sqlite')) {
            $from = '(' . $from . ')';
            $from .= $categoriesdef['more'];
        }
    }
    $query .= ' FROM ' . $from;

// TODO: check the order of the conditions for brain-dead databases ?
    // Create the WHERE part
    $where = array();
    // we rely on leftjoin() to create the necessary articles clauses now
    if (!empty($articlesdef['where'])) {
        $where[] = $articlesdef['where'];
    }
    if (!empty($required['counter']) && !empty($hitcountdef['where'])) {
        $where[] = $hitcountdef['where'];
    }
    if (!empty($required['rating']) && !empty($ratingsdef['where'])) {
        $where[] = $ratingsdef['where'];
    }
    if (count($cids) > 0) {
        // we rely on leftjoin() to create the necessary categories clauses
        $where[] = $categoriesdef['where'];
    }
    if (count($where) > 0) {
        $query .= ' WHERE ' . join(' AND ', $where);
    }

// TODO: support other non-articles fields too someday ?
    // Create the ORDER BY part
    if (count($sortlist) > 0) {
        $sortparts = array();
        $seenid = 0;
        foreach ($sortlist as $criteria) {
            // ignore empty sort criteria
            if (empty($criteria)) continue;
            // split off trailing ASC or DESC
            if (preg_match('/^(.+)\s+(ASC|DESC)\s*$/i',$criteria,$matches)) {
                $criteria = trim($matches[1]);
                $sortorder = strtoupper($matches[2]);
            } else {
                $sortorder = '';
            }
            if ($criteria == 'title') {
                $sortparts[] = $articlesdef['title'] . ' ' . (!empty($sortorder) ? $sortorder : 'ASC');
            } elseif ($criteria == 'pubdate' || $criteria == 'date') {
                $sortparts[] = $articlesdef['pubdate'] . ' ' . (!empty($sortorder) ? $sortorder : 'DESC');
            } elseif ($criteria == 'hits' && !empty($hitcountdef['hits'])) {
                $sortparts[] = $hitcountdef['hits'] . ' ' . (!empty($sortorder) ? $sortorder : 'DESC');
            } elseif ($criteria == 'rating' && !empty($ratingsdef['rating'])) {
                $sortparts[] = $ratingsdef['rating'] . ' ' . (!empty($sortorder) ? $sortorder : 'DESC');
            } elseif ($criteria == 'author' && !empty($usersdef['name'])) {
                $sortparts[] = $usersdef['name'] . ' ' . (!empty($sortorder) ? $sortorder : 'ASC');
            } elseif ($criteria == 'relevance' && !empty($articlesdef['relevance'])) {
                $sortparts[] = 'relevance' . ' ' . (!empty($sortorder) ? $sortorder : 'DESC');
            } elseif ($criteria == 'id') {
                $sortparts[] = $articlesdef['id'] . ' ' . (!empty($sortorder) ? $sortorder : 'ASC');
                $seenid = 1;
            // other articles fields, e.g. summary, notes, ...
            } elseif (!empty($articlesdef[$criteria])) {
                $sortparts[] = $articlesdef[$criteria] . ' ' . (!empty($sortorder) ? $sortorder : 'ASC');
            } else {
                // ignore unknown sort fields
            }
        }
        // add sorting by id for unique sort order
        if (count($sortparts) < 2 && empty($seenid)) {
            $sortparts[] = $articlesdef['id'] . ' DESC';
        }
        $query .= ' ORDER BY ' . join(', ',$sortparts);

    } elseif (!empty($search) && !empty($searchtype) && substr($searchtype,0,8) == 'fulltext') {
        // For fulltext, let the database return the articles by relevance here (= default)

        // For fulltext in boolean mode, add MATCH () ... AS relevance ... ORDER BY relevance DESC (cfr. leftjoin)
        if (!empty($required['relevance']) && $searchtype == 'fulltext boolean') {
            $query .= ' ORDER BY relevance DESC, ' . $articlesdef['pubdate'] . ' DESC, ' . $articlesdef['id'] . ' DESC';
        }

    } else { // default is 'pubdate'
        $query .= ' ORDER BY ' . $articlesdef['pubdate'] . ' DESC, ' . $articlesdef['id'] . ' DESC';
    }

    // Run the query - finally :-)
    if (isset($numitems) && is_numeric($numitems)) {
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    } else {
        $result =& $dbconn->Execute($query);
    }
    if (!$result) return;

    $itemids_per_type = array();
    // Put articles into result array
    for (; !$result->EOF; $result->MoveNext()) {
        $data = $result->fields;
        $item = array();
        // loop over all required fields again
        foreach ($required as $field => $val) {
            if ($field == 'cids' || $field == 'dynamicdata' || $val != 1) {
                continue;
            }
            $value = array_shift($data);
            if ($field == 'rating') {
                $value = intval($value);
            }
            $item[$field] = $value;
        }
        // check security - don't generate an exception here
        if (empty($required['cids']) && !xarSecurityCheck('ViewArticles',0,'Article',"$item[pubtypeid]:All:$item[authorid]:$item[id]")) {
            continue;
        }
        $articles[] = $item;
        if (!empty($required['dynamicdata'])) {
            $pubtype = $item['pubtypeid'];
            if (!isset($itemids_per_type[$pubtype])) {
                $itemids_per_type[$pubtype] = array();
            }
            $itemids_per_type[$pubtype][] = $item['id'];
        }
    }
    $result->Close();

    if (!empty($required['cids']) && count($articles) > 0) {
        // Get all the categories at once
        $ids = array();
        foreach ($articles as $article) {
            $ids[] = $article['id'];
        }

        // Load API
        if (!xarModAPILoad('categories', 'user')) return;

        // Get the links for the Array of iids we have
        $cids = xarModAPIFunc('categories',
                             'user',
                             'getlinks',
                             array('iids' => $ids,
                                   'reverse' => 1,
                               // Note : we don't need to specify the item type here for articles, since we use unique ids anyway
                                   'modid' => $sysid));

        // Inserting the corresponding Category ID in the Article Description
        $delete = array();
        $cachesec = array();
        foreach ($articles as $key => $article) {
            if (isset($cids[$article['id']]) && count($cids[$article['id']]) > 0) {
                $articles[$key]['cids'] = $cids[$article['id']];
                foreach ($cids[$article['id']] as $cid) {
                    if (!xarSecurityCheck('ViewArticles',0,'Article',"$article[pubtypeid]:$cid:$article[authorid]:$article[id]")) {
                        $delete[$key] = 1;
                        break;
                    }
                    if (!isset($cachesec[$cid])) {
                    // TODO: combine with ViewCategoryLink check when we can combine module-specific
                    // security checks with "parent" security checks transparently ?
                        $cachesec[$cid] = xarSecurityCheck('ReadCategories',0,'Category',"All:$cid");
                    }
                    if (!$cachesec[$cid]) {
                        $delete[$key] = 1;
                        break;
                    }
                }
            } else {
                if (!xarSecurityCheck('ViewArticles',0,'Article',"$article[pubtypeid]:All:$article[authorid]:$article[id]")) {
                    $delete[$key] = 1;
                    continue;
                }
            }
        }
        if (count($delete) > 0) {
            foreach ($delete as $key => $val) {
                unset($articles[$key]);
            }
        }
    }

    if (!empty($required['dynamicdata']) && count($articles) > 0) {
        foreach ($itemids_per_type as $pubtype => $itemids) {
            if (!xarModIsHooked('dynamicdata','articles',$pubtype)) {
                continue;
            }
            list($properties,$items) = xarModAPIFunc('dynamicdata','user','getitemsforview',
                                                     array('module'   => 'articles',
                                                           'itemtype' => $pubtype,
                                                           'itemids'  => $itemids,
                                                           // ignore the display-only properties
                                                           'status'   => 1));

            if (empty($properties) || count($properties) == 0) continue;
            foreach ($articles as $key => $article) {

                // otherwise articles (of different pub types) with dd properties having the same
                // names reset previously set values to empty string for each iteration based on the pubtype
                if ($article['pubtypeid'] != $pubtype) continue;

                foreach (array_keys($properties) as $name) {
                    if (isset($items[$article['id']]) && isset($items[$article['id']][$name])) {
                        $value = $items[$article['id']][$name];
                    } else {
                        $value = $properties[$name]->default;
                    }

                    $articles[$key][$name] = $value;

                    // TODO: clean up this temporary fix
                    if (!empty($value)) {
                        $articles[$key][$name.'_output'] = $properties[$name]->showOutput(array('value' => $value));
                    }
                }
            }
        }
    }

    return $articles;
}

?>