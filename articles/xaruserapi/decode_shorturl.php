<?php

/**
 * extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 * @param $params array containing the elements of PATH_INFO
 * @returns array
 * @return array containing func the function to be called and args the query
 *         string arguments, or empty if it failed
 */
function articles_userapi_decode_shorturl($params)
{
    $args = array();

    $module = 'articles';

    // check if we want to try decoding URLs using titles rather then ID
    // TODO: get this from a modvar or something
    $decodeUsingTitle = false;

    // Check if we're dealing with an alias here
    if ($params[0] != $module) {
        $alias = xarModGetAlias($params[0]);
        if ($module == $alias) {
            // yup, looks like it
            $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
            foreach ($pubtypes as $id => $pubtype) {
                if ($params[0] == $pubtype['name']) {
                    $args['ptid'] = $id;
                    break;
                }
            }
        }
    }

    if (empty($params[1])) {
        return array('view', $args);

    } elseif (preg_match('/^index/i',$params[1])) {
        return array('main', $args);

    } elseif (preg_match('/^map/i',$params[1])) {
        return array('viewmap', $args);

    } elseif (preg_match('/^search/i',$params[1])) {
        if (!empty($params[2]) && preg_match('/^c([0-9 +-]+)/',$params[2],$matches)) {
            $catid = $matches[1];
            $args['catid'] = $catid;
        }
        return array('search', $args);

    } elseif (preg_match('/^(\d+)/',$params[1],$matches)) {
        $aid = $matches[1];
        $args['aid'] = $aid;
        return array('display', $args);

    } elseif ($params[1] == 'archive') {
        if (!empty($params[2]) && preg_match('/^(\d{4}|all)/',$params[2],$matches)) {
            $args['month'] = $matches[1];
            if ($args['month'] != 'all' && !empty($params[3]) && is_numeric($params[3])) {
                $args['month'] .= '-' . $params[3];
            }
        }
        return array('archive', $args);

    } elseif ($params[1] == xarML('by_author')) {
        if (!empty($params[2]) && preg_match('/^(\d+)/',$params[2],$matches)) {
            $args['authorid'] = $matches[1];
            return array('view', $args);
        }

    } elseif ($params[1] == 'redirect') {
        if (!empty($params[2]) && preg_match('/^(\d+)/',$params[2],$matches)) {
            $args['aid'] = $matches[1];
            return array('redirect', $args);
        }

    } elseif (preg_match('/^c(_?[0-9 +-]+)/',$params[1],$matches)) {
        $catid = $matches[1];
        $args['catid'] = $catid;
        if (!empty($params[2])) {
            $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
            foreach ($pubtypes as $id => $pubtype) {
                if ($params[1] == $pubtype['name']) {
                    $args['ptid'] = $id;
                    $args['bycat'] = 1;
                    break;
                }
            }
        }
        return array('view', $args);

    } elseif ($params[1] == 'c') {
        // perhaps someday...

    } else {

        $pubtypes = xarModAPIFunc('articles','user','getpubtypes');
        foreach ($pubtypes as $id => $pubtype) {
            if ($params[1] == $pubtype['name']) {
                $args['ptid'] = $id;
                if (!empty($params[2])) {
                    if (preg_match('/^(\d+)/',$params[2],$matches)) {
                        $aid = $matches[1];
                        $args['aid'] = $aid;
                        return array('display', $args);
                    } elseif (preg_match('/^c(_?[0-9 +-]+)/',$params[2],$matches)) {
                        $catid = $matches[1];
                        $args['catid'] = $catid;
                        return array('view', $args);
                    } elseif (preg_match('/^map/i',$params[2])) {
                        return array('viewmap', $args);
                    } elseif (preg_match('/^search/i',$params[2])) {
                        return array('search', $args);
                    } elseif ($params[2] == 'archive') {
                        if (!empty($params[3]) && preg_match('/^(\d{4}|all)/',$params[3],$matches)) {
                            $args['month'] = $matches[1];
                            if ($args['month'] != 'all' && !empty($params[4]) && is_numeric($params[4])) {
                                $args['month'] .= '-' . $params[4];
                            }
                        }
                        return array('archive', $args);
                    } elseif ($params[2] == 'redirect') {
                        if (!empty($params[3]) && preg_match('/^(\d+)/',$params[3],$matches)) {
                            $args['aid'] = $matches[1];
                            return array('redirect', $args);
                        }
                    } else {

                        // Decode using title
                        if( $decodeUsingTitle )
                        {
                            return decodeUsingTitle( $params, $id );
                        }

                        return array('view', $args);
                    }
                } else {
                    return array('view', $args);
                }
            }
        }
        
        // Decode using title
        if( $decodeUsingTitle )
        {
            return decodeUsingTitle( $params );
        }
    }

    // default : return nothing -> no short URL
    // (e.g. for multiple category selections)
}

function decodeUsingTitle( $params, $ptid = '' )
{
    $dupeResolutionMethod = 'Append AID';

    // The $params passed in does not match on all legal URL characters and
    // so some urls get cut off -- my test cases included parens and commans "this(here)" and "that,+there"
    // So lets parse the path info manually here.
    //
    // TODO: fix this so that it still allows theme overides, ie &theme=print
    // TODO: fix xarServer.php, line 421 to properly deal with this 
    // xarServer.php[421] :: preg_match_all('|/([a-z0-9_ .+-]+)|i', $path, $matches);
    
    $pathInfo = xarServerGetVar('PATH_INFO');
    preg_match_all('|/([^/]+)|i', $pathInfo, $matches);
    $params = $matches[1];                        

    if( isset($ptid) and !empty($ptid) ) 
    {
        $searchArgs['pubtypeid'] = $ptid;
        $searchArgs['where']     = "title = '".urldecode($params[2])."'";
    } else {
        $searchArgs['where']     = "title = '".urldecode($params[1])."'";
    }
    
    $articles = xarModAPIFunc('articles', 'user', 'getall', $searchArgs);

    if( count($articles) == 1 )
    {
        $theArticle = $articles[0];
    } else {
        // NOTE: We could probably just loop through the various dupe detection methods rather then 
        // pulling from a config variable.  This would allow old URLs encoded using one system
        // to keep working even if the configuration changes.
        switch( $dupeResolutionMethod )
        {
            case 'Append AID':
                // Look for AID appended after title
                if( !empty($params[3]) )
                {
                    foreach ($articles as $article)
                    {
                        if( $article['aid'] == $params[3] )
                        {
                            $theArticle = $article;
                            break;
                        }
                    }
                }
                break;
                
            case 'Append Date':
                // Look for date appended after title
                if( !empty($params[3]) )
                {
                    foreach ($articles as $article)
                    {
                        if( date('Y-m-d H:i',$article['pubdate']) == $params[3] )
                        {
                            $theArticle = $article;
                            break;
                        }
                    }
                }
                break;
                
            default:
                // Just use the first one that came back
                $path = $aid;
                $theArticle = $articles[0];
        }
    }

    if( !empty($theArticle) )
    {
        $aid = $theArticle['aid'];
        $args['aid'] = $aid;
        return array('display', $args);
    }
}

?>
