<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2011 Netspan AG
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

function publications_user_display($args)
{
    // Get parameters from user
// this is used to determine whether we come from a pubtype-based view or a
// categories-based navigation
    if(!xarVarFetch('name',    'str',   $name,  '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('ptid',   'id',    $ptid,  NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemid',  'id',    $id,    NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('page',    'int:1', $page,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    
    // Override xarVarFetch
    extract ($args);
    
    // If no id supplied, get the default id.
    if (empty($id)) $id = xarModVars::get('publications', 'defaultpage');
    $id = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $id));
    
    // If the document doesn't exist, we might be viewing a pubtype
    if (empty($name) && empty($ptid) && empty($id)) {
        // Nothing to be done
        $id = xarModVars::get('publications', 'notfoundpage');
    } elseif (empty($id)) {
        // We're missing an id but can get a pubtype: jump to the pubtype view
        xarController::redirect(xarModURL('publications','user','view'));
    }
    
    // We have come to the end of the line
    if (empty($id)) return xarResponse::NotFound();
    
    // Here we get the publication type first, and then from that the page
    // Perhaps more efficient to get the page directly?
    $ptid = xarMod::apiFunc('publications','user','getitempubtype',array('itemid' => $id));

    // An empty publication type means the page does not exist
    if (empty($ptid)) return xarResponse::NotFound();
    
/*    if(empty($ptid)) {
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
    $id = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $id));
    $itemid = $data['object']->getItem(array('itemid' => $id));
    
# --------------------------------------------------------
#
# If this is a redirect page, then send it on its way now
#
    if ($data['object']->properties['redirect_flag']->value) {
    
        // If this is from a link of a redirect child page, use the child param as new URL
        if(!xarVarFetch('child',    'str', $child,  NULL, XARVAR_NOT_REQUIRED)) {return;}
        if (!empty($child)) {
            // Turn entities into amps
            $url = urldecode($child);
        } else {
            $url = $data['object']->properties['redirect_url']->value;
        }
        $params = parse_url($url);
        $params['query'] = preg_replace('/&amp;/','&',$params['query']);
        
        // If this is an external link, show it without further processing
        if (!empty($params['host']) && $params['host'] != xarServer::getHost() && $params['host'].":".$params['port'] != xarServer::getHost()) {
            xarController::redirect($url);
        } else{
            parse_str($params['query'], $info);
            $other_params = $info;
            unset($other_params['module']);
            unset($other_params['type']);
            unset($other_params['func']);
            unset($other_params['child']);
            $page = xarMod::guiFunc($info['module'],'user',$info['func'],$other_params);
            
            // Debug
            // echo xarModURL($info['module'],'user',$info['func'],$other_params);
# --------------------------------------------------------
#
# The transform of the subordinate function's template
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

    // Get the complete tree for this section of pages.
    // We need this for blocks etc.
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
    
# --------------------------------------------------------
#
# Additional data
#
    // Specific layout within a template (optional)
    $data['layout'] = isset($layout) ? $layout : 'detail';
    
    // Get the settings for this publication type;
    $data['settings'] = xarModAPIFunc('publications','user','getsettings',array('ptid' => $ptid));
    
    // The name of this object
    $data['objectname'] = $data['object']->name;
    
# --------------------------------------------------------
#
# Set the theme if needed
#
    if (!empty($data['object']->properties['theme']->value)) xarTplSetThemeName($data['object']->properties['theme']->value);
    
# --------------------------------------------------------
#
# Set the page template from the pubtype if needed
#
    if (!empty($data['settings']['page_template'])) {
        $pagename = $data['settings']['page_template'];
        $position = strpos($pagename,'.');
        if ($position === false) {
            $pagetemplate = $pagename;
        } else {
            $pagetemplate = substr($pagename,0,$position);
        }
        xarTpl::setPageTemplateName($pagetemplate);
    }
    // It can be overridden by the page itself
    if (!empty($data['object']->properties['page_template']->value)) {
        $pagename = $data['object']->properties['page_template']->value;
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
# Cache data for blocks
#
    // Now we can cache all this data away for the blocks.
    // The blocks should have access to most of the same data as the page.
    xarVarSetCached('Blocks.publications', 'pagedata', $tree);

    // The 'serialize' hack ensures we have a proper copy of the
    // paga data, which is a self-referencing array. If we don't
    // do this, then any changes we make will affect the stored version.
    $data = unserialize(serialize($data));

    // Save some values. These are used by blocks in 'automatic' mode.
    xarVarSetCached('Blocks.publications', 'current_id', $id);
    xarVarSetCached('Blocks.publications', 'ptid', $ptid);
    xarVarSetCached('Blocks.publications', 'author', $data['object']->properties['author']->value);

    return $data;
}

?>