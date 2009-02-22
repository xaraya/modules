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
 * view publications
 *
 * catid=1   : category 1        == cids[0]=1
 * catid=1-2 : category 1 OR 2   == cids[0]=1&cids[1]=2
 * catid=1+2 : category 1 AND 2  == cids[0]=1&cids[1]=2&andcids=1
 *
 * @param template string Alternative default view-template name.
 * @param showcatcount integer Show the number of publications for each category (0..1)
 * @param showpubcount integer Show the number of publications for each publication type (0..1)
 *
 * @todo Provide a 'data only' mode that returns each item as data rather than through a rendered template
 *
 */

 sys::import('modules.dynamicdata.class.objects.master');

function publications_user_view($args)
{
    // Get parameters
    if (!xarVarFetch('ptid',     'id',    $ptid,      xarModVars::get('publications', 'defaultpubtype'), XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('startnum', 'int:0', $startnum,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('cids',     'array', $cids,      NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('andcids',  'str',   $andcids,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('catid',    'str',   $catid,     NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('itemtype', 'id',    $itemtype,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    // TODO: put the query string through a proper parser, so searches on multiple words can be done.
    if (!xarVarFetch('q',        'pre:trim:passthru:str:1:200',   $q,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    // can't use list enum here, because we don't know which sorts might be used
    // True - but we can provide some form of validation and normalisation.
    // The original 'regexp:/^[\w,]*$/' lets through *any* non-space character.
    // This validation will accept a list of comma-separated words, and will lower-case, trim
    // and strip out non-alphanumeric characters from each word.
    if (!xarVarFetch('sort',     'strlist:,:pre:trim:lower:alnum', $sort, NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('numcols',  'int:0', $numcols,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('owner', 'id',    $owner,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('pubdate',  'str:1', $pubdate,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    // This may not be set via user input, only e.g. via template tags, API calls, blocks etc.
    //    if(!xarVarFetch('startdate','int:0', $startdate, NULL, XARVAR_NOT_REQUIRED)) {return;}
    //    if(!xarVarFetch('enddate',  'int:0', $enddate,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    //    if(!xarVarFetch('where',    'str',   $where,     NULL, XARVAR_NOT_REQUIRED)) {return;}

    // Added to impliment an Alpha Pager
    if (!xarVarFetch('letter', 'pre:lower:passthru:str:1:20', $letter, NULL, XARVAR_NOT_REQUIRED)) return;

    // Override if needed from argument array (e.g. ptid, numitems etc.)
    extract($args);

    // Constants used throughout.
    //
    // publications module ID
    $c_modid = xarMod::getID('publications');
    // state: front page or approved
    $c_posted = array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED);

    // Default parameters
    if (!isset($startnum)) $startnum = 1;

    // Check if we want the default 'front page'
    if (!isset($catid) && !isset($cids) && empty($ptid) && !isset($owner)) {
        $ishome = 1;
        // default publication type
        $ptid = xarModVars::get('publications', 'defaultpubtype');
        // frontpage state
        $state = array(PUBLICATIONS_STATE_FRONTPAGE);
    } else {
        $ishome = 0;
        // frontpage or approved state
        $state = $c_posted;
    }

    // Get the publication type for this display
    $data['pubtypeobject'] = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $data['pubtypeobject']->getItem(array('itemid' => $ptid));

    // Get the settings of this publication type
    $data['settings'] = $data['pubtypeobject']->properties['configuration']->getValue();

    // Get the template for this publication type
    if ($ishome) $data['template'] = 'frontpage';
    else $data['template'] = $data['pubtypeobject']->properties['template']->getValue();

    
    
    $isdefault = 0;
    // check default view for this type of publications
    if (empty($catid) && empty($cids) && empty($owner) && empty($sort)) {
        if (substr($data['settings']['defaultview'], 0, 1) == 'c') {
            $catid = substr($data['settings']['defaultview'], 1);
        }
    }

    // Do not transform titles if we are not transforming output at all.
    if (empty($data['settings']['dotransform'])) $data['settings']['dotitletransform'] = 0;

    // Page template for frontpage or depending on publication type (optional)
    // Note : this cannot be overridden in templates
    if (!empty($data['settings']['page_template'])) {
        xarTplSetPageTemplateName($data['settings']['page_template']);
    }

    if (empty($data['settings']['defaultsort'])) {
        $defaultsort = 'date';
    } else {
        $defaultsort = $data['settings']['defaultsort'];
    }
    if (empty($sort)) {
        $sort = $defaultsort;
    }

    // TODO: show this *after* category list when we start from categories :)
    // Navigation links
    $data['publabel'] = xarML('Publication');
    $data['publinks'] = xarModAPIFunc('publications', 'user', 'getpublinks',
        array(
            'ptid' => $ishome ? '' : $ptid,
            'state' => $c_posted,
            'count' => $data['settings']['showpubcount']
        )
    );
    $data['pager'] = '';

    // Add Sort to data passed to template so that we can automatically turn on alpha pager, if needed
    $data['sort'] = $sort;

    // Add current display letter, so that we can highlight the current filter in the alpha pager
    $data['letter']=$letter;

    // Get the users requested number of stories per page.
    // If user doesn't care, use the site default
    if (xarUserIsLoggedIn()) {
        // TODO: figure how to let users specify their settings
        // COMMENT: if the settings were split into separate module variables,
        // then they could all be individually over-ridden by each user.
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
        if (strpos($catid, ' ')) {
            $cids = explode(' ', $catid);
            $andcids = true;
        } elseif (strpos($catid, '+')) {
            $cids = explode('+', $catid);
            $andcids = true;
        } elseif (strpos($catid, '-')) {
            $cids = explode('-', $catid);
            $andcids = false;
        } else {
            $cids = array($catid);
            if (strstr($catid, '_')) {
                $andcids = false; // don't combine with current category
            } else {
                $andcids = true;
            }
        }
    } else {
        if (empty($cids)) $cids = array();
        if (!isset($andcids)) $andcids = true;
    }
    // rebuild $catid in standard format again
    $catid = null;
    if (count($cids) > 0) {
        $seencid = array();
        foreach ($cids as $cid) {
            // make sure cids are numeric
            if (!empty($cid) && preg_match('/^_?[0-9]+$/', $cid)) {
                $seencid[$cid] = 1;
            }
        }
        $cids = array_keys($seencid);
        sort($cids, SORT_NUMERIC);
        if ($andcids) {
            $catid = join('+', $cids);
        } else {
            $catid = join('-', $cids);
        }
    }

    // every field you always wanted to know about but were afraid to ask for :)
    $extra = array();
    $extra[] = 'author';

    // Note: we always include cids for security checks now (= performance impact if showcategories was 0)
    $extra[] = 'cids';
    if ($data['settings']['showhitcounts']) $extra[] = 'counter';
    if ($data['settings']['showratings']) $extra[] = 'rating';
    if (xarModIsHooked('dynamicdata', 'publications', $ptid)) $extra[] = 'dynamicdata';
    if (xarModIsHooked('uploads', 'publications', $ptid)) xarVarSetCached('Hooks.uploads', 'ishooked', 1);

    $now = time();

    if (empty($startdate) || !is_numeric($startdate) || $startdate > $now) $startdate = null;
    if (empty($enddate) || !is_numeric($enddate) || $enddate > $now) $enddate = $now;
    if (empty($pubdate) || !preg_match('/^\d{4}(-\d+(-\d+|)|)$/',$pubdate)) $pubdate = null;
    if (empty($where)) $where = null;

    // Modify the where clause if an Alpha filter has been specified.
    if (!empty($letter)) {
        // We will allow up to three initial letters, anything more than that is assumed to be 'Other'.
        // Need to also be very wary of SQL injection, since we are not using bind variables here.
        // TODO: take into account international characters.
        if (preg_match('/^[a-z]{1,3}$/i', $letter)) {
            $extrawhere = "title LIKE '$letter%'";
        } else {
            // Loop through the alphabet for the 'not in' part.
            $letterwhere = array();
            for($i = ord('a'); $i <= ord('z'); $i++) {
                $letterwhere[] = "title NOT LIKE '" . chr($i) . "%'";
            }
            $extrawhere = implode(' and ', $letterwhere);
        }
        if ($where == null) {
            $where = $extrawhere;
        } else {
            $where .= $extrawhere;
        }
    }

    // Get publications
    $publications = xarModAPIFunc(
        'publications', 'user', 'getall',
        array(
            'startnum' => $startnum,
            'cids' => $cids,
            'andcids' => $andcids,
            'ptid' => (isset($ptid) ? $ptid : null),
            'owner' => $owner,
            'state' => $state,
            'sort' => $sort,
            'extra' => $extra,
            'where' => $where,
            'search' => $q,
            'numitems' => $numitems,
            'pubdate' => $pubdate,
            'startdate' => $startdate,
            'enddate' => $enddate
        )
    );

    if (!is_array($publications)) {
        throw new Exception('Failed to retrieve publications');
    }

    // TODO : support different 'index' templates for different types of publications
    //        (e.g. News, Sections, ...), depending on what "view" the user
    //        selected (per category, per publication type, a combination, ...) ?

    if (!empty($owner)) {
        $data['author'] = xarUserGetVar('name', $owner);
        if (empty($data['author'])) {
            xarErrorHandled();
            $data['author'] = xarML('Unknown');
        }
    }
    if (!empty($pubdate)) {
        $data['pubdate'] = $pubdate;
    }

    // Save some variables to (temporary) cache for use in blocks etc.
    xarVarSetCached('Blocks.publications', 'ptid', $ptid);
    xarVarSetCached('Blocks.publications', 'cids', $cids);
    xarVarSetCached('Blocks.publications', 'owner', $owner);
    if (isset($data['author'])) {
        xarVarSetCached('Blocks.publications', 'author', $data['author']);
    }
    if (isset($data['pubdate'])) {
        xarVarSetCached('Blocks.publications', 'pubdate', $data['pubdate']);
    }

    // TODO: add this to publications configuration ?
    if ($ishome) {
        $data['ptid'] = null;
        if (xarSecurityCheck('SubmitPublications',0)) {
            $data['submitlink'] = xarModURL('publications', 'admin', 'new');
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
                if (xarSecurityCheck('SubmitPublications', 0, 'Publication', "$curptid:$cid:All:All")) {
                    $data['submitlink'] = xarModURL('publications', 'admin', 'new', array('ptid' => $ptid, 'catid' => $catid));
                    break;
                }
            }
        } elseif (xarSecurityCheck('SubmitPublications', 0, 'Publication', "$curptid:All:All:All")) {
            $data['submitlink'] = xarModURL('publications', 'admin', 'new', array('ptid' => $ptid));
        }
    }
    $data['cids'] = $cids;
    $data['catid'] = $catid;
    xarVarSetCached('Blocks.categories', 'module', 'publications');
    xarVarSetCached('Blocks.categories', 'itemtype', $ptid);
    xarVarSetCached('Blocks.categories', 'cids', $cids);
    if (!empty($ptid) && !empty($pubtypes[$ptid]['description'])) {
        xarVarSetCached('Blocks.categories', 'title', $pubtypes[$ptid]['description']);
        // Note : this gets overriden by the categories navigation if necessary
        xarTplSetPageTitle(xarVarPrepForDisplay($pubtypes[$ptid]['description']));
    }

    // optional category count
    if ($data['settings']['showcatcount']) {
        if (!empty($ptid)) {
            $pubcatcount = xarModAPIFunc('publications', 'user', 'getpubcatcount',
                // frontpage or approved
                array('state' => $c_posted, 'ptid' => $ptid)
            );
            if (isset($pubcatcount[$ptid])) {
                xarVarSetCached('Blocks.categories','catcount',$pubcatcount[$ptid]);
            }
            unset($pubcatcount);
        } else {
            $pubcatcount = xarModAPIFunc('publications', 'user', 'getpubcatcount',
                // frontpage or approved
                array('state' => $c_posted, 'reverse' => 1)
            );

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
        // xarVarSetCached('Blocks.categories','catcount',array());
    }

    // retrieve the number of comments for each article
    if ($data['settings']['showcomments']) {
        $idlist = array();
        foreach ($publications as $article) {
            $idlist[] = $article['id'];
        }
        $numcomments = xarModAPIFunc('comments', 'user', 'get_countlist',
            array('modid' => $c_modid, 'objectids' => $idlist)
        );
    }

    // retrieve the keywords for each article
    if ($data['settings']['showkeywords']) {
        $idlist = array();
        foreach ($publications as $article) {
            $idlist[] = $article['id'];
        }

        $keywords = xarModAPIFunc('keywords', 'user', 'getmultiplewords',
            array(
                'modid' => $c_modid,
                'objectids' =>  $idlist,
                'itemtype'  => $ptid
            )
        );
    }
/*
    // retrieve the categories for each article
    $catinfo = array();
    if ($showcategories) {
        $cidlist = array();
        foreach ($publications as $article) {
            if (!empty($article['cids']) && count($article['cids']) > 0) {
                 foreach ($article['cids'] as $cid) {
                     $cidlist[$cid] = 1;
                 }
            }
        }
        if (count($cidlist) > 0) {
            $catinfo = xarModAPIFunc('categories','user','getcatinfo', array('cids' => array_keys($cidlist)));
            // get root categories for this publication type
            // get base categories for all if needed
            $catroots = xarModAPIFunc('publications', 'user', 'getrootcats',
                array('ptid' => $ptid, 'all' => true)
            );
        }
        foreach ($catinfo as $cid => $info) {
            $catinfo[$cid]['name'] = xarVarPrepForDisplay($info['name']);
            $catinfo[$cid]['link'] = xarModURL('publications', 'user', 'view',
                array('ptid' => $ptid, 'catid' => (($catid && $andcids) ? $catid . '+' . $cid : $cid) )
            );

            // only needed when sorting by root category id
            $catinfo[$cid]['root'] = 0; // means not found under a root category
            // only needed when sorting by root category order
            $catinfo[$cid]['order'] = 0; // means not found under a root category
            $rootidx = 1;
            foreach ($catroots as $rootcat) {
                // see if we're a child category of this rootcat (cfr. Celko model)
                if ($info['left'] >= $rootcat['catleft'] && $info['left'] < $rootcat['catright']) {
                    // only needed when sorting by root category id
                    $catinfo[$cid]['root'] = $rootcat['catid'];
                    // only needed when sorting by root category order
                    $catinfo[$cid]['order'] = $rootidx;
                    break;
                }
                $rootidx++;
            }
        }
        // needed for sort function below
        $GLOBALS['artviewcatinfo'] = $catinfo;
    }
*/
/*    $number = 0;
    foreach ($publications as $article)
    {
        // TODO: don't include ptid and catid if we don't use short URLs
        // link to article
        $article['link'] = xarModURL('publications', 'user', 'display',
            // don't include pubtype id if we're navigating by category
            array(
                'ptid' => empty($ptid) ? null : $article['pubtype_id'],
                'catid' => $catid,
                'id' => $article['id']
            )
        );

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
        $curptid = $article['pubtype_id'];

        // TODO: make configurable?
        $article['redirect'] = xarModURL('publications', 'user', 'redirect',
            array('ptid' => $curptid, 'id' => $article['id'])
        );


        // multi-column display (default from left to right, then from top to bottom)
        $article['number'] = $number;
        if (!empty($settings['number_of_columns'])) {
            $col = $number % $settings['number_of_columns'];
        } else {
            $col = 0;
        }

        // RSS Processing
        $current_theme = xarVarGetCached('Themes.name', 'CurrentTheme');
        if (($current_theme == 'rss') or ($current_theme == 'atom')){
            $article['rsstitle'] = htmlspecialchars($article['title']);
            //$article['rssdate'] = strtotime($article['date']);
            $article['rsssummary'] = preg_replace('<br />', "\n", $article['summary']);
            $article['rsssummary'] = xarVarPrepForDisplay(strip_tags($article['rsssummary']));
            $article['rsscomment'] = xarModURL('comments', 'user', 'display', array('modid' => $c_modid, 'objectid' => $article['id']));
            // $article['rsscname'] = htmlspecialchars($item['cname']);
            // <category>#$rsscname#</category>
        }

        // TODO: clean up depending on field format
        if ($dotransform) {
            $article['itemtype'] = $article['pubtype_id'];
            // TODO: what about transforming DD fields?
            if ($titletransform) {
                $article['transform'] = array('title', 'summary', 'body', 'notes');
            } else {
                $article['transform'] = array('summary', 'body', 'notes');
            }
            $article = xarModCallHooks('item', 'transform', $article['id'], $article, 'publications');
        }

        $data['titles'][$article['id']] = $article['title'];

        // fill in the summary template for this article
        $summary_template = $pubtypes[$article['pubtype_id']]['name'];
        $number++;echo $number;
    }
*/
    unset($publications);


    // TODO: verify for other URLs as well
    if ($ishome) {
        if (!empty($numcols) && $numcols > 1) {
            // if we're currently showing more than 1 column
            $data['showcols'] = 1;
        } else {
            $defaultcols = $data['settings']['number_of_columns'];
            if ($defaultcols > 1) {
                // if the default number of columns is more than 1
                $data['showcols'] = $defaultcols;
            }
        }
    }

    // Specific layout within a template (optional)
    if (isset($layout)) $data['layout'] = $layout;

    // Get the publications we want to view
    $data['object'] = DataObjectMaster::getObject(array('name' => $data['pubtypeobject']->properties['name']->value));
    
    $object = DataObjectMaster::getObjectList(array('name' => $data['pubtypeobject']->properties['name']->value));
    $data['items'] = $object->getItems();
    $data['object'] = DataObjectMaster::getObject(array('name' => $data['pubtypeobject']->properties['name']->value));
    
    // Only show top level documents, not translations
    $q = new Query();
    $q->eq('parent_id',0);
    $data['conditions'] = $q;

    // Throw all the relevant settings we are using into the cache
    $data['settings']['pubtypeobject'] = $data['pubtypeobject'];
    xarCore::setCached('publications', 'context' . $ptid, $data['settings']);

    return xarTplModule('publications', 'user', 'view', $data, $data['template']);
}

?>