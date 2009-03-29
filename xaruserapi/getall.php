<?php
/**
 * Publications module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Publications Module
 
 * @author mikespub
 */
/**
 * get overview of all publications
 * Note : the following parameters are all optional
 *
 * @param $args['numitems'] number of publications to get
 * @param $args['sort'] sort order ('create_date','title','hits','rating','author','id','summary','notes',...)
 * @param $args['startnum'] starting article number
 * @param $args['ids'] array of article ids to get
 * @param $args['owner'] the ID of the author
 * @param $args['ptid'] publication type ID (for news, sections, reviews, ...)
 * @param $args['state'] array of requested status(es) for the publications
 * @param $args['search'] search parameter(s)
 * @param $args['searchfields'] array of fields to search in
 * @param $args['searchtype'] start, end, like, eq, gt, ... (TODO)
 * @param $args['cids'] array of category IDs for which to get publications (OR/AND)
 *                      (for all categories don?t set it)
 * @param $args['andcids'] true means AND-ing categories listed in cids
 * @param $args['create_date'] publications published in a certain year (YYYY), month (YYYY-MM) or day (YYYY-MM-DD)
 * @param $args['startdate'] publications published at startdate or later
 *                           (unix timestamp format)
 * @param $args['enddate'] publications published before enddate
 *                         (unix timestamp format)
 * @param $args['fields'] array with all the fields to return per article
 *                        Default list is : 'id','title','summary','owner',
 *                        'create_date','pubtype_id','notes','state','body'
 *                        Optional fields : 'cids','author','counter','rating','dynamicdata'
 * @param $args['extra'] array with extra fields to return per article (in addition
 *                       to the default list). So you can EITHER specify *all* the
 *                       fields you want with 'fields', OR take all the default
 *                       ones and add some optional fields with 'extra'
 * @param $args['where'] additional where clauses (e.g. myfield gt 1234)
 * @param $args['wheredd'] where clauses for hooked dd fields (e.g. myddfield gt 1234) [requires 'ptid' is defined]
 * @param $args['locale'] language/locale (if not using multi-sites, categories etc.)
 * @return array Array of publications, or false on failure
 */
function publications_userapi_getall($args)
{
    // Get arguments from argument array
    extract($args);

    // do the wheredd bit first
    //
    // A note just so you know what is actually happening here:
    // All article IDs that match the DD where-clause are fetched and put into an array.
    // This getall function is then called up again with the array passed in as a parameter.
    // If the number of matching publications is large (and there really is no limit to this) then
    // the second call to getall would result in an extremely long query string. It is not really
    // scaleable and should probably be discouraged.
    if (isset($wheredd) && !empty($ptid) && xarModIsHooked('dynamicdata','publications',$ptid) ) {
        // (is it possible to determine ptid(s) from the other args? not easily)
        $dditems = xarModApiFunc('dynamicdata','user','getitems', array('module'=>'publications', 'itemtype'=>$ptid, 'where'=>$wheredd));
        if (empty($dditems) || !count($dditems))
            return array(); // get nothing, return nothing
        $ddids = array_keys($dditems);
        if (!empty($ids))
            $args['ids'] = array_intersect( $ids, $ddids ); // allow filter on passed in ids
        else
            $args['ids'] = $ddids;
        unset($args['wheredd']);
        return xarModApiFunc( 'publications', 'user', 'getall', $args );
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
    if (empty($ptid)) $ptid = null;

    // Default fields in publications (for now)
    $columns = array('id','title','summary','owner','pubtype_id',
                     'notes','state');

    // Optional fields in publications (for now)
    // + 'cids' = list of categories an article belongs to
    // + 'author' = user name of owner
    // + 'counter' = number of times this article was displayed (hitcount)
    // + 'rating' = rating for this article (ratings)
    // + 'dynamicdata' = dynamic data fields for this article (dynamicdata)
    // + 'relevance' = relevance for this article (MySQL full-text search only)
    // $optional = array('cids','author','counter','rating','dynamicdata','relevance');

    if (!isset($fields)) $fields = $columns;

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
            // default sort by create_date
            $sortlist = array('create_date');
        }
    } elseif (is_array($sort)) {
        $sortlist = $sort;
    } else {
        $sortlist = explode(',',$sort);
    }

    $publications = array();

    // Security check
    if (!xarSecurityCheck('ViewPublications')) return;

    // Fields requested by the calling function
    $required = array();
    foreach ($fields as $field) {
        $required[$field] = 1;
    }
    // mandatory fields for security
    $required['id'] = 1;
    $required['title'] = 1;
    $required['pubtype_id'] = 1;
    $required['create_date'] = 1;
    $required['owner'] = 1; // not to be confused with author (name) :-)
    // force cids as required when categories are given
    if (count($cids) > 0) {
        $required['cids'] = 1;
    }

// TODO: put all this in dynamic data and retrieve everything via there (including hooked stuff)

    // Database information
    $dbconn = xarDB::getConn();

    // Get the field names and LEFT JOIN ... ON ... parts from publications
    // By passing on the $args, we can let leftjoin() create the WHERE for
    // the publications-specific columns too now
    $publicationsdef = xarModAPIFunc('publications','user','leftjoin',$args);

// TODO : how to handle the case where name is empty, but uname isn't

    if (!empty($required['owner'])) {
        // Load API
        if (!xarModAPILoad('roles', 'user')) return;

        // Get the field names and LEFT JOIN ... ON ... parts from users
        $usersdef = xarModAPIFunc('roles','user','leftjoin');
        if (empty($usersdef)) return;
    }

    $sysid = xarMod::getID('publications');

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

    if (!empty($required['counter']) && xarModIsHooked('hitcount','publications',$ptid)) {
        // Load API
        if (!xarModAPILoad('hitcount', 'user')) return;

        // Get the LEFT JOIN ... ON ...  and WHERE (!) parts from hitcount
        $hitcountdef = xarModAPIFunc('hitcount','user','leftjoin',
                                    array('modid' => $sysid,
                                          'itemtype' => isset($ptid) ? $ptid : null));
    }

    if (!empty($required['rating']) && xarModIsHooked('ratings','publications',$ptid)) {
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
        } elseif ($field == 'owner') {
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
            $select[] = $publicationsdef[$field];
        }
    }

    // FIXME: <rabbitt> PostgreSQL requires that all fields in an 'Order By' be in the SELECT
    //        this has been added to remove the error that not having it creates
    // FIXME: <mikespub> Oracle doesn't allow having the same field in a query twice if you
    //        don't specify an alias (at least in sub-queries, which is what SelectLimit uses)
//    if (!in_array($publicationsdef['create_date'], $select)) {
//        $select[] = $publicationsdef['create_date'];
//    }

    // we need distinct for multi-category OR selects where publications fit in more than 1 category
    if (count($cids) > 0) {
        $query = 'SELECT DISTINCT ' . join(', ', $select);
    } else {
        $query = 'SELECT ' . join(', ', $select);
    }

//    var_dump($required);exit;
    // Create the FROM ... [LEFT JOIN ... ON ...] part
    $from = $publicationsdef['table'];
    $addme = 0;
    if (!empty($required['owner'])) {
        // Add the LEFT JOIN ... ON ... parts from users
        $from .= ' LEFT JOIN ' . $usersdef['table'];
        $from .= ' ON ' . $usersdef['field'] . ' = ' . $publicationsdef['owner'];
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
        $from .= ' ON ' . $hitcountdef['field'] . ' = ' . $publicationsdef['id'];
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
        $from .= ' ON ' . $ratingsdef['field'] . ' = ' . $publicationsdef['id'];
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
        $from .= ' ON ' . $categoriesdef['field'] . ' = ' . $publicationsdef['id'];
        if (!empty($categoriesdef['more']) && ($dbconn->databaseType != 'sqlite')) {
            $from = '(' . $from . ')';
            $from .= $categoriesdef['more'];
        }
    }
    $query .= ' FROM ' . $from;

// TODO: check the order of the conditions for brain-dead databases ?
    // Create the WHERE part
    $where = array();
    // we rely on leftjoin() to create the necessary publications clauses now
    if (!empty($publicationsdef['where'])) {
        $where[] = $publicationsdef['where'];
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

// TODO: support other non-publications fields too someday ?
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
                $sortparts[] = $publicationsdef['title'] . ' ' . (!empty($sortorder) ? $sortorder : 'ASC');
//            } elseif ($criteria == 'create_date' || $criteria == 'date') {
//                $sortparts[] = $publicationsdef['create_date'] . ' ' . (!empty($sortorder) ? $sortorder : 'DESC');
            } elseif ($criteria == 'hits' && !empty($hitcountdef['hits'])) {
                $sortparts[] = $hitcountdef['hits'] . ' ' . (!empty($sortorder) ? $sortorder : 'DESC');
            } elseif ($criteria == 'rating' && !empty($ratingsdef['rating'])) {
                $sortparts[] = $ratingsdef['rating'] . ' ' . (!empty($sortorder) ? $sortorder : 'DESC');
            } elseif ($criteria == 'owner' && !empty($usersdef['name'])) {
                $sortparts[] = $usersdef['name'] . ' ' . (!empty($sortorder) ? $sortorder : 'ASC');
            } elseif ($criteria == 'relevance' && !empty($publicationsdef['relevance'])) {
                $sortparts[] = 'relevance' . ' ' . (!empty($sortorder) ? $sortorder : 'DESC');
            } elseif ($criteria == 'id') {
                $sortparts[] = $publicationsdef['id'] . ' ' . (!empty($sortorder) ? $sortorder : 'ASC');
                $seenid = 1;
            // other publications fields, e.g. summary, notes, ...
            } elseif (!empty($publicationsdef[$criteria])) {
                $sortparts[] = $publicationsdef[$criteria] . ' ' . (!empty($sortorder) ? $sortorder : 'ASC');
            } else {
                // ignore unknown sort fields
            }
        }
        // add sorting by id for unique sort order
        if (count($sortparts) < 2 && empty($seenid)) {
            $sortparts[] = $publicationsdef['id'] . ' DESC';
        }
        $query .= ' ORDER BY ' . join(', ',$sortparts);

    } elseif (!empty($search) && !empty($searchtype) && substr($searchtype,0,8) == 'fulltext') {
        // For fulltext, let the database return the publications by relevance here (= default)

        // For fulltext in boolean mode, add MATCH () ... AS relevance ... ORDER BY relevance DESC (cfr. leftjoin)
        if (!empty($required['relevance']) && $searchtype == 'fulltext boolean') {
            $query .= ' ORDER BY relevance DESC, ' . $publicationsdef['create_date'] . ' DESC, ' . $publicationsdef['id'] . ' DESC';
        }

    } else { // default is 'create_date'
        $query .= ' ORDER BY ' . $publicationsdef['create_date'] . ' DESC, ' . $publicationsdef['id'] . ' DESC';
    }

    // Run the query - finally :-)
    if (isset($numitems) && is_numeric($numitems)) {
        $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    } else {
        $result =& $dbconn->Execute($query);
    }
    if (!$result) return;

    $itemids_per_type = array();
    // Put publications into result array
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
        if (empty($required['cids']) && !xarSecurityCheck('ViewPublications',0,'Publication',"$item[pubtype_id]:All:$item[owner]:$item[id]")) {
            continue;
        }
        $publications[] = $item;
        if (!empty($required['dynamicdata'])) {
            $pubtype = $item['pubtype_id'];
            if (!isset($itemids_per_type[$pubtype])) {
                $itemids_per_type[$pubtype] = array();
            }
            $itemids_per_type[$pubtype][] = $item['id'];
        }
    }
    $result->Close();

    if (!empty($required['cids']) && count($publications) > 0) {
        // Get all the categories at once
        $ids = array();
        foreach ($publications as $article) {
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
                               // Note : we don't need to specify the item type here for publications, since we use unique ids anyway
                                   'modid' => $sysid));

        // Inserting the corresponding Category ID in the Publication Description
        $delete = array();
        $cachesec = array();
        foreach ($publications as $key => $article) {
            if (isset($cids[$article['id']]) && count($cids[$article['id']]) > 0) {
                $publications[$key]['cids'] = $cids[$article['id']];
                foreach ($cids[$article['id']] as $cid) {
                    if (!xarSecurityCheck('ViewPublications',0,'Publication',"$article[pubtype_id]:$cid:$article[owner]:$article[id]")) {
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
                if (!xarSecurityCheck('ViewPublications',0,'Publication',"$article[pubtype_id]:All:$article[owner]:$article[id]")) {
                    $delete[$key] = 1;
                    continue;
                }
            }
        }
        if (count($delete) > 0) {
            foreach ($delete as $key => $val) {
                unset($publications[$key]);
            }
        }
    }

    if (!empty($required['dynamicdata']) && count($publications) > 0) {
        foreach ($itemids_per_type as $pubtype => $itemids) {
            if (!xarModIsHooked('dynamicdata','publications',$pubtype)) {
                continue;
            }
            list($properties,$items) = xarModAPIFunc('dynamicdata','user','getitemsforview',
                                                     array('module'   => 'publications',
                                                           'itemtype' => $pubtype,
                                                           'itemids'  => $itemids,
                                                           // ignore the display-only properties
                                                           'state'   => 1));

            if (empty($properties) || count($properties) == 0) continue;
            foreach ($publications as $key => $article) {

                // otherwise publications (of different pub types) with dd properties having the same
                // names reset previously set values to empty string for each iteration based on the pubtype
                if ($article['pubtype_id'] != $pubtype) continue;

                foreach (array_keys($properties) as $name) {
                    if (isset($items[$article['id']]) && isset($items[$article['id']][$name])) {
                        $value = $items[$article['id']][$name];
                    } else {
                        $value = $properties[$name]->default;
                    }

                    $publications[$key][$name] = $value;

                    // TODO: clean up this temporary fix
                    if (!empty($value)) {
                        $publications[$key][$name.'_output'] = $properties[$name]->showOutput(array('value' => $value));
                    }
                }
            }
        }
    }

    return $publications;
}

?>