<?php
sys::import('modules.base.class.pager');

/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Display publication
 *
 * @param int itemid
 * @param str name
 * @param int page
 * @param int ptid The publication type ID
 * @return array with template information
 */

/**
 * Notes
 * If passed an itemid this function will return the page with that ID
 * If instead passed a name or ID of a publication type it will show the view of that publication type
 * If nothing is passed it will show the default page of this module
 * If no default page is defined it will show the custom 404 page of this module
 * If no 404 page is defined it will show the standard Xaraya 404 page
 */

sys::import('modules.dynamicdata.class.objects.master');

function publications_admin_display($args)
{
    if (!xarSecurity::check('EditPublications')) {
        return;
    }

    // Get parameters from user
    // this is used to determine whether we come from a pubtype-based view or a
    // categories-based navigation
    // Note we support both id and itemid
    if (!xarVar::fetch('name', 'str', $name, '', xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('ptid', 'id', $ptid, null, xarVar::DONT_SET)) {
        return;
    }
    if (!xarVar::fetch('itemid', 'id', $itemid, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('id', 'id', $id, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('page', 'int:1', $page, null, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('translate', 'int:1', $translate, 1, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('layout', 'str:1', $layout, 'detail', xarVar::NOT_REQUIRED)) {
        return;
    }
    
    // Override xarVar::fetch
    extract($args);

    //The itemid var takes precedence if it exiata
    if (isset($itemid)) {
        $id = $itemid;
    }
        
    # --------------------------------------------------------
#
    # If no ID supplied, try getting the id of the default page.
#
    if (empty($id)) {
        $id = xarModVars::get('publications', 'defaultpage');
    }

    # --------------------------------------------------------
#
    # Get the ID of the translation if required
#
    // First save the "untranslated" id
    xarCoreCache::setCached('Blocks.publications', 'current_base_id', $id);

    if ($translate) {
        $id = xarMod::apiFunc('publications', 'user', 'gettranslationid', array('id' => $id));
    }
    
    # --------------------------------------------------------
#
    # If still no ID, check if we are trying to display a pubtype
#
    if (empty($name) && empty($ptid) && empty($id)) {
        // Nothing to be done
        $id = xarModVars::get('publications', 'notfoundpage');
    } elseif (empty($id)) {
        // We're missing an id but can get a pubtype: jump to the pubtype view
        xarController::redirect(xarController::URL('publications', 'user', 'view'));
    }
    
    # --------------------------------------------------------
#
    # If still no ID, we have come to the end of the line
#
    if (empty($id)) {
        return xarResponse::NotFound();
    }

    # --------------------------------------------------------
#
    # We have an ID, now first get the page
#
    // Here we get the publication type first, and then from that the page
    // Perhaps more efficient to get the page directly?
    $ptid = xarMod::apiFunc('publications', 'user', 'getitempubtype', array('itemid' => $id));

    // An empty publication type means the page does not exist
    if (empty($ptid)) {
        return xarResponse::NotFound();
    }

    /*    if (empty($name) && empty($ptid)) return xarResponse::NotFound();

        if(empty($ptid)) {
            $publication_type = DataObjectMaster::getObjectList(array('name' => 'publications_types'));
            $where = 'name = ' . $name;
            $items = $publication_type->getItems(array('where' => $where));
            $item = current($items);
            $ptid = $item['id'];
        }
    */
    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $ptid));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
//    $id = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $id));
    $itemid = $data['object']->getItem(array('itemid' => $id));
    
    # --------------------------------------------------------
#
    # Are we allowed to see this page?
#
    $accessconstraints = unserialize($data['object']->properties['access']->value);
    $access = DataPropertyMaster::getProperty(array('name' => 'access'));
    $data['allow'] = $access->check($accessconstraints['display']);
    $nopublish = (time() < $data['object']->properties['start_date']->value) || ((time() > $data['object']->properties['end_date']->value) && !$data['object']->properties['no_end']->value);
    
    // If no access, then bail showing a forbidden or an empty page
    if (!$data['allow'] || $nopublish) {
        if ($accessconstraints['display']['failure']) {
            return xarResponse::Forbidden();
        } else {
            return $data;
        }
    }
    
    # --------------------------------------------------------
#
    # If this is a redirect page, then send it on its way now
#
    $redirect_type = $data['object']->properties['redirect_flag']->value;
    if ($redirect_type == 1) {
        // This is a simple redirect to another page
        try {
            $url = $data['object']->properties['redirect_url']->value;
                
            // Check if this is a Xaraya function
            $pos = strpos($url, 'xar');
            if ($pos === 0) {
                eval('$url = ' . $url .';');
            }
                
            xarController::redirect($url, 301);
        } catch (Exception $e) {
            return xarResponse::NotFound();
        }
    } elseif ($redirect_type == 2) {
        // This displays a page of a different module
        // If this is from a link of a redirect child page, use the child param as new URL
        if (!xarVar::fetch('child', 'str', $child, null, xarVar::NOT_REQUIRED)) {
            return;
        }
        if (!empty($child)) {
            // Turn entities into amps
            $url = urldecode($child);
        } else {
            $url = $data['object']->properties['proxy_url']->value;
        }
        
        // Bail if the URL is bad
        try {
            // Check if this is a Xaraya function
            $pos = strpos($url, 'xar');
            if ($pos === 0) {
                eval('$url = ' . $url .';');
            }
            
            $params = parse_url($url);
            $params['query'] = preg_replace('/&amp;/', '&', $params['query']);
        } catch (Exception $e) {
            return xarResponse::NotFound();
        }
        
        // If this is an external link, show it without further processing
        if (!empty($params['host']) && $params['host'] != xarServer::getHost() && $params['host'].":".$params['port'] != xarServer::getHost()) {
            xarController::redirect($url, 301);
        } else {
            parse_str($params['query'], $info);
            $other_params = $info;
            unset($other_params['module']);
            unset($other_params['type']);
            unset($other_params['func']);
            unset($other_params['child']);
            try {
                $page = xarMod::guiFunc($info['module'], 'user', $info['func'], $other_params);
            } catch (Exception $e) {
                return xarResponse::NotFound();
            }
            
            // Debug
            // echo xarController::URL($info['module'],'user',$info['func'],$other_params);
            # --------------------------------------------------------
#
            # For proxy pages: the transform of the subordinate function's template
#
            // Find the URLs in submits
            $pattern='/(action)="([^"\r\n]*)"/';
            preg_match_all($pattern, $page, $matches);
            $pattern = array();
            $replace = array();
            foreach ($matches[2] as $match) {
                $pattern[] = '%</form%';
                $replace[] = '<input type="hidden" name="return_url" id="return_url" value="' . urlencode(xarServer::getCurrentURL()) . '"/><input type="hidden" name="child" value="' . urlencode($match) . '"/></form';
            }
            $page = preg_replace($pattern, $replace, $page);

            $pattern='/(action)="([^"\r\n]*)"/';
            $page = preg_replace_callback(
                $pattern,
                create_function(
                    '$matches',
                    'return $matches[1]."=\"".xarServer::getCurrentURL()."\"";'
                ),
                $page
            );

            // Find the URLs in links
            $pattern='/(href)="([^"\r\n]*)"/';
            $page = preg_replace_callback(
                $pattern,
                create_function(
                    '$matches',
                    'return $matches[1]."=\"".xarServer::getCurrentURL(array("child" => urlencode($matches[2])))."\"";'
                ),
                $page
            );

            return $page;
        }
    }

    # --------------------------------------------------------
#
    # If this is a bloccklayout page, then process it
#

    if ($data['object']->properties['pagetype']->value == 2) {
        // Get a copy of the compiler
        sys::import('xaraya.templating.compiler');
        $blCompiler = XarayaCompiler::instance();
        
        // Get the data fields
        $fields = array();
        $sourcefields = array('title','description','summary','body1','body2','body3','body4','body5','notes');
        $prefix = strlen('publications.')-1;
        foreach ($data['object']->properties as $prop) {
            if (in_array(substr($prop->source, $prefix), $sourcefields)) {
                $fields[] = $prop->name;
            }
        }

        // Run each template field through the compiler
        foreach ($fields as $field) {
            try {
                $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
                $tplString .= xarMod::apiFunc('publications', 'user', 'prepareforbl', array('string' => $data['object']->properties[$field]->value));

                $tplString .= '</xar:template>';

                $tplString = $blCompiler->compilestring($tplString);
                // We don't allow passing $data to the template for now
                $tpldata = array();
                $tplString = xarTpl::string($tplString, $tpldata);
            } catch (Exception $e) {
                var_dump($tplString);
            }
            $data['object']->properties[$field]->value = $tplString;
        }
    }

    # --------------------------------------------------------
#
    # Get the complete tree for this section of pages. We need this for blocks etc.
#
            
    $tree = xarMod::apiFunc(
        'publications',
        'user',
        'getpagestree',
        array(
            'tree_contains_pid' => $id,
            'key' => 'id',
            'status' => 'ACTIVE,FRONTPAGE,PLACEHOLDER'
        )
    );
        
    // If this page is of type PLACEHOLDER, then look in its descendents
    if ($data['object']->properties['state']->value == 5) {
    
        // Scan for a descendent that is ACTIVE or FRONTPAGE
        if (!empty($tree['pages'][$id]['child_keys'])) {
            foreach ($tree['pages'][$id]['child_keys'] as $scan_key) {
                // If the page is displayable, then treat it as the new page.
                if ($tree['pages'][$scan_key]['status'] == 3 || $tree['pages'][$scan_key]['status'] == 4) {
                    $id = $tree['pages'][$scan_key]['id'];
                    $id = xarMod::apiFunc('publications', 'user', 'gettranslationid', array('id' => $id));
                    $itemid = $data['object']->getItem(array('itemid' => $id));
                    break;
                }
            }
        }
    }
    
    # --------------------------------------------------------
#
    # Additional data
#
    // Pass the layout to the template
    $data['layout'] = $layout;

    // Get the settings for this publication type;
    $data['settings'] = xarMod::apiFunc('publications', 'user', 'getsettings', array('ptid' => $ptid));
    
    // The name of this object
    $data['objectname'] = $data['object']->name;
    
    # --------------------------------------------------------
#
    # Set the theme if needed
#
    if (!empty($data['object']->properties['theme']->value)) {
        xarTpl::setThemeName($data['object']->properties['theme']->value);
    }
    
    # --------------------------------------------------------
#
    # Set the page template from the pubtype if needed
#
    if (!empty($pubtypeobject->properties['page_template']->value)) {
        $pagename = $pubtypeobject->properties['page_template']->value;
        $position = strpos($pagename, '.');
        if ($position === false) {
            $pagetemplate = $pagename;
        } else {
            $pagetemplate = substr($pagename, 0, $position);
        }
        xarTpl::setPageTemplateName($pagetemplate);
    }
    // It can be overridden by the page itself
    if (!empty($data['object']->properties['page_template']->value)) {
        $pagename = $data['object']->properties['page_template']->value;
        $position = strpos($pagename, '.');
        if ($position === false) {
            $pagetemplate = $pagename;
        } else {
            $pagetemplate = substr($pagename, 0, $position);
        }
        xarTpl::setPageTemplateName($pagetemplate);
    }

    # --------------------------------------------------------
#
    # Cache data for blocks
#
    // Now we can cache all this data away for the blocks.
    // The blocks should have access to most of the same data as the page.
    xarCoreCache::setCached('Blocks.publications', 'pagedata', $tree);

    // The 'serialize' hack ensures we have a proper copy of the
    // paga data, which is a self-referencing array. If we don't
    // do this, then any changes we make will affect the stored version.
    $data = unserialize(serialize($data));

    // Save some values. These are used by blocks in 'automatic' mode.
    xarCoreCache::setCached('Blocks.publications', 'current_id', $id);
    xarCoreCache::setCached('Blocks.publications', 'ptid', $ptid);
    xarCoreCache::setCached('Blocks.publications', 'author', $data['object']->properties['author']->value);

    # --------------------------------------------------------
#
    # Make the properties available to the template
#
    $data['properties'] =& $data['object']->properties;
    
    # --------------------------------------------------------
#
    # Get information on next and previous items
#
    $data['prevpublication'] = xarMod::apiFunc(
        'publications',
        'user',
        'getnext',
        array('id' => $id,
                                       'ptid' => $ptid,
                                       'sort' => 'tree',)
    );
    $data['nextpublication'] = xarMod::apiFunc(
        'publications',
        'user',
        'getnext',
        array('id' => $id,
                                       'ptid' => $ptid,
                                       'sort' => 'tree',)
    );
    return $data;


    /*
        // TEST - highlight search terms
        if(!xarVar::fetch('q',     'str',  $q,     NULL, xarVar::NOT_REQUIRED)) {return;}
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
            $publication = xarMod::apiFunc('publications',
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
        $pubtypes = xarMod::apiFunc('publications','user','get_pubtypes');

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
    if (!isset($show_pubcount)) {
        if (!isset($settings['show_pubcount']) || !empty($settings['show_pubcount'])) {
            $show_pubcount = 1; // default yes
        } else {
            $show_pubcount = 0;
        }
    }
    // show the number of publications for each category
    if (!isset($show_catcount)) {
        if (empty($settings['show_catcount'])) {
            $show_catcount = 0; // default no
        } else {
            $show_catcount = 1;
        }
    }

    // Initialize the data array
    $data = $publication->getFieldValues();
    $data['ptid'] = $ptid; // navigation pubtype
    $data['pubtype_id'] = $pubtype_id; // publication pubtype

// TODO: improve the case where we have several icons :)
    $data['topic_icons'] = '';
    $data['topic_images'] = array();
    $data['topic_urls'] = array();
    $data['topic_names'] = array();
    /*
    if (count($cids) > 0) {
        if (!xarMod::apiLoad('categories', 'user')) return;
        $catlist = xarMod::apiFunc('categories',
                                'user',
                                'getcatinfo',
                                array('cids' => $cids));
        foreach ($catlist as $cat) {
            $link = xarController::URL('publications','user','view',
                             array(//'state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED).
                                   'ptid' => $ptid,
                                   'catid' => $cat['cid']));
            $name = xarVar::prepForDisplay($cat['name']);

            $data['topic_urls'][] = $link;
            $data['topic_names'][] = $name;

            if (!empty($cat['image'])) {
                $image = xarTpl::getImage($cat['image'],'categories');
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
    $themeName = xarCoreCache::getCached('Themes.name', 'CurrentTheme');
    if ($themeName != 'print') {
        if (strstr($publication->properties['body']->value, '<!--pagebreak-->')) {
            if ($preview) {
                $publication['body'] = preg_replace(
                    '/<!--pagebreak-->/',
                    '<hr/><div style="text-align: center;">'.xarML('Page Break').'</div><hr/>',
                    $publication->properties['body']->value
                );
                $data['previous'] = '';
                $data['next'] = '';
            } else {
                $pages = explode('<!--pagebreak-->', $publication->properties['body']->value);

                // For documents with many pages, the pages can be
                // arranged in blocks.
                $pageBlockSize = 10;

                // Get pager information: one item per page.
                $pagerinfo = xarTplPager::getInfo((empty($page) ? 1 : $page), count($pages), 1, $pageBlockSize);

                // Retrieve current page and total pages from the pager info.
                // These will have been normalised to ensure they are in range.
                $page = $pagerinfo['currentpage'];
                $numpages = $pagerinfo['totalpages'];

                // Discard everything but the current page.
                $publication['body'] = $pages[$page - 1];
                unset($pages);

                if ($page > 1) {
                    // Don't count page hits after the first page.
                    xarCoreCache::setCached('Hooks.hitcount', 'nocount', 1);
                }

                // Pass in the pager info so a complete custom pager
                // can be created in the template if required.
                $data['pagerinfo'] = $pagerinfo;

                // Get the rendered pager.
                // The pager template (last parameter) could be an
                // option for the publication type.
                $urlmask = xarController::URL(
                    'publications',
                    'user',
                    'display',
                    array('ptid' => $ptid, 'id' => $id, 'page' => '%%')
                );
                $data['pager'] = xarTplPager::getPager(
                    $page,
                    $numpages,
                    $urlmask,
                    1,
                    $pageBlockSize,
                    'multipage'
                );

                // Next two assignments for legacy templates.
                // TODO: deprecate them?
                $data['next'] = xarTplPager::getPager(
                    $page,
                    $numpages,
                    $urlmask,
                    1,
                    $pageBlockSize,
                    'multipagenext'
                );
                $data['previous'] = xarTplPager::getPager(
                    $page,
                    $numpages,
                    $urlmask,
                    1,
                    $pageBlockSize,
                    'multipageprev'
                );
            }
        } else {
            $data['previous'] = '';
            $data['next'] = '';
        }
    } else {
        $publication['body'] = preg_replace(
            '/<!--pagebreak-->/',
            '',
            $publication['body']
        );
    }

    // Display publication
    unset($publication);

    // temp. fix to include dynamic data fields without changing templates
    if (xarModHooks::isHooked('dynamicdata', 'publications', $pubtype_id)) {
        list($properties) = xarMod::apiFunc(
            'dynamicdata',
            'user',
            'getitemfordisplay',
            array('module'   => 'publications',
                                                'itemtype' => $pubtype_id,
                                                'itemid'   => $id,
                                                'preview'  => $preview)
        );
        if (!empty($properties) && count($properties) > 0) {
            foreach (array_keys($properties) as $field) {
                $data[$field] = $properties[$field]->getValue();
                // POOR mans flagging for transform hooks
                try {
                    $configuration = $properties[$field]->configuration;
                    if (substr($configuration, 0, 10) == 'transform:') {
                        $data['transform'][] = $field;
                    }
                } catch (Exception $e) {
                }
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
    if (!isset($title_transform)) {
        if (empty($settings['title_transform'])) {
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
    $data = xarModHooks::call('item', 'transform', $id, $data, 'publications');

    return xarTpl::module('publications', 'user', 'display', $data);


    if (!empty($data['title'])) {
        // CHECKME: <rabbit> Strip tags out of the title - the <title> tag shouldn't have any other tags in it.
        $title = strip_tags($data['title']);
        xarTpl::setPageTitle(xarVar::prepForDisplay($title), xarVar::prepForDisplay($pubtypes[$data['itemtype']]['description']));

        // Save some variables to (temporary) cache for use in blocks etc.
        xarCoreCache::setCached('Comments.title', 'title', $data['title']);
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
    $data['publinks'] = array(); //xarMod::apiFunc('publications','user','getpublinks',
    //    array('state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED),
    //          'count' => $show_pubcount));
    if (isset($show_map)) {
        $settings['show_map'] = $show_map;
    }
    if (!empty($settings['show_map'])) {
        $data['maplabel'] = xarML('View Publication Map');
        $data['maplink'] = xarController::URL(
            'publications',
            'user',
            'viewmap',
            array('ptid' => $ptid)
        );
    }
    if (isset($show_archives)) {
        $settings['show_archives'] = $show_archives;
    }
    if (!empty($settings['show_archives'])) {
        $data['archivelabel'] = xarML('View Archives');
        $data['archivelink'] = xarController::URL(
            'publications',
            'user',
            'archive',
            array('ptid' => $ptid)
        );
    }
    if (isset($show_publinks)) {
        $settings['show_publinks'] = $show_publinks;
    }
    if (!empty($settings['show_publinks'])) {
        $data['show_publinks'] = 1;
    } else {
        $data['show_publinks'] = 0;
    }
    $data['show_catcount'] = $show_catcount;

    // Tell the hitcount hook not to display the hitcount, but to save it
    // in the variable cache.
    if (xarModHooks::isHooked('hitcount', 'publications', $pubtype_id)) {
        xarCoreCache::setCached('Hooks.hitcount', 'save', 1);
        $data['dohitcount'] = 1;
    } else {
        $data['dohitcount'] = 0;
    }

    // Tell the ratings hook to save the rating in the variable cache.
    if (xarModHooks::isHooked('ratings', 'publications', $pubtype_id)) {
        xarCoreCache::setCached('Hooks.ratings', 'save', 1);
        $data['doratings'] = 1;
    } else {
        $data['doratings'] = 0;
    }


    // Retrieve the current hitcount from the variable cache
    if ($data['dohitcount'] && xarVar::isCached('Hooks.hitcount', 'value')) {
        $data['counter'] = xarCoreCache::getCached('Hooks.hitcount', 'value');
    } else {
        $data['counter'] = '';
    }

    // Retrieve the current rating from the variable cache
    if ($data['doratings'] && xarVar::isCached('Hooks.ratings', 'value')) {
        $data['rating'] = intval(xarCoreCache::getCached('Hooks.ratings', 'value'));
    } else {
        $data['rating'] = '';
    }

    // Save some variables to (temporary) cache for use in blocks etc.
    xarCoreCache::setCached('Blocks.publications', 'title', $data['title']);

    // Generating keywords from the API now instead of setting the entire
    // body into the cache.
    $keywords = xarMod::apiFunc(
        'publications',
        'user',
        'generatekeywords',
        array('incomingkey' => $data['body'])
    );

    xarCoreCache::setCached('Blocks.publications', 'body', $keywords);
    xarCoreCache::setCached('Blocks.publications', 'summary', $data['summary']);
    xarCoreCache::setCached('Blocks.publications', 'id', $id);
    xarCoreCache::setCached('Blocks.publications', 'ptid', $ptid);
    xarCoreCache::setCached('Blocks.publications', 'cids', $cids);
    xarCoreCache::setCached('Blocks.publications', 'owner', $owner);
    if (isset($data['author'])) {
        xarCoreCache::setCached('Blocks.publications', 'author', $data['author']);
    }
    // TODO: add this to publications configuration ?
    //if ($shownavigation) {
    $data['id'] = $id;
    $data['cids'] = $cids;
    xarCoreCache::setCached('Blocks.categories', 'module', 'publications');
    xarCoreCache::setCached('Blocks.categories', 'itemtype', $ptid);
    xarCoreCache::setCached('Blocks.categories', 'itemid', $id);
    xarCoreCache::setCached('Blocks.categories', 'cids', $cids);

    if (!empty($ptid) && !empty($pubtypes[$ptid]['description'])) {
        xarCoreCache::setCached('Blocks.categories', 'title', $pubtypes[$ptid]['description']);
    }

    // optional category count
    if ($show_catcount && !empty($ptid)) {
        $pubcatcount = xarMod::apiFunc(
            'publications',
            'user',
            'getpubcatcount',
                                    // frontpage or approved
                                    array('state' => array(PUBLICATIONS_STATE_FRONTPAGE,PUBLICATIONS_STATE_APPROVED),
                                          'ptid' => $ptid)
        );
        if (!empty($pubcatcount[$ptid])) {
            xarCoreCache::setCached('Blocks.categories', 'catcount', $pubcatcount[$ptid]);
        }
    } else {
        //    xarCoreCache::setCached('Blocks.categories','catcount',array());
    }
    //}

    // Module template depending on publication type
    $template = $pubtypes[$pubtype_id]['name'];

    // Page template depending on publication type (optional)
    // Note : this cannot be overridden in templates
    if (empty($preview) && !empty($settings['page_template'])) {
        xarTpl::setPageTemplateName($settings['page_template']);
    }

    // Specific layout within a template (optional)
    if (isset($layout)) {
        $data['layout'] = $layout;
    }

    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $ptid));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
    $id = xarMod::apiFunc('publications', 'user', 'getranslationid', array('id' => $id));
    $data['object']->getItem(array('itemid' => $id));

    return xarTpl::module('publications', 'user', 'display', $data, $template);
}
