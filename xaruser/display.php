<?php
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
 * @param int itemid or id
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

function publications_user_display($args)
{
    // Get parameters from user
// this is used to determine whether we come from a pubtype-based view or a
// categories-based navigation
// Note we support both id and itemid
    if(!xarVarFetch('name',      'str',   $name,  '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('ptid',     'id',    $ptid,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemid',    'id',    $itemid,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('id',        'id',    $id,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('page',      'int:1', $page,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('translate', 'int:1', $translate,  1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('layout',    'str:1', $layout,  'detail', XARVAR_NOT_REQUIRED)) {return;}
    
    // Override xarVarFetch
    extract ($args);
    
    // The itemid var takes precedence if it exiata
    if (isset($itemid)) $id = $itemid;
    
# --------------------------------------------------------
#
# If no ID supplied, try getting the id of the default page.
#
    if (empty($id)) $id = xarModVars::get('publications', 'defaultpage');

# --------------------------------------------------------
#
# Get the ID of the translation if required
#
    // First save the "untranslated" id for blocks etc.
    xarCoreCache::setCached('Blocks.publications', 'current_base_id', $id);

    if ($translate) {
        $id = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $id));
        /*
        $newid = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $id));
        if ($newid != $id) {
            // We do a full redirect rather than just continuing with the new id so that 
            // anything working off the itemid of the page to be displayed will automatically 
            // use the new one
            xarController::redirect(xarModURL('publications', 'user', 'display', array('itemid' => $newid, 'translate' => 0)));
        }
        */
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
        xarController::redirect(xarModURL('publications','user','view'));
    }
    
# --------------------------------------------------------
#
# If still no ID, we have come to the end of the line
#
    if (empty($id)) return xarResponse::NotFound();
    
# --------------------------------------------------------
#
# We have an ID, now first get the page
#
    // Here we get the publication type first, and then from that the page
    // Perhaps more efficient to get the page directly?
    $ptid = xarMod::apiFunc('publications','user','getitempubtype',array('itemid' => $id));

    // An empty publication type means the page does not exist
    if (empty($ptid)) return xarResponse::NotFound();
    
    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $ptid));

    // A non-active publication type means the page does not exist
    if ($pubtypeobject->properties['state']->value < PUBLICATIONS_STATE_ACTIVE) return xarResponse::NotFound();

    // Save this as the current pubtype
    xarCoreCache::setCached('Publications', 'current_pubtype_object', $pubtypeobject);
    
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
//    $id = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $id));
    $itemid = $data['object']->getItem(array('itemid' => $id));
    
# --------------------------------------------------------
#
# Are we allowed to see this page?
#
    $accessconstraints = xarMod::apiFunc('publications', 'admin', 'getpageaccessconstraints', array('property' => $data['object']->properties['access']));
    $access = DataPropertyMaster::getProperty(array('name' => 'access'));
    $allow = $access->check($accessconstraints['display']);
    $nopublish = (time() < $data['object']->properties['start_date']->value) || ((time() > $data['object']->properties['end_date']->value) && !$data['object']->properties['no_end']->value);
    
    // If no access, then bail showing a forbidden or the "no permission" page or an empty page
    $nopermissionpage_id = xarModVars::get('publications', 'noprivspage');
    if (!$allow || $nopublish) {
        if ($accessconstraints['display']['failure']) return xarResponse::Forbidden();
        elseif ($nopermissionpage_id) xarController::redirect(xarModURL('publications', 'user', 'display', array('itemid' => $nopermissionpage_id)));
        else return xarTplModule('publications', 'user', 'empty');
    }
    
    // If we use process states, then also check that
    if (xarModVars::get('publications', 'use_process_states')) {
        if ($data['object']->properties['process_state']->value < 3)
            if ($accessconstraints['display']['failure']) return xarResponse::Forbidden();
            elseif ($nopermissionpage_id) xarController::redirect(xarModURL('publications', 'user', 'display', array('itemid' => $nopermissionpage_id)));
            else return xarTplModule('publications', 'user', 'empty');            
    }

# --------------------------------------------------------
#
# If this is a redirect page, then send it on its way now
#
    $redirect_type = $data['object']->properties['redirect_flag']->value;
    if ($redirect_type == 1) {
        // This is a simple redirect to another page
        $url = $data['object']->properties['redirect_url']->value; 
        if (empty($url)) return xarResponse::NotFound();
        try {
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
        if(!xarVarFetch('child',    'str', $child,  NULL, XARVAR_NOT_REQUIRED)) {return;}
        if (!empty($child)) {
            // This page was submitted
            // Turn entities into amps
            $url = urldecode($child);
        } else {
            // Get the proxy URL to redirect to
            $url = $data['object']->properties['proxy_url']->value;
        }
        
        // Bail if the URL is bad
        if (empty($url)) return xarResponse::NotFound();
        try {
            // Check if this is a Xaraya function
            $pos = strpos($url, 'xar');
            if ($pos === 0) {
                eval('$url = ' . $url .';');
            }
            
            // Parse the URL to get host and port
            // we can use a simple parse_url() in this case
            $params = parse_url($url);
        } catch (Exception $e) {
            return xarResponse::NotFound();
        }
        
        // If this is an external link, show it without further processing
        if (!empty($params['host']) && $params['host'] != xarServer::getHost() && $params['host'].":".$params['port'] != xarServer::getHost()) {
            xarController::redirect($url, 301);
        } elseif (strpos(xarServer::getCurrentURL(),$url) === 0) {
            // CHECKME: is this robust enough?
            // Redirect to avoid recursion if $url is already our present URL
            xarController::redirect($url, 301);
        } else{
            // This is a local URL. We need to parse it, but parse_url is no longer good enough here
            $request = new xarRequest($url);
            $router = new xarRouter();
            $router->route($request);
            $request->setRoute($router->getRoute());
            $dispatcher = new xarDispatcher();
            $controller = $dispatcher->findController($request);
            $controller->actionstring = $request->getActionString();
            $args = $controller->decode() + $request->getFunctionArgs();
            $controller->chargeRequest($request, $args);

            try {
                $page = xarMod::guiFunc($request->getModule(),'user',$request->getFunction(),$request->getFunctionArgs());
            } catch (Exception $e) {
                return xarResponse::NotFound();
            }
            
            // Debug
            // echo xarModURL($info['module'],'user',$info['func'],$other_params);
# --------------------------------------------------------
#
# For proxy pages: the transform of the subordinate function's template
#
            // Find the URLs in submits
            $pattern='/(action)="([^"\r\n]*)"/';
            preg_match_all($pattern,$page,$matches);
            $pattern = array();
            $replace = array();
            foreach ($matches[2] as $match) {
                $pattern[] = '%</form%';
                $replace[] = '<input type="hidden" name="return_url" id="return_url" value="' . urlencode(xarServer::getCurrentURL()) . '"/><input type="hidden" name="child" value="' . urlencode($match) . '"/></form';
            }
            $page = preg_replace($pattern,$replace,$page);

            $pattern='/(action)="([^"\r\n]*)"/';
            $page = preg_replace_callback($pattern,
                create_function(
                    '$matches',
                    'return $matches[1]."=\"".xarServer::getCurrentURL()."\"";'
                ),
                $page
            );

            // Find the URLs in links
            $pattern='/(href)="([^"\r\n]*)"/';
            $page = preg_replace_callback($pattern,
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
# If this is a blocklayout page, then process it
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
            if (in_array(substr($prop->source, $prefix), $sourcefields)) $fields[] = $prop->name;
        }

        // Run each template field through the compiler
        foreach ($fields as $field) {
            try{        
                $tplString  = '<xar:template xmlns:xar="http://xaraya.com/2004/blocklayout">';
                $tplString .= xarMod::apiFunc('publications','user','prepareforbl',array('string' => $data['object']->properties[$field]->value));

                $tplString .= '</xar:template>';

                $tplString = $blCompiler->compilestring($tplString);
                // We don't allow passing $data to the template for now
                $tpldata = array();
                $tplString = xarTplString($tplString, $tpldata);
            } catch(Exception $e) {
                var_dump($tplString);
            }
            $data['object']->properties[$field]->value = $tplString;
        }
    }

# --------------------------------------------------------
#
# Get the complete tree for this section of pages. We need this for blocks etc.
#
/*
    $tree = xarMod::apiFunc(
        'publications', 'user', 'getpagestree',
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
            foreach($tree['pages'][$id]['child_keys'] as $scan_key) {
                // If the page is displayable, then treat it as the new page.
                if ($tree['pages'][$scan_key]['status'] == 3 || $tree['pages'][$scan_key]['status'] == 4) {
                    $id = $tree['pages'][$scan_key]['id'];
                    $id = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $id));
                    $itemid = $data['object']->getItem(array('itemid' => $id));
                    break;
                }
            }
        }
    }
*/    
# --------------------------------------------------------
#
# Additional data
#
    // Pass the layout to the template
    $data['layout'] = $layout;

    // Get the settings for this publication type
    $data['settings'] = xarMod::apiFunc('publications','user','getsettings',array('ptid' => $ptid));
    
    // The name of this object
    $data['objectname'] = $data['object']->name;

    // Pass the access rules of the publication type to the template
    $data['pubtype_access'] = $pubtypeobject->properties['access']->getValue();
    xarCoreCache::setCached('Publications', 'pubtype_access', $data['pubtype_access']);
    
# --------------------------------------------------------
#
# Set the theme if needed
#
    if (!empty($data['object']->properties['theme']->value)) xarTplSetThemeName($data['object']->properties['theme']->value);
    
# --------------------------------------------------------
#
# Set the page template from the pubtype if needed
#
    $pagename = $pubtypeobject->properties['page_template']->value;
    if (!empty($pagename)  && ($pagename != 'admin.xt')){
        $position = strpos($pagename,'.');
        if ($position === false) {
            $pagetemplate = $pagename;
        } else {
            $pagetemplate = substr($pagename,0,$position);
        }
        xarTpl::setPageTemplateName($pagetemplate);
    }
    // It can be overridden by the page itself
    $pagename = $data['object']->properties['page_template']->value;
    if (!empty($pagename)  && ($pagename != 'admin.xt')){
        $position = strpos($pagename,'.');
        if ($position === false) {
            $pagetemplate = $pagename;
        } else {
            $pagetemplate = substr($pagename,0,$position);
        }
        xarTpl::setPageTemplateName($pagetemplate);
    }

# --------------------------------------------------------
#
# Do the same for page title, page description and keywords
# The values (if any) are then passed to the meta tag in the template
#
    // Page title
    if (!empty($pubtypeobject->properties['page_title']->value)) {
        $data['page_title'] = $pubtypeobject->properties['page_title']->value;
    }
    // It can be overridden by the page itself
    if (!empty($data['object']->properties['page_title']->value)) {
        $data['page_title'] = $data['object']->properties['page_title']->value;
    }
    // If nothing then the setting from the themes module will be used, so we pass this page's title
    if (empty($data['page_title'])) $data['page_title'] = $data['object']->properties['title']->value;

    // Page description
    if (!empty($pubtypeobject->properties['page_description']->value)) {
        $data['page_description'] = $pubtypeobject->properties['page_description']->value;
    }
    // It can be overridden by the page itself
    if (!empty($data['object']->properties['page_description']->value)) {
        $data['page_description'] = $data['object']->properties['page_description']->value;
    }

    // Page keywords
    if (!empty($pubtypeobject->properties['keywords']->value)) {
        $data['keywords'] = $pubtypeobject->properties['keywords']->value;
    }
    // It can be overridden by the page itself
    if (!empty($data['object']->properties['keywords']->value)) {
        $data['keywords'] = $data['object']->properties['keywords']->value;
    }
# --------------------------------------------------------
#
# Cache data for blocks
#
    // Now we can cache all this data away for the blocks.
    // The blocks should have access to most of the same data as the page.
//    xarCoreCache::setCached('Blocks.publications', 'pagedata', $tree);

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
    if ($data['settings']['show_prevnext']) {
        $prevpublication = xarMod::apiFunc('publications','user','getprevious',
                                     array('id' => $itemid,
                                           'ptid' => $data['object']->properties['itemtype']->value,
                                           'sort' => 'title',));
        $nextpublication = xarMod::apiFunc('publications','user','getnext',
                                     array('id' => $itemid,
                                           'ptid' => $data['object']->properties['itemtype']->value,
                                           'sort' => 'title',));
    } else {
        $prevpublication = '';
        $nextpublication = '';
    }
    xarCoreCache::setCached('Publications', 'prevpublication', $prevpublication);
    xarCoreCache::setCached('Publications', 'nextpublication', $nextpublication);

    return $data;
}

?>