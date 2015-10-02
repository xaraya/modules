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
                        $linkdata = xarModURL($source['module'],$source['display_type'],$source['display_function']);
                        if (is_array($linkdata)) {
                            // Need a special function here for each module
                            $locationdata = array();
                            if (isset($linkdata['url'])) $locationdata['url'] = $linkdata['url'];
                            if (isset($linkdata['modified'])) $locationdata['modified'] = $linkdata['modified'];
                            $data['locations'][] = $locationdata;
                        } else {
                            // This is a "normal" display function
                            // This does not send last_modified information
                            $data['locations'][] = $link;
                        }
                    break;
                    
                    // Multiple pages
                    case 2:
                        $linkdata = xarMod::apiFunc($source['module'],$source['gen_type'],$source['gen_function']);
                        foreach ($linkdata as $link) {
                            $locationdata = array();
                            if (isset($link['url'])) $locationdata['url'] = $link['url'];
                            if (isset($link['modified'])) $locationdata['modified'] = $link['modified'];
                            $data['locations'][] = $locationdata;
                        }
                    break;
                }
            }
            
            // Empty the table
            sys::import('xaraya.structures.query');
            $tables =& xarDB::getTables();
            $q = new Query('DELETE', $tables['sitemapper_links']);
            $q->run();
            
            $object = DataObjectMaster::getObject(array('name' => 'sitemapper_links'));
            $defaultfields = $object->getFieldValues();
            foreach ($data['locations'] as $location) {    
                if (isset($location['url'])) $defaultfields['location'] = $location['url'];
                if (isset($location['modified'])) $defaultfields['last_modified'] = (int)$location['modified'];
                $object->setFieldValues($defaultfields);
                $object->createItem(array('itemid' => 0));                
            }
        } else {
        }
        return $data;
    }
?>