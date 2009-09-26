<?php

/**
 * File: $Id$
 *
 * Admin view of all pages, in hierarchical format.
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 * @todo Support a pager of sorts, or allow display to be limited to specific sub-trees.
 */

function xarpages_admin_viewpages($args)
{
    extract($args);

    // Security check
    if (!xarSecurityCheck('ModerateXarpagesPage', 1, 'Page', 'All')) {
        // No privilege for viewing pages.
        return false;
    }

    // Accept a parameter to allow selection of a single tree.
    xarVarFetch('contains', 'id', $contains, 0, XARVAR_NOT_REQUIRED);

    $data = xarMod::apiFunc(
        'xarpages', 'user', 'getpagestree',
        array('key' => 'index', 'dd_flag' => false, 'tree_contains_pid' => $contains)
    );

    if (empty($data['pages'])) {
        // TODO: pass to template.
        return $data; //xarML('NO PAGES DEFINED');
    } else {
        $data['pages'] = xarMod::apiFunc('xarpages', 'tree', 'array_maptree', $data['pages']);
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
        $accessproperty->module = 'xarpages';
        $accessproperty->component = 'Page';
        foreach($data['pages'] as $key => $page) {

            $thisinstance = $page['name'] . ':' . $page['pagetype']['name'];

            // Do we have admin access?
            $args = array(
                'instance' => $thisinstance,
                'level' => 800,
            );
            $adminaccess = $accessproperty->check($args);

            // Decide whether this page can be modified by the current user
            $args = array(
                'instance' => $thisinstance,
                'group' => $page['info']['modify_access']['group'],
                'level' => $page['info']['modify_access']['level'],
            );
            $data['pages'][$key]['edit_allowed'] = $adminaccess || $accessproperty->check($args);

            // Decide whether this page can be deleted by the current user
            $args = array(
                'instance' => $thisinstance,
                'group' => $page['info']['delete_access']['group'],
                'level' => $page['info']['delete_access']['level'],
            );
            $data['pages'][$key]['delete_allowed'] = $adminaccess ||  $accessproperty->check($args);
        }
    }

    // Check if the user is allowed to add pages.
    if (xarSecurityCheck('AddXarpagesPage', 0, 'Page', 'All')) {
        $data['add_allowed'] = true;
    }

    return $data;
}

?>