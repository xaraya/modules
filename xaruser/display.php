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
    $data['object']->getItem(array('itemid' => $id));
    $publication = $data['object']->getFieldValues();

    // This function displays the detail layout
    $data['layout'] = 'detail';
    
    // Set the theme if needed
    if (!empty($data['object']->properties['theme']->value)) xarTplSetThemeName($data['object']->properties['theme']->value);
    
    // Set the page template if needed
    if (!empty($data['object']->properties['page_template']->value)) xarTplSetPageTemplateName($data['object']->properties['page_template']->value);

    return xarTplModule('publications', 'user', 'display', $data);
}

?>
