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

function publications_admin_view_pages($args)
{

   if (!xarSecurityCheck('ManagePublications')) return;

    extract($args);

   // Accept a parameter to allow selection of a single tree.
    xarVarFetch('root_id', 'int', $root_id, NULL, XARVAR_NOT_REQUIRED);

   if (NULL === $root_id) {
        $root_id = xarSession::getVar('publications_root_id');
        if (empty($root_id)) $root_id = 0;
    }
    xarSession::setVar('publications_root_id', $root_id);

   $data = xarMod::apiFunc(
        'publications', 'user', 'getpagestree',
        array('key' => 'index', 'dd_flag' => false, 'tree_contains_id' => $root_id)
    );
    echo "<pre>";var_dump($data);exit;

   if (empty($data['pages'])) {
        // TODO: pass to template.
        return $data; //xarML('NO PAGES DEFINED');
    } else {
        $data['pages'] = xarMod::apiFunc('publications', 'tree', 'array_maptree', $data['pages']);
    }

   $data['root_id'] = $root_id;

   // Check modify and delete privileges on each page.
    // EditPage - allows basic changes, but no moving or renaming (good for sub-editors who manage content)
    // AddPage - new pages can be added (further checks may limit it to certain page types)
    // DeletePage - page can be renamed, moved and deleted
    if (!empty($data['pages'])) {
        // Bring in the access property for security checks
        sys::import('modules.dynamicdata.class.properties.master');
        $accessproperty = DataPropertyMaster::getProperty(array('name' => 'access'));
        $accessproperty->module = 'publications';
        $accessproperty->component = 'Page';
        foreach($data['pages'] as $key => $page) {

            $thisinstance = $page['name'] . ':' . $page['pubtype_name'];

            // Do we have admin access?
            $args = array(
                'instance' => $thisinstance,
                'level' => 800,
            );
            $adminaccess = $accessproperty->check($args);

            // Decide whether this page can be modified by the current user
            /*try {
                $args = array(
                    'instance' => $thisinstance,
                    'group' => $page['access']['modify_access']['group'],
                    'level' => $page['access']['modify_access']['level'],
                );
            } catch (Exception $e) {
                $args = array();
            }*/
            $data['pages'][$key]['edit_allowed'] = $adminaccess || $accessproperty->check($args);
            /*
            // Decide whether this page can be deleted by the current user
           try {
                $args = array(
                    'instance' => $thisinstance,
                    'group' => $page['access']['delete_access']['group'],
                    'level' => $page['access']['delete_access']['level'],
                );
            } catch (Exception $e) {
                $args = array();
            }*/
            $data['pages'][$key]['delete_allowed'] = $adminaccess ||  $accessproperty->check($args);
        }
    }

    // Flag this as the current list view
    xarSession::setVar('publications_current_listview', xarServer::getCurrentURL());
    
    return $data;
}

?>