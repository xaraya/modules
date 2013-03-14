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

sys::import('modules.dynamicdata.class.objects.master');

function publications_admin_display_version($args)
{
    if (!xarSecurityCheck('ManagePublications')) return;
    
    if (!xarVarFetch('itemid',  'id',    $data['page_id'], 0,  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('name',    'str',   $data['objectname'], '',  XARVAR_NOT_REQUIRED)) {return;}
    if (empty($data['page_id'])) return xarResponse::NotFound();
    
    sys::import('modules.dynamicdata.class.objects.master');
    $entries = DataObjectMaster::getObjectList(array('name' => 'publications_versions'));
    $entries->dataquery->eq('page_id', $data['page_id']);
    $data['versions'] = $entries->countItems();
    
    if ($data['versions'] < 1) return $data;
    
    if (!xarVarFetch('confirm',  'int',    $confirm, 1,  XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('version_1',  'int',    $version_1, $data['versions'],  XARVAR_NOT_REQUIRED)) {return;}
    $data['version_1'] = $version_1;
        
    // Get the content data for the display
    $version = DataObjectMaster::getObjectList(array('name' => 'publications_versions'));
    $version->dataquery->eq('page_id', $data['page_id']);
    $version->dataquery->eq('version_number', $version_1);
    $items = $version->getItems();
    if (count($items) > 1)
        throw new Exception(xarML('More than one instance with the version number #(1)', $version_1));
    $item = current($items);
    $content_array_1 = unserialize($item['content']);

    // Get an empty object for the page data
    $pubtype = DataObjectMaster::getObject(array('name' => 'publications_types'));
    $pubtype->getItem(array('itemid' => $content_array_1['itemtype']));
    $page = DataObjectMaster::getObject(array('name' => $pubtype->properties['name']->value));
    $page->tplmodule = 'publications';
    $page->layout = 'publications_documents';
    
    // Load the data into its object
    $page->setFieldValues($content_array_1, 1);

    if ($confirm == 1) {
        // Now in turn get the actual display
        $data['content'] = $page->showDisplay();
        // Assemple options for the version dropdowns
        $data['options'] = array();
        for ($i=$data['versions'];$i>=1;$i--) 
            $data['options'][] = array('id' => $i, 'name' => $i);
        
    } elseif ($confirm == 2) {
        $page->properties['version']->value = $data['versions'] + 1;
        $page->updateItem();
        
        xarController::redirect(xarModURL('publications', 'admin', 'modify', array('name' => $pubtype->properties['name']->value, 'itemid' => $content_array_1['id'])));
        return true;
    }
    return $data;
}

?>