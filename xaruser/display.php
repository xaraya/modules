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
 * Display publication
 *
 * @param int id
 * @param int page
 * @param int ptid The publication Type ID
 * @return array with template information
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_user_display($args)
{
    extract ($args);
    // Get parameters from user
    if(!xarVarFetch('ptid',    'id',    $ptid,  xarModVars::get('publications', 'defaultpubtype'), XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('itemid',  'id',    $itemid,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('page', 'int:1', $page,  NULL, XARVAR_NOT_REQUIRED)) {return;}
// this is used to determine whether we come from a pubtype-based view or a
// categories-based navigation

    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $ptid));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
    $data['object']->getItem(array('itemid' => $itemid));

    return xarTplModule('publications', 'user', 'display', $data);


/*
    // TEST - highlight search terms
    if(!xarVarFetch('q',     'str',  $q,     NULL, XARVAR_NOT_REQUIRED)) {return;}
*/

    // Override if needed from argument array (e.g. preview)
    extract($args);

    // Defaults
    if (!isset($page)) $page = 1;

    // via arguments only
    if (!isset($preview)) $preview = 0;

/*
    if ($preview) {
        if (!isset($publication)) {
            return xarML('Invalid publication');
        }
        $id = $publication->properties['id']->value;
    } elseif (!isset($id) || !is_numeric($id) || $id < 1) {
        return xarML('Invalid publication ID');
    }
*/

/*    // Get publication
    if (!$preview) {
        $publication = xarModAPIFunc('publications',
                                'user',
                                'get',
                                array('id' => $id,
                                      'withcids' => true));
    }

    if (!is_array($publication)) {
        $msg = xarML('Failed to retrieve publication in #(3)_#(1)_#(2).php', 'userapi', 'get', 'publications');
        throw new DataNotFoundException(null, $msg);
    }
    // Get publication types
    $pubtypes = xarModAPIFunc('publications','user','getpubtypes');

    // Check that the publication type is valid, otherwise use the publication's pubtype
    if (!empty($ptid) && !isset($pubtypes[$ptid])) {
        $ptid = $publication['pubtype_id'];
    }

*/
// keep original ptid (if any)
//    $ptid = $publication['pubtype_id'];
//    $pubtype_id = $publication->properties['itemtype']->value;
//    $owner = $publication->properties['author']->value;
/*    if (!isset($publication['cids'])) {
        $publication['cids'] = array();
    }
    $cids = $publication['cids'];
*/
    // Get the publication settings for this publication type
    if (empty($ptid)) {
        $settings = unserialize(xarModVars::get('publications', 'settings'));
    } else {
        $settings = unserialize(xarModVars::get('publications', 'settings.'.$ptid));
    }

    // show the number of publications for each publication type
    if (!isset($showpubcount)) {
        if (!isset($settings['showpubcount']) || !empty($settings['showpubcount'])) {
            $showpubcount = 1; // default yes
        } else {
            $showpubcount = 0;
        }
    }
    // show the number of publications for each category
    if (!isset($showcatcount)) {
        if (empty($settings['showcatcount'])) {
            $showcatcount = 0; // default no
        } else {
            $showcatcount = 1;
        }
    }

    // Initialize the data array
    $data = $publication->getFieldValues();
    $data['ptid'] = $ptid; // navigation pubtype
    $data['pubtype_id'] = $pubtype_id; // publication pubtype

    // Security check for EDIT access
    if (!$preview) {
        $input = array();
        $input['publication'] = $publication;
        $input['mask'] = 'EditPublications';
        if (xarModAPIFunc('publications','user','checksecurity',$input)) {
            $data['editurl'] = xarModURL('publications', 'admin', 'modify',
                                         array('id' => $publication['id']));
        // don't show unapproved publications to non-editors
        } elseif ($publication['state'] < 2) {
            $state = xarModAPIFunc('publications', 'user', 'getstatename',
                                    array('state' => $publication['state']));
            return xarML('You have no permission to view this item [Status: #(1)]', $state);
        }
    }
    $data['edittitle'] = xarML('Edit');

// TODO: improve the case where we have several icons :)
    $data['topic_icons'] = '';
    $data['topic_images'] = array();
    $data['topic_urls'] = array();
    $data['topic_names'] = array();
    /*
    if (count($cids) > 0) {
        if (!xarModAPILoad('categories', 'user')) return;
        $catlist = xarModAPIFunc('categories',
                                'user',
                                'getcatinfo',
                                array('cids' => $cids));
        foreach ($catlist as $cat) {
            $link = xarModURL('publications','user','view',
                             array(//'state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED).
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
*/
    // multi-page output for 'body' field (mostly for sections at the moment)
    $themeName = xarVarGetCached('Themes.name','CurrentTheme');
    if ($themeName != 'print'){
        if (strstr($publication->properties['body']->value,'<!--pagebreak-->')) {
            if ($preview) {
                $publication['body'] = preg_replace('/<!--pagebreak-->/',
                                                '<hr/><div style="text-align: center;">'.xarML('Page Break').'</div><hr/>',
                                                $publication->properties['body']->value);
                $data['previous'] = '';
                $data['next'] = '';
            } else {
                $pages = explode('<!--pagebreak-->',$publication->properties['body']->value);

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
                $publication['body'] = $pages[$page - 1];
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
                    'publications','user','display',
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
        $publication['body'] = preg_replace('/<!--pagebreak-->/',
                                        '',
                                        $publication['body']);
    }

    // TEST
    if (isset($prevnextart)) {
        $settings['prevnextart'] = $prevnextart;
    }
    if (!empty($settings['prevnextart']) && ($preview == 0)) {
        if(!array_key_exists('defaultsort',$settings)) {
            $settings['defaultsort'] = 'id';
        }
        $prevart = xarModAPIFunc('publications','user','getprevious',
                                 array('id' => $id,
                                       'ptid' => $ptid,
                                       'sort' => $settings['defaultsort'],
                                       'state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED),
                                       'enddate' => time()));
        if (!empty($prevart['id'])) {
            //Make all previous publication info available to template
            $data['prevartinfo'] = $prevart;

            $data['prevart'] = xarModURL('publications','user','display',
                                         array('ptid' => $prevart['pubtype_id'],
                                               'id' => $prevart['id']));
        } else {
            $data['prevart'] = '';
        }
        $nextart = xarModAPIFunc('publications','user','getnext',
                                 array('id' => $id,
                                       'ptid' => $ptid,
                                       'sort' => $settings['defaultsort'],
                                       'state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED),
                                       'enddate' => time()));
        if (!empty($nextart['id'])) {
            //Make all next art info available to template
            $data['nextartinfo'] = $nextart;

            $data['nextart'] = xarModURL('publications','user','display',
                                         array('ptid' => $nextart['pubtype_id'],
                                               'id' => $nextart['id']));
        } else {
            $data['nextart'] = '';
        }
    } else {
        $data['prevart'] = '';
        $data['nextart'] = '';
    }

    // Display publication
    unset($publication);

    // temp. fix to include dynamic data fields without changing templates
    if (xarModIsHooked('dynamicdata','publications',$pubtype_id)) {
        list($properties) = xarModAPIFunc('dynamicdata','user','getitemfordisplay',
                                          array('module'   => 'publications',
                                                'itemtype' => $pubtype_id,
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
    $data['itemtype'] = $pubtype_id;
    // TODO: what about transforming DDfields ?
    // <mrb> see above for a hack, needs to be a lot better.

    // Summary is always included, is that handled somewhere else? (publication config says i can ex/include it)
    // <mikespub> publications config allows you to call transforms for the publications summaries in the view function
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
    $data = xarModCallHooks('item', 'transform', $id, $data, 'publications');

    return xarTplModule('publications', 'user', 'display', $data);


    if (!empty($data['title'])) {
        // CHECKME: <rabbit> Strip tags out of the title - the <title> tag shouldn't have any other tags in it.
        $title = strip_tags($data['title']);
        xarTplSetPageTitle(xarVarPrepForDisplay($title), xarVarPrepForDisplay($pubtypes[$data['itemtype']]['description']));

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
    $data['publinks'] = array(); //xarModAPIFunc('publications','user','getpublinks',
                                 //    array('state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED),
                                 //          'count' => $showpubcount));
    if (isset($showmap)) {
        $settings['showmap'] = $showmap;
    }
    if (!empty($settings['showmap'])) {
        $data['maplabel'] = xarML('View Publication Map');
        $data['maplink'] = xarModURL('publications','user','viewmap',
                                    array('ptid' => $ptid));
    }
    if (isset($showarchives)) {
        $settings['showarchives'] = $showarchives;
    }
    if (!empty($settings['showarchives'])) {
        $data['archivelabel'] = xarML('View Archives');
        $data['archivelink'] = xarModURL('publications','user','archive',
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
    if (xarModIsHooked('hitcount','publications',$pubtype_id)) {
        xarVarSetCached('Hooks.hitcount','save',1);
        $data['dohitcount'] = 1;
    } else {
        $data['dohitcount'] = 0;
    }

    // Tell the ratings hook to save the rating in the variable cache.
    if (xarModIsHooked('ratings','publications',$pubtype_id)) {
        xarVarSetCached('Hooks.ratings','save',1);
        $data['doratings'] = 1;
    } else {
        $data['doratings'] = 0;
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
    xarVarSetCached('Blocks.publications','title',$data['title']);

    // Generating keywords from the API now instead of setting the entire
    // body into the cache.
    $keywords = xarModAPIFunc('publications',
                              'user',
                              'generatekeywords',
                              array('incomingkey' => $data['body']));

    xarVarSetCached('Blocks.publications','body',$keywords);
    xarVarSetCached('Blocks.publications','summary',$data['summary']);
    xarVarSetCached('Blocks.publications','id',$id);
    xarVarSetCached('Blocks.publications','ptid',$ptid);
    xarVarSetCached('Blocks.publications','cids',$cids);
    xarVarSetCached('Blocks.publications','owner',$owner);
    if (isset($data['author'])) {
        xarVarSetCached('Blocks.publications','author',$data['author']);
    }
// TODO: add this to publications configuration ?
//if ($shownavigation) {
    $data['id'] = $id;
    $data['cids'] = $cids;
    xarVarSetCached('Blocks.categories','module','publications');
    xarVarSetCached('Blocks.categories','itemtype',$ptid);
    xarVarSetCached('Blocks.categories','itemid',$id);
    xarVarSetCached('Blocks.categories','cids',$cids);

    if (!empty($ptid) && !empty($pubtypes[$ptid]['description'])) {
        xarVarSetCached('Blocks.categories','title',$pubtypes[$ptid]['description']);
    }

    // optional category count
    if ($showcatcount && !empty($ptid)) {
        $pubcatcount = xarModAPIFunc('publications',
                                    'user',
                                    'getpubcatcount',
                                    // frontpage or approved
                                    array('state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED),
                                          'ptid' => $ptid));
        if (!empty($pubcatcount[$ptid])) {
            xarVarSetCached('Blocks.categories','catcount',$pubcatcount[$ptid]);
        }
    } else {
    //    xarVarSetCached('Blocks.categories','catcount',array());
    }
//}

    // Module template depending on publication type
    $template = $pubtypes[$pubtype_id]['name'];

    // Page template depending on publication type (optional)
    // Note : this cannot be overridden in templates
    if (empty($preview) && !empty($settings['page_template'])) {
        xarTplSetPageTemplateName($settings['page_template']);
    }

    // Specific layout within a template (optional)
    if (isset($layout)) {
        $data['layout'] = $layout;
    }

    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $ptid));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
    $data['object']->getItem(array('itemid' => $itemid));

    return xarTplModule('publications', 'user', 'display', $data, $template);
}

?>
