<?php
/**
 * Sitemapper Module
 *
 * @package modules
 * @subpackage sitemapper module
 * @category Third Party Xaraya Module
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

    sys::import('modules.dynamicdata.class.objects.master');

    function sitemapper_admin_view_links() 
    {
        if (!xarVarFetch('regenerate', 'int', $data['regenerate'], 0, XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
        if ($data['regenerate']) {
            $object = DataObjectMaster::getObjectList(array('name' => 'sitemapper_sources'));
            $where = 'state = 3';
            $data['items'] = $object->getItems(array('where' => $where));
            // Run through all the sources entries and get the locations to be included in the file
            $data['locations'] = array();
            
            foreach ($data['items'] as $key => $source){
            
                // Ignore modules that are not active
                if (!xarModIsAvailable($source['module'])) continue;
                
                // Get the links of this source
                switch ($source['source_type']) {
                
                    // Single page
                    case 1:
                        $link = xarModURL($source['module'],$source['display_type'],$source['display_function']);
                        $data['locations'][] = $link;
                    break;
                    
                    // Multiple pages
                    case 2:
                        $links = xarMod::apiFunc($source['module'],$source['gen_type'],$source['gen_function']);
                        foreach ($links as $link) {
                            $data['locations'][] = $link['url'];
                        }
                    break;
                }
            }
            
            // Empty the table
            sys::import('xaraya.structures.query');
            $tables = xarDB::getTables();
            $q = new Query('DELETE', $tables['sitemapper_links']);
            $q->run();
            
            // Lazy man's empty
            $object = DataObjectMaster::getObject(array('name' => 'sitemapper_links'));
            $defaultfields = $object->getFieldValues();
            foreach ($data['locations'] as $location) {
                
                // See if we alrady have this page in the DB
//                $objectlist = DataObjectMaster::getObjectList(array('name' => 'sitemapper_links'));
//                $where = "location = '" . $location . "'";
//                $item = $objectlist->getItems(array('where' => $where));
    
//                if (empty($item)) {
                    $defaultfields['location'] = $location;
                    $object->setFieldValues($defaultfields);
                    $object->createItem(array('itemid' => 0));
//                } else {
                    // Just jump for now
//                    continue;
//                    $object->setFieldValues(current($item));
//                    $object->updateItem();
//                }
                
            }
        } else {
        }
        return $data;
    }
?>