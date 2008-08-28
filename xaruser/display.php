<?php
/**
 * Articles module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Articles Module
 * @link http://xaraya.com/index.php/release/151.html
 * @author mikespub
 */
/**
 * Display article
 *
 * @param int id
 * @param int page
 * @param int ptid The publication Type ID
 * @return array with template information
 */
function articles_user_display($args)
{
    extract ($args);
    // Get parameters from user
    if(!xarVarFetch('id',  'id',    $id,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('page', 'int:1', $page,  NULL, XARVAR_NOT_REQUIRED)) {return;}
// this is used to determine whether we come from a pubtype-based view or a
// categories-based navigation
    if(!xarVarFetch('ptid', 'id',    $ptid,  NULL, XARVAR_NOT_REQUIRED)) {return;}

/*
    // TEST - highlight search terms
    if(!xarVarFetch('q',     'str',  $q,     NULL, XARVAR_NOT_REQUIRED)) {return;}
*/

    // Override if needed from argument array (e.g. preview)
    extract($args);

    // Defaults
    if (!isset($page)) {
        $page = 1;
    }
    // via arguments only
    if (!isset($preview)) {
        $preview = 0;
    }

    if ($preview) {
        if (!isset($article)) {
            return xarML('Invalid article');
        }
        $id = $article['id'];
    } elseif (!isset($id) || !is_numeric($id) || $id < 1) {
        return xarML('Invalid article ID');
    }

    // Load API
    if (!xarModAPILoad('articles', 'user')) return;

    // Get article
    if (!$preview) {
        $article = xarModAPIFunc('articles',
                                'user',
                                'get',
                                array('id' => $id,
                                      'withcids' => true));
    }

    if (!is_array($article)) {
        $msg = xarML('Failed to retrieve article in #(3)_#(1)_#(2).php', 'userapi', 'get', 'articles');
        throw new DataNotFoundException(null, $msg);
    }

    // Get publication types
    $pubtypes = xarModAPIFunc('articles','user','getpubtypes');

    // Check that the publication type is valid, otherwise use the article's pubtype
    if (!empty($ptid) && !isset($pubtypes[$ptid])) {
        $ptid = $article['pubtypeid'];
    }

// keep original ptid (if any)
//    $ptid = $article['pubtypeid'];
    $pubtypeid = $article['pubtypeid'];
    $authorid = $article['authorid'];
    if (!isset($article['cids'])) {
        $article['cids'] = array();
    }
    $cids = $article['cids'];

    // Get the article settings for this publication type
    if (empty($ptid)) {
        $settings = unserialize(xarModVars::get('articles', 'settings'));
    } else {
        $settings = unserialize(xarModVars::get('articles', 'settings.'.$ptid));
    }

    // show the number of articles for each publication type
    if (!isset($showpubcount)) {
        if (!isset($settings['showpubcount']) || !empty($settings['showpubcount'])) {
            $showpubcount = 1; // default yes
        } else {
            $showpubcount = 0;
        }
    }
    // show the number of articles for each category
    if (!isset($showcatcount)) {
        if (empty($settings['showcatcount'])) {
            $showcatcount = 0; // default no
        } else {
            $showcatcount = 1;
        }
    }

    // Initialize the data array
    $data = array();
    $data['ptid'] = $ptid; // navigation pubtype
    $data['pubtypeid'] = $pubtypeid; // article pubtype

    // Security check for EDIT access
    if (!$preview) {
        $input = array();
        $input['article'] = $article;
        $input['mask'] = 'EditArticles';
        if (xarModAPIFunc('articles','user','checksecurity',$input)) {
            $data['editurl'] = xarModURL('articles', 'admin', 'modify',
                                         array('id' => $article['id']));
        // don't show unapproved articles to non-editors
        } elseif ($article['status'] < 2) {
            $status = xarModAPIFunc('articles', 'user', 'getstatusname',
                                    array('status' => $article['status']));
            return xarML('You have no permission to view this item [Status: #(1)]', $status);
        }
    }
    $data['edittitle'] = xarML('Edit');

// TODO: improve the case where we have several icons :)
    $data['topic_icons'] = '';
    $data['topic_images'] = array();
    $data['topic_urls'] = array();
    $data['topic_names'] = array();
    if (count($cids) > 0) {
        if (!xarModAPILoad('categories', 'user')) return;
        $catlist = xarModAPIFunc('categories',
                                'user',
                                'getcatinfo',
                                array('cids' => $cids));
        foreach ($catlist as $cat) {
            $link = xarModURL('articles','user','view',
                             array(//'status' => array(ARTCLES_STATE_FRONTPAGE,ARTCLES_STATE_APPROVED).
                                   'ptid' => $ptid,
                                   'catid' => $cat['cid']));
            $name = xarVarPrepForDisplay($cat['name']);

            $data['topic_urls'][] = $link;
            $data['topic_names'][] = $name;

            if (!empty($cat['image'])) {
                $image = xarTplGetImage($cat['image'],'categories');
                $data['topic_icons'] .= '<a href="'. $link .'">'.
                                        '<img src="'. $image .
                                        '" alt="'. $name .'" />'.
                                        '</a>';
                $data['topic_images'][] = $image;

                break;
            }
        }
    }

    // multi-page output for 'body' field (mostly for sections at the moment)
    $themeName = xarVarGetCached('Themes.name','CurrentTheme');
    if ($themeName != 'print'){
        if (strstr($article['body'],'<!--pagebreak-->')) {
            if ($preview) {
                $article['body'] = preg_replace('/<!--pagebreak-->/',
                                                '<hr/><div style="text-align: center;">'.xarML('Page Break').'</div><hr/>',
                                                $article['body']);
                $data['previous'] = '';
                $data['next'] = '';
            } else {
                $pages = explode('<!--pagebreak-->',$article['body']);

                // For documents with many pages, the pages can be
                // arranged in blocks.
                $pageBlockSize = 10;

                // Get pager information: one item per page.
                $pagerinfo = xarTplPagerInfo((empty($page) ? 1 : $page), count($pages), 1, $pageBlockSize);

                // Retrieve current page and total pages from the pager info.
                // These will have been normalised to ensure they are in range.
                $page = $pagerinfo['currentpage'];
                $numpages = $pagerinfo['totalpages'];

                // Discard everything but the current page.
                $article['body'] = $pages[$page - 1];
                unset($pages);

                if ($page > 1) {
                    // Don't count page hits after the first page.
                    xarVarSetCached('Hooks.hitcount','nocount',1);
                }

                // Pass in the pager info so a complete custom pager
                // can be created in the template if required.
                $data['pagerinfo'] = $pagerinfo;

                // Get the rendered pager.
                // The pager template (last parameter) could be an
                // option for the publication type.
                $urlmask = xarModURL(
                    'articles','user','display',
                    array('ptid' => $ptid, 'id' => $id, 'page' => '%%')
                );
                $data['pager'] = xarTplGetPager(
                    $page, $numpages, $urlmask,
                    1, $pageBlockSize, 'multipage'
                );

                // Next two assignments for legacy templates.
                // TODO: deprecate them?
                $data['next'] = xarTplGetPager(
                    $page, $numpages, $urlmask,
                    1, $pageBlockSize, 'multipagenext'
                );
                $data['previous'] = xarTplGetPager(
                    $page, $numpages, $urlmask,
                    1, $pageBlockSize, 'multipageprev'
                );
            }
        } else {
            $data['previous'] = '';
            $data['next'] = '';
        }
    } else {
        $article['body'] = preg_replace('/<!--pagebreak-->/',
                                        '',
                                        $article['body']);
    }

    // TEST
    if (isset($prevnextart)) {
        $settings['prevnextart'] = $prevnextart;
    }
    if (!empty($settings['prevnextart']) && ($preview == 0)) {
        if(!array_key_exists('defaultsort',$settings)) {
            $settings['defaultsort'] = 'id';
        }
        $prevart = xarModAPIFunc('articles','user','getprevious',
                                 array('id' => $id,
                                       'ptid' => $ptid,
                                       'sort' => $settings['defaultsort'],
                                       'status' => array(ARTCLES_STATE_FRONTPAGE,ARTCLES_STATE_APPROVED),
                                       'enddate' => time()));
        if (!empty($prevart['id'])) {
            //Make all previous article info available to template
            $data['prevartinfo'] = $prevart;

            $data['prevart'] = xarModURL('articles','user','display',
                                         array('ptid' => $prevart['pubtypeid'],
                                               'id' => $prevart['id']));
        } else {
            $data['prevart'] = '';
        }
        $nextart = xarModAPIFunc('articles','user','getnext',
                                 array('id' => $id,
                                       'ptid' => $ptid,
                                       'sort' => $settings['defaultsort'],
                                       'status' => array(ARTCLES_STATE_FRONTPAGE,ARTCLES_STATE_APPROVED),
                                       'enddate' => time()));
        if (!empty($nextart['id'])) {
            //Make all next art info available to template
            $data['nextartinfo'] = $nextart;

            $data['nextart'] = xarModURL('articles','user','display',
                                         array('ptid' => $nextart['pubtypeid'],
                                               'id' => $nextart['id']));
        } else {
            $data['nextart'] = '';
        }
    } else {
        $data['prevart'] = '';
        $data['nextart'] = '';
    }

    // Display article

    // Fill in the fields based on the pubtype configuration
    foreach ($pubtypes[$pubtypeid]['config'] as $field => $value) {
        if (empty($value['label'])) {
            $data[$field] = '';
            continue;
        }
        switch ($value['format']) {
            case 'username':
            case 'userlist':
                $data[$field] = $article[$field];
        // TODO: replace by authorid and sync with templates
                $data['author'] = xarUserGetVar('name', $article[$field]);
                if (!isset($data['author'])) {
                    $data['author'] = '';
                    // clear error retrieving non-existing author
                    xarErrorFree();
                } elseif (empty($data['author'])) {
                    $data['author'] = xarUserGetVar('uname', $article[$field]);
                }
                if ($article[$field] > _XAR_ID_UNREGISTERED) {
                    $data['profile'] = xarModURL('roles','user','display',
                                                array('uid' => $article[$field]));
                }
                break;
            case 'status':
                $data[$field] = $article[$field];
                break;
            case 'calendar':
                // Make sure there is a value date
                if (!empty($article[$field])) {
                    // all calendar fields are passed "as is" now, so you can format them in the templates
                    $data[$field] = $article[$field];
                    // legacy support for $date variable in templates
                    if ($field == 'pubdate') {
                        // the date for this field is represented in the user's timezone for display
                        $data['date'] = xarLocaleFormatDate('%a, %d %B %Y %H:%M:%S %Z', $article[$field]);
                    }
                } else {
                    $data[$field] = '';
                    // legacy support for $date variable in templates
                    if ($field == 'pubdate') {
                        $data['date'] = '';
                    }
                }
                break;
            case 'url':
                $data[$field] = xarVarPrepHTMLDisplay($article[$field]);
                if (!empty($article[$field]) && $article[$field] != 'http://') {
                    $data['redirect'] = xarModURL('articles','user','redirect',
                                                  array('ptid' => $ptid,
                                                        'id' => $id));
                } else {
                    $data['redirect'] = '';
                }
                break;
            case 'urltitle':
                // fall through
        // TEST ONLY
            case 'webpage':
                if (empty($value['validation'])) {
                    $value['validation'] = 'modules/articles';
                }
                // fall through
            case 'imagelist':
                if (empty($value['validation'])) {
                    $value['validation'] = 'modules/articles/xarimages';
                }
                // fall through
            case 'dropdown':
                if (empty($value['validation'])) {
                    $value['validation'] = '';
                }
                if (!empty($article[$field])) {
                    $data[$field] = xarModAPIFunc('dynamicdata','user','showoutput',
                                                  array('name' => $field,
                                                        'type' => $value['format'],
                                                        'configuration' => $value['validation'],
                                                        'value' => $article[$field]));
                } else {
                    $data[$field] = '';
                }
                break;
            default:
                $data[$field] = xarVarPrepHTMLDisplay($article[$field]);
                //$data[$field] = $article[$field];
        }
    }
    unset($article);

    if (xarModIsHooked('uploads', 'articles', $pubtypeid)) {
        xarVarSetCached('Hooks.uploads','ishooked',1);
    }
    // temp. fix to include dynamic data fields without changing templates
    if (xarModIsHooked('dynamicdata','articles',$pubtypeid)) {
        list($properties) = xarModAPIFunc('dynamicdata','user','getitemfordisplay',
                                          array('module'   => 'articles',
                                                'itemtype' => $pubtypeid,
                                                'itemid'   => $id,
                                                'preview'  => $preview));
        if (!empty($properties) && count($properties) > 0) {
            foreach (array_keys($properties) as $field) {
                $data[$field] = $properties[$field]->getValue();
                // POOR mans flagging for transform hooks
                try {
                $configuration = $properties[$field]->configuration;
                if(substr($configuration,0,10) == 'transform:') {
                    $data['transform'][] = $field;
                }
                } catch (Exception $e) {}
                // TODO: clean up this temporary fix
                $data[$field.'_output'] = $properties[$field]->showOutput();
            }
        }
    }

    // Let any transformation hooks know that we want to transform some text.
    // You'll need to specify the item id, and an array containing all the
    // pieces of text that you want to transform (e.g. for autolinks, wiki,
    // smilies, bbcode, ...).
    $data['itemtype'] = $pubtypeid;
    // TODO: what about transforming DDfields ?
    // <mrb> see above for a hack, needs to be a lot better.

    // Summary is always included, is that handled somewhere else? (article config says i can ex/include it)
    // <mikespub> articles config allows you to call transforms for the articles summaries in the view function
    if (!isset($titletransform)) {
        if (empty($settings['titletransform'])) {
            $data['transform'][] = 'summary';
            $data['transform'][] = 'body';
            $data['transform'][] = 'notes';

        } else {
            $data['transform'][] = 'title';
            $data['transform'][] = 'summary';
            $data['transform'][] = 'body';
            $data['transform'][] = 'notes';
        }
    }
    $data = xarModCallHooks('item', 'transform', $id, $data, 'articles');

    if (!empty($data['title'])) {
        // CHECKME: <rabbit> Strip tags out of the title - the <title> tag shouldn't have any other tags in it.
        $title = strip_tags($data['title']);
        xarTplSetPageTitle(xarVarPrepForDisplay($title), xarVarPrepForDisplay($pubtypes[$pubtypeid]['descr']));

        // Save some variables to (temporary) cache for use in blocks etc.
        xarVarSetCached('Comments.title','title',$data['title']);
    }

/*
    if (!empty($q)) {
    // TODO: split $q into search terms + add style (cfr. handlesearch in search module)
        foreach ($data['transform'] as $field) {
            $data[$field] = preg_replace("/$q/","<span class=\"xar-search-match\">$q</span>",$data[$field]);
        }
    }
*/

    // Navigation links
    $data['publabel'] = xarML('Publication');
    $data['publinks'] = xarModAPIFunc('articles','user','getpublinks',
                                     array('status' => array(ARTCLES_STATE_FRONTPAGE,ARTCLES_STATE_APPROVED),
                                           'count' => $showpubcount));
    if (isset($showmap)) {
        $settings['showmap'] = $showmap;
    }
    if (!empty($settings['showmap'])) {
        $data['maplabel'] = xarML('View Article Map');
        $data['maplink'] = xarModURL('articles','user','viewmap',
                                    array('ptid' => $ptid));
    }
    if (isset($showarchives)) {
        $settings['showarchives'] = $showarchives;
    }
    if (!empty($settings['showarchives'])) {
        $data['archivelabel'] = xarML('View Archives');
        $data['archivelink'] = xarModURL('articles','user','archive',
                                        array('ptid' => $ptid));
    }
    if (isset($showpublinks)) {
        $settings['showpublinks'] = $showpublinks;
    }
    if (!empty($settings['showpublinks'])) {
        $data['showpublinks'] = 1;
    } else {
        $data['showpublinks'] = 0;
    }
    $data['showcatcount'] = $showcatcount;

    // Tell the hitcount hook not to display the hitcount, but to save it
    // in the variable cache.
    if (xarModIsHooked('hitcount','articles',$pubtypeid)) {
        xarVarSetCached('Hooks.hitcount','save',1);
        $data['dohitcount'] = 1;
    } else {
        $data['dohitcount'] = 0;
    }

    // Tell the ratings hook to save the rating in the variable cache.
    if (xarModIsHooked('ratings','articles',$pubtypeid)) {
        xarVarSetCached('Hooks.ratings','save',1);
        $data['doratings'] = 1;
    } else {
        $data['doratings'] = 0;
    }

    // Hooks
    if ($preview) {
        $data['hooks'] = '';
    } else {
        $data['hooks'] = xarModCallHooks('item', 'display', $id,
                                         array('module'    => 'articles',
                                               'itemtype'  => $pubtypeid,
                                               'returnurl' => xarModURL('articles',
                                                                        'user',
                                                                        'display',
                                                                        array('ptid' => $ptid,
                                                                              'id' => $id))
                                              ),
                                         'articles'
                                        );
    }

    // Retrieve the current hitcount from the variable cache
    if ($data['dohitcount'] && xarVarIsCached('Hooks.hitcount','value')) {
        $data['counter'] = xarVarGetCached('Hooks.hitcount','value');
    } else {
        $data['counter'] = '';
    }

    // Retrieve the current rating from the variable cache
    if ($data['doratings'] && xarVarIsCached('Hooks.ratings','value')) {
        $data['rating'] = intval(xarVarGetCached('Hooks.ratings','value'));
    } else {
        $data['rating'] = '';
    }

    // Save some variables to (temporary) cache for use in blocks etc.
    xarVarSetCached('Blocks.articles','title',$data['title']);

    // Generating keywords from the API now instead of setting the entire
    // body into the cache.
    $keywords = xarModAPIFunc('articles',
                              'user',
                              'generatekeywords',
                              array('incomingkey' => $data['body']));

    xarVarSetCached('Blocks.articles','body',$keywords);
    xarVarSetCached('Blocks.articles','summary',$data['summary']);
    xarVarSetCached('Blocks.articles','id',$id);
    xarVarSetCached('Blocks.articles','ptid',$ptid);
    xarVarSetCached('Blocks.articles','cids',$cids);
    xarVarSetCached('Blocks.articles','authorid',$authorid);
    if (isset($data['author'])) {
        xarVarSetCached('Blocks.articles','author',$data['author']);
    }
// TODO: add this to articles configuration ?
//if ($shownavigation) {
    $data['id'] = $id;
    $data['cids'] = $cids;
    xarVarSetCached('Blocks.categories','module','articles');
    xarVarSetCached('Blocks.categories','itemtype',$ptid);
    xarVarSetCached('Blocks.categories','itemid',$id);
    xarVarSetCached('Blocks.categories','cids',$cids);

    if (!empty($ptid) && !empty($pubtypes[$ptid]['descr'])) {
        xarVarSetCached('Blocks.categories','title',$pubtypes[$ptid]['descr']);
    }

    // optional category count
    if ($showcatcount && !empty($ptid)) {
        $pubcatcount = xarModAPIFunc('articles',
                                    'user',
                                    'getpubcatcount',
                                    // frontpage or approved
                                    array('status' => array(ARTCLES_STATE_FRONTPAGE,ARTCLES_STATE_APPROVED),
                                          'ptid' => $ptid));
        if (!empty($pubcatcount[$ptid])) {
            xarVarSetCached('Blocks.categories','catcount',$pubcatcount[$ptid]);
        }
    } else {
    //    xarVarSetCached('Blocks.categories','catcount',array());
    }
//}

    // Module template depending on publication type
    $template = $pubtypes[$pubtypeid]['name'];

    // Page template depending on publication type (optional)
    // Note : this cannot be overridden in templates
    if (empty($preview) && !empty($settings['page_template'])) {
        xarTplSetPageTemplateName($settings['page_template']);
    }

    // Specific layout within a template (optional)
    if (isset($layout)) {
        $data['layout'] = $layout;
    }

    // return template out
    return xarTplModule('articles', 'user', 'display', $data, $template);
}

?>
