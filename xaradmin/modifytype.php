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

    if (!xarVar::fetch('creating', 'bool', $creating, true, xarVar::NOT_REQUIRED)) {
        return;
    }
    if (!xarVar::fetch('ptid', 'id', $ptid, 0, xarVar::DONT_SET)) {
        return;
    }

    $data = [];

    // Get the itemtype of the page type.
    $type_itemtype = xarMod::apiFunc('xarpages', 'user', 'gettypeitemtype');

    if (!empty($ptid)) {
        // Editing an existing page type

        // We need all pages, but with the current page tree pruned.
        $type = xarMod::apiFunc(
            'xarpages',
            'user',
            'gettype',
            ['ptid' => $ptid, 'dd_flag' => false]
        );

        if (empty($type)) {
            // TODO: raise an error message.
            $msg = xarML('Page type "#(1)" not found.', $ptid);
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;
        }

        // Security: check we are able to modify this page type.
        if (!xarSecurity::check('EditXarpagesPagetype', 1, 'Pagetype', $type['name'])) {
            return;
        }

        $data['func'] = 'modify';

        // The modify hooks for the page type as an item.
        $modifyhooks = xarModHooks::call(
            'item',
            'modify',
            $type['ptid'],
            ['module' => 'xarpages', 'itemtype' => $type_itemtype]
        );

        // Do config hooks for the page type as an item type.
        $confighooks = xarModHooks::call(
            'module',
            'modifyconfig',
            'xarpages',
            ['module' => 'xarpages', 'itemtype' => $type['ptid']]
        );
    } else {
        // Adding a new page type.

        // Get some example page types from the xardata directory.
        $files = [];
        $xml_files = xarMod::apiFunc(
            'dynamicdata',
            'admin',
            'browse',
            ['basedir' => 'modules/xarpages/xardata', 'filetype' => 'xml']
        );
        if (!empty($xml_files)) {
            $files[''] = xarML('-- Predefined --');

            foreach ($xml_files as $xml_file) {
                $type_name = preg_replace('/-def\.xml$/', '', $xml_file);
                $files[$type_name] = $type_name;
            }
        }
        $data['files'] = $files;

        // Security: allowed to create page types?
        if (!xarSecurity::check('AdminXarpagesPagetype', 1, 'Pagetype', 'All')) {
            return;
        }

        // Default data for the page type form.
        $type = [
            'ptid' => null,
            'name' => '',
            'desc' => '',
        ];

        $data['func'] = 'create';
        $data['ptid'] = null;

        // The 'new' modify hooks for the page type as an item.
        $modifyhooks = xarModHooks::call(
            'item',
            'new',
            '',
            ['module' => 'xarpages', 'itemtype' => $type_itemtype]
        );
    }

    // Clear out any empty hooks, trim the remainder.
    foreach ($modifyhooks as $key => $modifyhook) {
        if (trim($modifyhook) == '') {
            unset($modifyhooks[$key]);
        } else {
            $modifyhooks[$key] = trim($modifyhook);
        }
    }
    $data['modifyhooks'] =& $modifyhooks;

    // Clear out any empty hooks, trim the remainder.
    if (isset($confighooks)) {
        foreach ($confighooks as $key => $confighook) {
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
    $data['ptid'] = $type['ptid'];

    // Return output
    return $data;
}
