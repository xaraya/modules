<?php

/**
 * return the path for a short URL to xarModURL for this module
 * @param $args the function and arguments passed to xarModURL
 * @returns string
 * @return path to be added to index.php for a short URL, or empty if failed
 */
function articles_userapi_encode_shorturl($args)
{
    // Get arguments from argument array
    extract($args);

    // check if we have something to work with
    if (!isset($func)) {
        return;
    }
    
    // Get the article settings for this publication type
    if (!empty($ptid)) {
        $settings = unserialize(xarModGetVar('articles', 'settings.'.$ptid));
    } else {
        $string = xarModGetVar('articles', 'settings');
        if (!empty($string)) {
            $settings = unserialize($string);
        }
    }
    
    // check if we want to encode URLs using their titles rather then their ID
    $encodeUsingTitle= (!isset($settings['usetitleforurl']) || empty($settings['usetitleforurl'])) ? false : true;
    

    // Coming from categories etc.
    if (!empty($objectid)) {
        $aid = $objectid;
    }
    if (!empty($itemtype)) {
        $ptid = $itemtype;
    }
    if (empty($catid) && !empty($cids) && count($cids) > 0) {
        if (!empty($andcids)) {
            $catid = join('+',$cids);
        } else {
            $catid = join('-',$cids);
        }
    }

    // make sure you don't pass the following variables as arguments too

    // default path is empty -> no short URL
    $path = '';
    // if we want to add some common arguments as URL parameters below
    $join = '?';
    // we can't rely on xarModGetName() here (yet) !
    $module = 'articles';

    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    // specify some short URLs relevant to your module
    if ($func == 'main') {
        $alias = xarModGetAlias('frontpage');
        if ($module == $alias) {
            // OK, we can use a 'fake' module name here
            $path = '/frontpage/';
        } else {
            $path = '/' . $module . '/';
        }
    } elseif ($func == 'view') {
// TODO: review logic of possible combinations
        if (isset($authorid) && $authorid > 0) {
            $path = '/' . $module . '/' . xarML('by_author') . '/'
                    . $authorid;
        }
        if (isset($ptid) && isset($pubtypes[$ptid])) {
            $alias = xarModGetAlias($pubtypes[$ptid]['name']);
            if ($module == $alias) {
                // OK, we can use a 'fake' module name here
                $path = '/' . $pubtypes[$ptid]['name'] . '/';
            } else {
                $path = '/' . $module . '/' . $pubtypes[$ptid]['name'] . '/';
            }
        } else {
            $alias = xarModGetAlias('frontpage');
            if ($module == $alias) {
                // OK, we can use a 'fake' module name here
                $path = '/frontpage/';
            } else {
                $path = '/' . $module . '/';
            }
        }
        if (!empty($catid)) {
            if (isset($ptid) && isset($pubtypes[$ptid])) {
                if (isset($bycat)) {
                    $path = '/' . $module . '/c' . $catid
                            . '/' . $pubtypes[$ptid]['name'] . '/';
                } else {
                    $alias = xarModGetAlias($pubtypes[$ptid]['name']);
                    if ($module == $alias) {
                        // OK, we can use a 'fake' module name here
                        $path = '/' . $pubtypes[$ptid]['name']
                                . '/c' . $catid . '/';
                    } else {
                        $path = '/' . $module . '/' . $pubtypes[$ptid]['name']
                                . '/c' . $catid . '/';
                    }
                }
            } else {
                $path = '/' . $module . '/c' . $catid . '/';
            }
/*
// perhaps someday, with a convertor to 7-bit ASCII or something...
            // use a cache to avoid re-querying for each URL in the same cat
            static $catcache = array();
            $cid = $cids[0];
            if (xarModAPILoad('categories','user')) {
                if (isset($catcache[$cid])) {
                    $cat = $catcache[$cid];
                } else {
                    $cat = xarModAPIFunc('categories','user','getcatinfo',
                                        array('cid' => $cid));
                    // put the category in cache
                    $catcache[$cid] = $cat;
                }
                if (!empty($cat) && !empty($cat['name'])) {
                    // use the category name as part of the path here
                    $name = preg_replace('/\s+/','_',$cat['name']);
                    $name = strtolower($name);
                    $path = '/' . $module . '/c/' . rawurlencode($name) .'/';
                }
            }
*/
        } elseif (empty($path)) {
            $path = '/' . $module . '/';
        }
    } elseif ($func == 'display' && isset($aid)) {
        if (isset($ptid) && isset($pubtypes[$ptid])) 
        {
        
            $alias = xarModGetAlias($pubtypes[$ptid]['name']);
            if ($module == $alias) {
                // OK, we can use a 'fake' module name here
                $path = '/' . $pubtypes[$ptid]['name'] . "/";
            } else {
                $path = '/' . $module . '/' . $pubtypes[$ptid]['name'] . "/";
            }
            
            // Check to see if we want to encode using Title
            if( $encodeUsingTitle )
            {
                $path .= encodeUsingTitle($aid);
            } else {
                $path .= $aid;
            }
            
        } else {
        
            $path = '/' . $module . "/";
            // Check to see if we want to encode using Title
            if( $encodeUsingTitle )
            {
                $path .= encodeUsingTitle($aid);
            } else {
                $path .= "$aid";
            }
        }
        // TODO: do we want to include categories in the display URL too someday ?
    } elseif ($func == 'redirect' && isset($aid)) {
        if (isset($ptid) && isset($pubtypes[$ptid])) {
            $alias = xarModGetAlias($pubtypes[$ptid]['name']);
            if ($module == $alias) {
                // OK, we can use a 'fake' module name here
                $path = '/' . $pubtypes[$ptid]['name'] . "/redirect/$aid";
            } else {
                $path = '/' . $module . '/' . $pubtypes[$ptid]['name'] . "/redirect/$aid";
            }
        } else {
            $path = '/' . $module . "/redirect/$aid";
        }
    } elseif ($func == 'archive') {
        if (isset($ptid) && isset($pubtypes[$ptid])) {
            $alias = xarModGetAlias($pubtypes[$ptid]['name']);
            if ($module == $alias) {
                // OK, we can use a 'fake' module name here
                $module = $pubtypes[$ptid]['name'];
            } else {
                $module .= '/' . $pubtypes[$ptid]['name'];
            }
        }
        if (!empty($month)) {
            if ($month == 'all') {
                $path = '/' . $module . '/archive/' . $month .'/';
            } else {
                list($year,$mon) = split('-',$month);
                $path = '/' . $module . '/archive/' . $year .'/'. $mon . '/';
            }
        } else {
            $path = '/' . $module . '/archive/';
        }
    } elseif ($func == 'viewmap') {
        if (isset($ptid) && isset($pubtypes[$ptid])) {
            $alias = xarModGetAlias($pubtypes[$ptid]['name']);
            if ($module == $alias) {
                // OK, we can use a 'fake' module name here
                $module = $pubtypes[$ptid]['name'];
            } else {
                $module .= '/' . $pubtypes[$ptid]['name'];
            }
        }
        $path = '/' . $module . '/map/';
    } elseif ($func == 'search') {
        if (isset($ptid) && isset($pubtypes[$ptid])) {
            $alias = xarModGetAlias($pubtypes[$ptid]['name']);
            if ($module == $alias) {
                // OK, we can use a 'fake' module name here
                $module = $pubtypes[$ptid]['name'];
            } else {
                $module .= '/' . $pubtypes[$ptid]['name'];
            }
        }
        $path = '/' . $module . '/search';
        if (!empty($catid)) {
            $path .= '/c' . $catid;
        }
    }
    // anything else does not have a short URL equivalent

// TODO: add *any* extra args we didn't use yet here
    // add some other module arguments as standard URL parameters
    if (!empty($path)) {
        // search
        if (isset($q)) {
            $path .= $join . 'q=' . $q;
            $join = '&';
        }
        // by author
        if (isset($author)) {
            $path .= $join . 'author=' . $author;
            $join = '&';
        }
        // by startdate
        if (isset($start)) {
            $path .= $join . 'start=' . $start;
            $join = '&';
        }
        // by enddate
        if (isset($end)) {
            $path .= $join . 'end=' . $end;
            $join = '&';
        }
        // by status
        if (isset($status)) {
            $path .= $join . 'status=' . $status;
            $join = '&';
        }
        // field list
        if (isset($fields)) {
            foreach ($fields as $field => $val) {
                $path .= $join . 'fields[' . $field . ']=' . $val;
                $join = '&';
            }
        }
        // search type
        if (isset($searchtype)) {
            $path .= $join . 'searchtype=' . $searchtype;
            $join = '&';
        }
        // sort
        if (isset($sort)) {
            $path .= $join . 'sort=' . $sort;
            $join = '&';
        }
        // pager
        if (isset($startnum) && $startnum != 1) {
            $path .= $join . 'startnum=' . $startnum;
            $join = '&';
        }
        // multi-page articles
        if (isset($page)) {
            $path .= $join . 'page=' . $page;
            $join = '&';
        }
        // number of columns
        if (isset($numcols)) {
            $path .= $join . 'numcols=' . $numcols;
            $join = '&';
        }
        // view map by ...
        if (isset($by)) {
            $path .= $join . 'by=' . $by;
            $join = '&';
        }
    }

    return $path;
}

function encodeUsingTitle( $aid )
{
    $searchArgs['aid'] = $aid;
    $article = xarModAPIFunc('articles','user','get', $searchArgs);
    // TODO: Check to see if there are more then one articles with this title
    // if there are more then one article with the same title, fall back on something
    // else -- like using $aid

    $dupeResolutionMethod = 'Append Date';

    $searchArgs = array();
    $searchArgs['where'] = "title = '".$article['title']."'";
    $articles = xarModAPIFunc('articles', 'user', 'getall', $searchArgs);
    
    if( count($articles) == 1 )
    {
        $path = rawurlencode($article['title']);
    } else {    
        switch( $dupeResolutionMethod )
        {
            case 'Append AID':
                // User Title and AID
                $path = rawurlencode($article['title']).'/'.$aid;
                break;
                
            case 'Append Date':
                // User Title and Date
                
                $path = rawurlencode( $article['title'] ) .'/'.date('Y-m-d H:i',$article['pubdate']) ;
                break;
                
            default:
                // Just use ID instead of title
                $path = $aid;
        }
    }
        
    return $path;
}
?>
