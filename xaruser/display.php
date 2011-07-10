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
    // Get parameters from user
// this is used to determine whether we come from a pubtype-based view or a
// categories-based navigation
    if(!xarVarFetch('name',    'str',   $name,  '', XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('ptid',   'isset', $ptid, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('itemid',      'id',    $id,   NULL, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('page', 'int:1', $page,  NULL, XARVAR_NOT_REQUIRED)) {return;}
    
    // Override xarVarFetch
    extract ($args);
    
    if (empty($name) && empty($ptid)) return xarResponse::NotFound();

    if(empty($ptid)) {
        $publication_type = DataObjectMaster::getObjectList(array('name' => 'publications_types'));
        $where = 'name = ' . $name;
        $items = $publication_type->getItems(array('where' => $where));
        $item = current($items);
        $ptid = $item['id'];
    }
    
    $pubtypeobject = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtypeobject->getItem(array('itemid' => $ptid));
    $data['object'] = DataObjectMaster::getObject(array('name' => $pubtypeobject->properties['name']->value));
    
    $id = xarMod::apiFunc('publications','user','gettranslationid',array('id' => $id));
    $itemid = $data['object']->getItem(array('itemid' => $id));
    
    // If the document doesn't exist, bail
    if (empty($itemid)) return xarResponse::NotFound();
    
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
    
    $publication = $data['object']->getFieldValues();

    // Specific layout within a template (optional)
    if (isset($layout)) {
        $data['layout'] = $layout;
    } else {
        $data['layout'] = 'detail';
    }
    
    // Set the theme if needed
    if (!empty($data['object']->properties['theme']->value)) xarTplSetThemeName($data['object']->properties['theme']->value);
    
    // Set the page template if needed
    if (!empty($data['object']->properties['page_template']->value)) xarTplSetPageTemplateName($data['object']->properties['page_template']->value);

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