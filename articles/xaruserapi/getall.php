<?php

/**
 * get overview of all articles
 * Note : the following parameters are all optional
 *
 * @param $args['numitems'] number of articles to get
 * @param $args['sort'] sort order ('date','title','hits','rating',...)
 * @param $args['startnum'] starting article number
 * @param $args['aids'] array of article ids to get
 * @param $args['authorid'] the ID of the author
 * @param $args['ptid'] publication type ID (for news, sections, reviews, ...)
 * @param $args['status'] array of requested status(es) for the articles
 * @param $args['search'] search parameter(s)
 * @param $args['cids'] array of category IDs for which to get articles (OR/AND)
 *                      (for all categories don´t set it)
 * @param $args['andcids'] true means AND-ing categories listed in cids
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
 * @returns array
 * @return array of articles, or false on failure
 */
function articles_userapi_getall($args)
{
    // Get arguments from argument array
    extract($args);

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
    if (empty($sort)) {
        $sort = 'date';
    }
    if (empty($ptid)) {
        $ptid = null;
    }

    // Default fields in articles (for now)
    $columns = array('aid','title','summary','authorid','pubdate','pubtypeid',
                     'notes','status','body');

    // Optional fields in articles (for now)
    // + 'cids' = list of categories an article belongs to
    // + 'author' = user name of authorid
    // + 'counter' = number of times this article was displayed (hitcount)
    // + 'rating' = rating for this article (ratings)
    // + 'dynamicdata' = dynamic data fields for this article (dynamicdata)
    // $optional = array('cids','author','counter','rating','dynamicdata');

    if (!isset($fields)) {
        $fields = $columns;
    }
    if (isset($extra) && is_array($extra) && count($extra) > 0) {
        $fields = array_merge($fields,$extra);
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
    $required['aid'] = 1;
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
    $dbconn =& xarDBGetConn();

    // Get the field names and LEFT JOIN ... ON ... parts from articles
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the articles-specific columns too now
    $articlesdef = xarModAPIFunc('articles','user','leftjoin',$args);

// TODO : how to handle the case where xar_name is empty, but xar_uname isn't

    if (!empty($required['author'])) {
        // Load API
        if (!xarModAPILoad('roles', 'user')) return;

        // Get the field names and LEFT JOIN ... ON ... parts from users
        $usersdef = xarModAPIFunc('roles','user','leftjoin');
        if (empty($usersdef)) return;
    }

    if (!empty($required['cids'])) {
        // Load API
        if (!xarModAPILoad('categories', 'user')) return;

        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from categories
        $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                      array('cids' => $cids,
                                            'andcids' => $andcids,
                                            'itemtype' => isset($ptid) ? $ptid : null,
                                            'modid' =>
                                              xarModGetIDFromName('articles')));
    }

    if (!empty($required['counter']) && xarModIsHooked('hitcount','articles',$ptid)) {
        // Load API
        if (!xarModAPILoad('hitcount', 'user')) return;

        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from hitcount
        $hitcountdef = xarModAPIFunc('hitcount','user','leftjoin',
                                    array('modid' =>
                                            xarModGetIDFromName('articles'),
                                          'itemtype' => isset($ptid) ? $ptid : null));
    }

    if (!empty($required['rating']) && xarModIsHooked('ratings','articles',$ptid)) {
        // Load API
        if (!xarModAPILoad('ratings', 'user')) return;

        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from ratings
        $ratingsdef = xarModAPIFunc('ratings','user','leftjoin',
                                    array('modid' =>
                                            xarModGetIDFromName('articles'),
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
    $select[] = $articlesdef['pubdate'];
    
    // we need distinct for multi-category OR selects where articles fit in more than 1 category
    $query = 'SELECT DISTINCT ' . join(', ', $select);

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
        if ($addme) {
            $from = '(' . $from . ')';
        }
        // Add the LEFT JOIN ... ON ... parts from hitcount
        $from .= ' LEFT JOIN ' . $hitcountdef['table'];
        $from .= ' ON ' . $hitcountdef['field'] . ' = ' . $articlesdef['aid'];
        $addme = 1;
    }
    if (!empty($required['rating']) && isset($ratingsdef)) {
        // add this for SQL compliance when there are multiple JOINs
        if ($addme) {
            $from = '(' . $from . ')';
        }
        // Add the LEFT JOIN ... ON ... parts from ratings
        $from .= ' LEFT JOIN ' . $ratingsdef['table'];
        $from .= ' ON ' . $ratingsdef['field'] . ' = ' . $articlesdef['aid'];
        $addme = 1;
    }
    if (count($cids) > 0) {
        // add this for SQL compliance when there are multiple JOINs
        if ($addme) {
            $from = '(' . $from . ')';
        }
        // Add the LEFT JOIN ... ON ... parts from categories
        $from .= ' LEFT JOIN ' . $categoriesdef['table'];
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $articlesdef['aid'];
        if (!empty($categoriesdef['more'])) {
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

// TODO: make this configurable too someday ?
    // Create the ORDER BY part
    if ($sort == 'title') {
        $query .= ' ORDER BY ' . $articlesdef['title'] . ' ASC, ' . $articlesdef['aid'] . ' DESC';
    } elseif ($sort == 'hits' && !empty($hitcountdef['hits'])) {
        $query .= ' ORDER BY ' . $hitcountdef['hits'] . ' DESC, ' . $articlesdef['aid'] . ' DESC';
    } elseif ($sort == 'rating' && !empty($ratingsdef['rating'])) {
        $query .= ' ORDER BY ' . $ratingsdef['rating'] . ' DESC, ' . $articlesdef['aid'] . ' DESC';
    } else { // default is 'date'
        $query .= ' ORDER BY ' . $articlesdef['pubdate'] . ' DESC, ' . $articlesdef['aid'] . ' DESC';
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
        if (empty($required['cids']) && !xarSecurityCheck('ViewArticles',0,'Article',"$item[pubtypeid]:All:$item[authorid]:$item[aid]")) {
            continue;
        }
        $articles[] = $item;
        if (!empty($required['dynamicdata'])) {
            $pubtype = $item['pubtypeid'];
            if (!isset($itemids_per_type[$pubtype])) {
                $itemids_per_type[$pubtype] = array();
            }
            $itemids_per_type[$pubtype][] = $item['aid'];
        }
    }
    $result->Close();

    if (!empty($required['cids']) && count($articles) > 0) {
        // Get all the categories at once
        $aids = array();
        foreach ($articles as $article) {
            $aids[] = $article['aid'];
        }

        // Load API
        if (!xarModAPILoad('categories', 'user')) return;

        // Get the links for the Array of iids we have
        $cids = xarModAPIFunc('categories',
                             'user',
                             'getlinks',
                             array('iids' => $aids,
                                   'reverse' => 1,
                               // Note : we don't need to specify the item type here for articles, since we use unique ids anyway
                                   'modid' => xarModGetIDFromName('articles')));

        // Inserting the corresponding Category ID in the Article Description
        $delete = array();
        foreach ($articles as $key => $article) {
            if (isset($cids[$article['aid']]) && count($cids[$article['aid']]) > 0) {
                $articles[$key]['cids'] = $cids[$article['aid']];
                foreach ($cids[$article['aid']] as $cid) {
                    if (!xarSecurityCheck('ViewArticles',0,'Article',"$article[pubtypeid]:$cid:$article[authorid]:$article[aid]")) {
                        $delete[$key] = 1;
                        break;
                    }
                }
            } else {
                if (!xarSecurityCheck('ViewArticles',0,'Article',"$article[pubtypeid]:All:$article[authorid]:$article[aid]")) {
                    $delete[$key] = 1;
                    break;
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
            if (empty($properties) || count($properties) == 0 || empty($items) || count($items) == 0) continue;
            foreach ($articles as $key => $article) {
                if (isset($items[$article['aid']])) {
                // TODO: compare with array_merge
                    foreach ($items[$article['aid']] as $name => $value) {
                        $articles[$key][$name] = $value;
                    // TODO: clean up this temporary fix
                        if (isset($properties[$name]) && !empty($value)) {
                            $articles[$key][$name.'_output'] = $properties[$name]->showOutput(array('value' => $value));
                        }
                    }
                }
            }
        }
    }

    return $articles;
}

?>
