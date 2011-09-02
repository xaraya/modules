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

function publications_admin_view_pages($args)
{
    extract($args);

    if (!xarSecurityCheck('ManagePublications')) return;

    // Accept a parameter to allow selection of a single tree.
    xarVarFetch('contains', 'id', $contains, 0, XARVAR_NOT_REQUIRED);

    $data = xarMod::apiFunc(
        'publications', 'user', 'getpagestree',
        array('key' => 'index', 'dd_flag' => false, 'tree_contains_pid' => $contains)
    );

    if (empty($data['pages'])) {
        // TODO: pass to template.
        return $data; //xarML('NO PAGES DEFINED');
    } else {
        $data['pages'] = xarMod::apiFunc('publications', 'tree', 'array_maptree', $data['pages']);
    }

    $data['contains'] = $contains;

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

            $thisinstance = $page['name'] . ':' . $page['ptid']['name'];

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