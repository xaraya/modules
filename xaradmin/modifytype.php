<?php

/**
 * File: $Id$
 *
 * Modify or create a page type
 *
 * @package Xaraya
 * @copyright (C) 2004 by Jason Judge
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.academe.co.uk/
 * @author Jason Judge
 * @subpackage xarpages
 * @todo Warn user if there is not default template when the page type is created
 * @todo Check the page type name is unique when creating and modifying; make a better error
 */

function xarpages_admin_modifytype($args)
{
    extract($args);

    if (!xarVarFetch('creating', 'bool', $creating, true, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('id', 'id', $id, 0, XARVAR_DONT_SET)) {return;}

    $data = array();

    // Get the itemtype of the page type.
    $type_itemtype = xarModAPIfunc('xarpages', 'user', 'gettypeitemtype');

    if (!empty($id)) {
        // Editing an existing page type

        // We need all pages, but with the current page tree pruned.
        $type = xarModAPIFunc(
            'xarpages', 'user', 'get_type',
            array('id' => $id, 'dd_flag' => false)
        );

        if (empty($type)) {
            // TODO: raise an error message.
            $msg = xarML('Page type "#(1)" not found.', $id);
            throw new BadParemeterException(null,$msg);
        }

        // Security: check we are able to modify this page type.
        if (!xarSecurityCheck('EditXarpagesPagetype', 1, 'Pagetype', $type['name'])) {
            return;
        }

        $data['func'] = 'modify';

        // The modify hooks for the page type as an item.
        $modifyhooks = xarModCallHooks(
            'item', 'modify', $type['id'],
            array('module' => 'xarpages', 'itemtype' => $type_itemtype)
        );

        // Do config hooks for the page type as an item type.
        $confighooks = xarModCallHooks(
            'module', 'modifyconfig', 'xarpages',
            array('module' => 'xarpages', 'itemtype' => $type['id'])
        );
    } else {
        // Adding a new page type.

        // Get some example page types from the xardata directory.
        $files = array();
        $xml_files = xarModAPIFunc(
            'dynamicdata', 'admin', 'browse',
            array('basedir' => 'modules/xarpages/xardata', 'filetype' => 'xml')
        );
        if (!empty($xml_files)) {
            foreach($xml_files as $xml_file) {
                $type_name = preg_replace('/-def\.xml$/', '', $xml_file);
                $files[] = array('id' => $type_name, 'name' => $type_name);
            }
        }
        $data['files'] = $files;

        // Security: allowed to create page types?
        if (!xarSecurityCheck('AdminXarpagesPagetype', 1, 'Pagetype', 'All')) return;

        // Default data for the page type form.
        $type = array(
            'id' => NULL,
            'name' => '',
            'description' => ''
        );

        // Default values for access
        sys::import('modules.dynamicdata.class.properties.master');
        $accessproperty = DataPropertyMaster::getProperty(array('name' => 'access'));
        $type['info']['add_access'] = $accessproperty->value;

        $data['func'] = 'create';
        $data['id'] = NULL;

        // The 'new' modify hooks for the page type as an item.
        $modifyhooks = xarModCallHooks(
            'item', 'new', '',
            array('module' => 'xarpages', 'itemtype' => $type_itemtype)
        );
    }

    // Clear out any empty hooks, trim the remainder.
    if (isset($modifyhooks)) {
        foreach($modifyhooks as $key => $modifyhook) {
            if (trim($modifyhook) == '') {
                unset($modifyhooks[$key]);
            } else {
                $modifyhooks[$key] = trim($modifyhook);
            }
        }
        $data['modifyhooks'] =& $modifyhooks;
    }

    // Clear out any empty hooks, trim the remainder.
    if (isset($confighooks)) {
        foreach($confighooks as $key => $confighook) {
            if (trim($confighook) == '') {
                unset($confighooks[$key]);
            } else {
                $confighooks[$key] = trim($confighook);
            }
        }
        $data['confighooks'] =& $confighooks;
    }

    // Pass the page type to the template.
    $data['type'] = $type;
    $data['id'] = $type['id'];

    // Return output
    return $data;
}

?>
