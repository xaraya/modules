<?php

/**
 * view articles
 *
 * catid=1   : category 1        == cids[0]=1
 * catid=1-2 : category 1 OR 2   == cids[0]=1&cids[1]=2
 * catid=1+2 : category 1 AND 2  == cids[0]=1&cids[1]=2&andcids=1
 *
 */
function articles_user_view($args)
{
    // Get parameters
    if(!xarVarFetch('startnum', 'isset', $startnum,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('cids',     'isset', $cids,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('andcids',  'isset', $andcids,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('catid',    'isset', $catid,     NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('ptid',     'isset', $ptid,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemtype', 'isset', $itemtype,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('sort',     'isset', $sort,      NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('numcols',  'isset', $numcols,   NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('authorid', 'isset', $authorid,  NULL, XARVAR_DONT_SET)) {return;}
// This may not be set via user input, only e.g. via template tags, API calls, blocks etc.
//    if(!xarVarFetch('startdate','isset', $startdate, NULL, XARVAR_DONT_SET)) {return;}
//    if(!xarVarFetch('enddate',  'isset', $enddate,   NULL, XARVAR_DONT_SET)) {return;}
//    if(!xarVarFetch('where',    'isset', $where,     NULL, XARVAR_DONT_SET)) {return;}

    // Override if needed from argument array (e.g. ptid, numitems etc.)
    extract($args);

    // Default parameters
    if (!isset($startnum)) {
        $startnum = 1;
    }

    if (!isset($ptid) && !empty($itemtype) && is_numeric($itemtype)) {
        $ptid = $itemtype;
    }

    // Check if we want the default 'front page'
    if (!isset($catid) && !isset($cids) && empty($ptid) && !isset($authorid)) {
        $ishome = 1;
        // default publication type
        $ptid = xarModGetVar('articles','defaultpubtype');
        // frontpage status
        $status = array(3);
    } else {
        $ishome = 0;
        // frontpage or approved status
        $status = array(3,2);
    }

    if (!isset($authorid)) {
        $authorid = null;
    }
    if (!isset($sort)) {
        $sort = null;
    }

    $isdefault = 0;
    if (!empty($ptid)) {
        $settings = unserialize(xarModGetVar('articles', 'settings.'.$ptid));
        // check default view for this type of articles
        if (empty($catid) && empty($cids) && empty($authorid) && empty($sort)) {
            if (substr($settings['defaultview'],0,1) == 'c') {
                $catid = substr($settings['defaultview'],1);
            }
        }
    // Note: 'sort' is used to override the default start view too
        if (substr($settings['defaultview'],0,1) == 'c') {
            if (!isset($sort)) {
                $sort = 'date';
            }
            $isdefault = 1;
        }
    } else {
        $string = xarModGetVar('articles', 'settings');
        if (!empty($string)) {
            $settings = unserialize($string);
        } else {
            $settings = array();
        }
    }

    if (!isset($showcategories)) {
        if (empty($settings['showcategories'])) {
            $showcategories = 0;
        } else {
            $showcategories = 1;
        }
    }
    if (!isset($showprevnext)) {
        if (empty($settings['showprevnext'])) {
            $showprevnext = 0;
        } else {
            $showprevnext = 1;
        }
    }
//    if (empty($settings['shownavigation'])) {
//        $shownavigation = 0;
//    } else {
//        $shownavigation = 1;
//    }
    if (!isset($showcomments)) {
        if (empty($settings['showcomments'])) {
            $showcomments = 0;
        } else {
            $showcomments = 1;
        }
    }
    if (!isset($showhitcounts)) {
        if (empty($settings['showhitcounts'])) {
            $showhitcounts = 0;
        } else {
            $showhitcounts = 1;
        }
    }
    if (!isset($showratings)) {
        if (empty($settings['showratings'])) {
            $showratings = 0;
        } else {
            $showratings = 1;
        }
    }
    if (!isset($showarchives)) {
        if (empty($settings['showarchives'])) {
            $showarchives = 0;
        } else {
            $showarchives = 1;
        }
    }
    if (!isset($showmap)) {
        if (empty($settings['showmap'])) {
            $showmap = 0;
        } else {
            $showmap = 1;
        }
    }
    if (!isset($showpublinks)) {
        if (empty($settings['showpublinks'])) {
            $showpublinks = 0;
        } else {
            $showpublinks = 1;
        }
    }
    if (!isset($dotransform)) {
        if (empty($settings['dotransform'])) {
            $dotransform = 0;
        } else {
            $dotransform = 1;
        }
    }
    // Page template for frontpage or depending on publication type (optional)
    // Note : this cannot be overridden in templates
    if (!empty($settings['page_template'])) {
        xarTplSetPageTemplateName($settings['page_template']);
    }

// TODO: make configurable
    // show the number of articles for each publication type
    $showpubcount = 1;
    $showcatcount = 0;

    // support multi-column output
    if (!isset($numcols) || !is_numeric($numcols)) {
        if (empty($settings['number_of_columns'])) {
            // default is no multi-column output
            $numcols = 0;
        } else {
            $numcols = $settings['number_of_columns'];
        }
    }
    if ($numcols == 1) {
        $numcols = 0;
    }

    // Load APIs
    if (!xarModAPILoad('articles', 'user')) return;
    if (!xarModAPILoad('categories', 'user')) return;
    // allow articles to work without comments being activated
    if ($showcomments && !xarModIsHooked('comments','articles',$ptid)) {
        $showcomments = 0;
    }
    // allow articles to work without hitcounts being activated
    if ($showhitcounts && !xarModIsHooked('hitcount','articles',$ptid)) {
        $showhitcounts = 0;
    }
    // allow articles to work without ratings being activated
    if ($showratings && !xarModIsHooked('ratings','articles',$ptid)) {
        $showratings = 0;
    }

    $data = array();

// TODO: show this *after* category list when we start from categories :)
    // Navigation links
    $data['publabel'] = xarML('Publication');
    $data['publinks'] = xarModAPIFunc('articles','user','getpublinks',
                                     array('ptid' => $ishome ? '' : $ptid,
                                           'status' => array(3,2),
                                           'count' => $showpubcount));
    if ($showmap) {
        $data['maplabel'] = xarML('View Article Map');
        $data['maplink'] = xarModURL('articles','user','viewmap',
                                    array('ptid' => !empty($ptid) ? $ptid : null));
    }
    if ($showarchives) {
        $data['archivelabel'] = xarML('View Archives');
        $data['archivelink'] = xarModURL('articles','user','archive',
                                        array('ptid' => !empty($ptid) ? $ptid : null));
    }

    $data['pager'] = '';

    // Get the users requested number of stories per page.
    // If user doesn't care, use the site default
    if (xarUserIsLoggedIn())
    {
    // TODO: figure how to let users specify their settings
        //$numitems = xarModUserGetVar('itemsperpage');
    }
    if (empty($numitems)) {
        if (!empty($settings['itemsperpage'])) {
            $numitems = $settings['itemsperpage'];
        } else {
            $numitems = 20;
        }
    }

    // turn $catid into $cids array and set $andcids flag
    if (!empty($catid)) {
        if (strpos($catid,' ')) {
            $cids = explode(' ',$catid);
            $andcids = true;
        } elseif (strpos($catid,'+')) {
            $cids = explode('+',$catid);
            $andcids = true;
        } elseif (strpos($catid,'-')) {
            $cids = explode('-',$catid);
            $andcids = false;
        } else {
            $cids = array($catid);
            if (strstr($catid,'_')) {
                $andcids = false; // don't combine with current category
            } else {
                $andcids = true;
            }
        }
    } else {
        if (empty($cids)) {
            $cids = array();
        }
        if (empty($andcids)) {
            $andcids = true;
        }
    }
    // rebuild $catid in standard format again
    $catid = null;
    if (count($cids) > 0) {
        sort($cids,SORT_NUMERIC);
        if ($andcids) {
            $catid = join('+',$cids);
        } else {
            $catid = join('-',$cids);
        }
    }

    // add a hit for the categories we're viewing here
// TODO: move off to categories
    if (count($cids) > 0 && xarModIsHooked('hitcount','categories')) {
        foreach ($cids as $cid) {
            if (empty($cid)) {
                continue;
            }
            // FIXME: if this fails, an exception will be set, so it needs to be cleared?
            xarModAPIFunc('hitcount','admin','update',
                         array('modname' => 'categories',
                               'objectid' => $cid));
        }
    }

    // every field you always wanted to know about but were afraid to ask for :)
    $extra = array();
    $extra[] = 'author';
    if ($showcategories) {
        $extra[] = 'cids';
    }
    if ($showhitcounts) {
        $extra[] = 'counter';
    }
    if ($showratings) {
        $extra[] = 'rating';
    }
    if (xarModIsHooked('dynamicdata','articles',$ptid)) {
        $extra[] = 'dynamicdata';
    }

    $now = time();
    if (empty($startdate) || !is_numeric($startdate) || $startdate > $now) {
        $startdate = null;
    }
    if (empty($enddate) || !is_numeric($enddate) || $enddate > $now) {
        $enddate = $now;
    }
    if (empty($where)) {
        $where = null;
    }

    // Get articles
    $articles = xarModAPIFunc('articles',
                             'user',
                             'getall',
                             array('startnum' => $startnum,
                                   'cids' => $cids,
                                   'andcids' => $andcids,
                                   'ptid' => (isset($ptid) ? $ptid : null),
                                   'authorid' => $authorid,
                                   'status' => $status,
                                   'sort' => $sort,
                                   'extra' => $extra,
                                   'where' => $where,
                                   'numitems' => $numitems,
                                   'startdate' => $startdate,
                                   'enddate' => $enddate));

    if (!is_array($articles)) {
        // Error getting articles
        if (xarCurrentErrorType() == XAR_SYSTEM_EXCEPTION) {
             return; // throw back
        } elseif (xarCurrentErrorType() == XAR_USER_EXCEPTION) {
            // get back the reason in string format
            $reason = xarExceptionValue();
            if (!empty($reason)) {
                $reason = ' - ' . xarML('Reason') . ' : ' . $reason->toString();
            }
        }
        $data['output'] = xarML('Failed to retrieve articles') . $reason;
        return $data;
    }

// TODO : support different 'index' templates for different types of articles
//        (e.g. News, Sections, ...), depending on what "view" the user
//        selected (per category, per publication type, a combination, ...) ?

    $catinfo = array();
    if ($showcategories) {
        // get root categories for this publication type
        $catlinks = xarModAPIFunc('articles',
                                  'user',
                                  'getrootcats',
                                  array('ptid' => $ptid));
        // grab the name and link of all children too
        foreach ($catlinks as $info) {
            $cattree = xarModAPIFunc('articles',
                                     'user',
                                     'getchildcats',
                                     array('cid' => $info['catid'],
                                           'ptid' => $ptid,
                                           // filter on the currently selected categories
                                           'filter' => $andcids ? $catid : '',
                                           // we don't want counts here
                                           'count' => false));
            foreach ($cattree as $catitem) {
                $catinfo[$catitem['id']] = array('name' => $catitem['name'],
                                                 'link' => $catitem['link'],
                                                 'image'=> $catitem['image'],
                                                 'root' => $info['catid']);
            }
        }
        unset($cattree);
        unset($catlinks);
    }

    if (!empty($authorid)) {
        $data['author'] = xarUserGetVar('name', $authorid);
        if (empty($data['author'])) {
            xarExceptionHandled();
            $data['author'] = xarML('Unknown');
        }
    }

    // Save some variables to (temporary) cache for use in blocks etc.
    xarVarSetCached('Blocks.articles','ptid',$ptid);
    xarVarSetCached('Blocks.articles','cids',$cids);
    xarVarSetCached('Blocks.articles','authorid',$authorid);
    if (isset($data['author'])) {
        xarVarSetCached('Blocks.articles','author',$data['author']);
    }

    // Get publication types
    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

// TODO: add this to articles configuration ?
//if ($shownavigation) {
    if ($ishome) {
        $data['ptid'] = null;
        if (xarSecurityCheck('SubmitArticles',0)) {
            $data['submitlink'] = xarModURL('articles','admin','new');
        }
    } else {
        $data['ptid'] = $ptid;
        if (!empty($ptid)) {
            $curptid = $ptid;
        } else {
            $curptid = 'All';
        }
        if (count($cids) > 0) {
            foreach ($cids as $cid) {
                if (xarSecurityCheck('SubmitArticles',0,'Article',"$curptid:$cid:All:All")) {
                    $data['submitlink'] = xarModURL('articles','admin','new',array('ptid' => $ptid, 'catid' => $catid));
                    break;
                }
            }
        } elseif (xarSecurityCheck('SubmitArticles',0,'Article',"$curptid:All:All:All")) {
            $data['submitlink'] = xarModURL('articles','admin','new',array('ptid' => $ptid));
        }
    }
    $data['cids'] = $cids;
    $data['catid'] = $catid;
    xarVarSetCached('Blocks.categories','module','articles');
    xarVarSetCached('Blocks.categories','itemtype',$ptid);
    xarVarSetCached('Blocks.categories','cids',$cids);
    if (!empty($ptid) && !empty($pubtypes[$ptid]['descr'])) {
        xarVarSetCached('Blocks.categories','title',$pubtypes[$ptid]['descr']);
        // Note : this gets overriden by the categories navigation if necessary
        xarTplSetPageTitle(xarVarPrepForDisplay($pubtypes[$ptid]['descr']));
    }

    // optional category count
    if ($showcatcount) {
        if (!empty($ptid)) {
            $pubcatcount = xarModAPIFunc('articles',
                                        'user',
                                        'getpubcatcount',
                                        // frontpage or approved
                                        array('status' => array(3,2),
                                              'ptid' => $ptid));
            if (isset($pubcatcount[$ptid])) {
                xarVarSetCached('Blocks.categories','catcount',$pubcatcount[$ptid]);
            }
            unset($pubcatcount);
        } else {
            $pubcatcount = xarModAPIFunc('articles',
                                        'user',
                                        'getpubcatcount',
                                        // frontpage or approved
                                        array('status' => array(3,2),
                                              'reverse' => 1));
            if (isset($pubcatcount) && count($pubcatcount) > 0) {
                $catcount = array();
                foreach ($pubcatcount as $cat => $count) {
                    $catcount[$cat] = $count['total'];
                }
                xarVarSetCached('Blocks.categories','catcount',$catcount);
            }
            unset($pubcatcount);
        }
    } else {
    //    xarVarSetCached('Blocks.categories','catcount',array());
    }
//}
    $data['showpublinks'] = $showpublinks;
    $data['showprevnext'] = $showprevnext;

    if (empty($articles)) {
        // No articles
        $data['output'] = '';
        if ($ishome) {
            $template = 'frontpage';
        } elseif (!empty($ptid)) {
            $template = $pubtypes[$ptid]['name'];
        } else {
    // TODO: allow templates per category ?
            $template = null;
        }
        return xarTplModule('articles', 'user', 'view', $data, $template);
    }

    // retrieve the number of comments for each article
    if ($showcomments) {
        $aidlist = array();
        foreach ($articles as $article) {
            $aidlist[] = $article['aid'];
        }
        $numcomments = xarModAPIFunc('comments',
                                     'user',
                                     'get_countlist',
                            array('modid' => xarModGetIDFromName('articles'),
                                  'objectids' => $aidlist));
    }

    $data['titles'] = array();

    // test 2-column output on frontpage
    $columns = array();
    $data['numcols'] = $numcols;

    $number = 0;
    foreach ($articles as $article)
    {
    // TODO: don't include ptid and catid if we don't use short URLs
        // link to article
        $article['link'] = xarModURL('articles', 'user', 'display',
                                    array(// don't include pubtype id if we're navigating by category
                                          'ptid' => empty($ptid) ? null : $article['pubtypeid'],
                                          'catid' => $catid,
                                          'aid' => $article['aid']));

        // N words/bytes more in article
        if (!empty($article['body'])) {
            // note : this is only an approximate number
            $wordcount = count(preg_split("/\s+/", strip_tags($article['body']), -1, PREG_SPLIT_NO_EMPTY));
            $article['words'] = $wordcount;

            // byte-count is less CPU-intensive -> make configurable ?
            $article['bytes'] = strlen($article['body']);
        } else {
            $article['words'] = 0;
            $article['bytes'] = 0;
        }

        // current publication type
        $curptid = $article['pubtypeid'];

    // TODO: make time display user/config dependent
        // publication date of article (if needed)
        foreach ($pubtypes[$curptid]['config'] as $field => $value) {
            if (empty($value['label'])) {
                continue;
            }
            switch ($value['format']) {
                case 'calendar':
                    if (!empty($article[$field])) {
                        if($field!='pubdate') {
                            // only convert this timestamp if it's NOT the pubdate
                            // we want the pubdate field to remain a UTC Unix TimeStamp
                            $article[$field] = trim(xarLocaleFormatDate("%a, %d %b %Y %H:%M:%S %Z",($article[$field])));
                        }
                    } else {
                        $article[$field] = '';
                    }
                    
                // TODO: replace by  and sync with templates
                    if ($field == 'pubdate') {
                        // the date for this field is represented in the user's timezone for display
                        $article['date'] = trim(xarLocaleFormatDate("%a, %d %b %Y %H:%M:%S %Z",$article[$field]));
                    }
                    break;
                case 'urltitle':
                    if (!empty($article[$field])) {
                        $array = array('type' => 'urltitle', 'value' => $article[$field]);
                        $article[$field] = xarModAPIFunc('dynamicdata','user','showoutput',$array);
                    }
                    break;
            }
        }

    // TODO: make configurable ?
        $article['redirect'] = xarModURL('articles','user','redirect',
                                        array('ptid' => $curptid,
                                              'aid' => $article['aid']));

    // TODO: put in getall() function to avoid a new query on each article ?
        // number of comments for this article
        if ($showcomments) {
            if (empty($numcomments[$article['aid']])) {
                $article['numcomments'] = 0;
                $article['comments'] = xarML('no comments');
            } elseif ($numcomments[$article['aid']] == 1) {
                $article['numcomments'] = 1;
                $article['comments'] = xarML('1 comment');
            } else {
                $article['numcomments'] = $numcomments[$article['aid']];
                $article['comments'] = xarML('#(1) comments', $numcomments[$article['aid']]);
            }
        } else {
            $article['comments'] = '';
        }

// TODO: improve the case where we have several icons :)
        $article['topic_icons'] = '';
        // categories this article belongs to
        $article['categories'] = array();
        if ($showcategories && !empty($article['cids']) &&
            is_array($article['cids']) && count($article['cids']) > 0) {

            // order cids by root category (to be improved)
            $cidlist = $article['cids'];
            usort($cidlist,'articles_user_sortbyroot');

            $isfirst = 1;
            foreach ($cidlist as $cid) {
                $item = array();
                if (!isset($catinfo[$cid])) {
                    // oops
                    continue;
                } elseif (in_array($cid,$cids) && $andcids) {
                    // we're already selecting on this category -> don't show
                    continue;
                }
                $item['cname'] = $catinfo[$cid]['name'];
                $item['clink'] = $catinfo[$cid]['link'];
                if ($isfirst) {
                    $item['cjoin'] = '';
                    $isfirst = 0;
                } else {
                    $item['cjoin'] = '|';
                }
                $article['categories'][] = $item;
                if (!empty($catinfo[$cid]['image'])) {
                    $image = xarTplGetImage($catinfo[$cid]['image'],'categories');
                    $article['topic_icons'] .= '<a href="'. $catinfo[$cid]['link'] .'">'.
                                            '<img src="'. $image .
                                            '" border="0" alt="'. xarVarPrepForDisplay($catinfo[$cid]['name']) .'" />'.
                                            '</a>';
                }
            }
        }

        // multi-column display (default from left to right, then from top to bottom)
        $article['number'] = $number;
        if (!empty($numcols)) {
            $col = $number % $numcols;
        } else {
            $col = 0;
        }
        if (!isset($columns[$col])) {
            $columns[$col] = array();
        }
        
        /*BIG COMMENT FOR MIKE
            BIG COMMENT FOR MIKE
                BIG COMMENT FOR MIKE
                    I am processing the RSS values for the RSS theme here for the time being, until we have the work around;) -- jc*/

        $article['rsstitle'] = htmlspecialchars($article['title']);
        //$article['rssdate'] = strtotime($article['date']);
        $article['rsssummary'] = preg_replace('<br />',"\n",$article['summary']);
        $article['rsssummary'] = xarVarPrepForDisplay(strip_tags($article['rsssummary']));
        $article['rsscomment'] = xarModURL('comments','user','display',array('modid' => xarModGetIDFromName('articles'),'objectid' => $article['aid']));
        // $article['rsscname'] = htmlspecialchars($item['cname']);
        // <category>#$rsscname#</category>

        /* END BIG COMMENT
            END BIG COMMENT
                END BIG COMMENT */

    // TODO: clean up depending on field format
        $article['title'] = xarVarPrepHTMLDisplay($article['title']);
        $article['summary'] = xarVarPrepHTMLDisplay($article['summary']);
        $article['notes'] = xarVarPrepHTMLDisplay($article['notes']);
        if ($dotransform) {
            $article['itemtype'] = $article['pubtypeid'];
        // TODO: what about transforming DD fields ?
        //    $article['transform'] = array('title','summary','notes');
            $article['transform'] = array('summary','notes');
            $article = xarModCallHooks('item', 'transform', $article['aid'], $article, 'articles');
        }

        $data['titles'][$article['aid']] = $article['title'];

        // fill in the summary template for this article
        $template = $pubtypes[$article['pubtypeid']]['name'];
        $columns[$col][] = xarTplModule('articles', 'user', 'summary', $article, $template);
        $number++;
    }
    
    unset($articles);

    $data['number'] = $number;
    $data['columns'] = $columns;

    if (!empty($numcols) && $number > 0) {
        $maxcols = $number > $numcols ? $numcols : $number;
        $data['colwidth'] = round(100 / $maxcols);
    }
// TODO: verify for other URLs as well
    if ($ishome) {
        if (!empty($numcols) && $numcols > 1) {
            // if we're currently showing more than 1 column
            $data['showcols'] = 1;
        } else {
            $defaultcols = $settings['number_of_columns'];
            if ($defaultcols > 1) {
                // if the default number of columns is more than 1
                $data['showcols'] = $defaultcols;
            }
        }
    }
    $data['output'] = '';

    // Pager
    $data['pager'] = xarTplGetPager($startnum,
                                    xarModAPIFunc('articles', 'user', 'countitems',
                                                  array('cids' => $cids,
                                                        'andcids' => $andcids,
                                                        'ptid' => (isset($ptid) ? $ptid : null),
                                                        'authorid' => $authorid,
                                                        'status' => $status,
                                                        'where' => $where,
                                                        'startdate' => $startdate,
                                                        'enddate' => $enddate)),
                                    xarModURL('articles', 'user', 'view',
                                              array('ptid' => ($ishome ? null : $ptid),
                                                    'catid' => $catid,
                                                    'authorid' => $authorid,
                                                    'sort' => $sort,
                                                    'startnum' => '%%')),
                                    $numitems);

// TODO: sorting on other fields ?
    if (strlen($data['pager']) > 5) {
        $data['pager'] .= '<br /><br />' . xarML('Sort by');
        $sortlist = array();
        $sortlist['date'] = xarML('Date');
        $sortlist['title'] = xarML('Title');
        if ($showhitcounts) {
            $sortlist['hits'] = xarML('Hits');
        }
        if ($showratings) {
            $sortlist['rating'] = xarML('Rating');
        }
        foreach ($sortlist as $sname => $stitle) {
            if (empty($sort) && $sname == 'date') {
                $data['pager'] .= '&nbsp;' . xarML($stitle) . '&nbsp;';
                continue;
            } elseif ($sname == $sort) {
                $data['pager'] .= '&nbsp;' . xarML($stitle) . '&nbsp;';
                continue;
            }
            // Note: 'sort' is used to override the default start view too
            if ($sname == 'date' && !$isdefault) {
                $sortlink = xarModURL('articles','user','view',
                                     array('ptid' => ($ishome ? null : $ptid),
                                           'catid' => $catid));
            } else {
                $sortlink = xarModURL('articles','user','view',
                                     array('ptid' => ($ishome ? null : $ptid),
                                           'catid' => $catid,
                                           'sort' => $sname));
            }
            $data['pager'] .= '&nbsp;<a href="' . $sortlink . '">' .
                              xarML($stitle) . '</a>&nbsp;';
        }
    }

    if ($ishome) {
        $template = 'frontpage';
    } elseif (!empty($ptid)) {
        $template = $pubtypes[$ptid]['name'];
    } else {
// TODO: allow templates per category ?
        $template = null;
    }
    return xarTplModule('articles', 'user', 'view', $data, $template);
}

/**
 * sorting function for article categories
 */

function articles_user_sortbyroot ($a,$b) {
    global $catinfo;
    if ($catinfo[$a]['root'] == $catinfo[$b]['root']) return 0;
    return ($catinfo[$a]['root'] > $catinfo[$b]['root']) ? -1 : 1;
}

?>
