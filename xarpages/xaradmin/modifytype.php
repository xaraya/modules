<?php

/**
 * Update question group.
 */
function xarpages_admin_modifytype()
{
    if (!xarVarFetch('creating', 'bool', $creating, true, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('ptid', 'id', $ptid, 0, XARVAR_DONT_SET)) {return;}

    $data = array();

    // Get the itemtype of the page type.
    $type_itemtype = xarModAPIfunc('xarpages', 'user', 'gettypeitemtype');

    if (!empty($ptid)) {
        // Editing an existing page type

        // We need all pages, but with the current page tree pruned.
        $type = xarModAPIFunc(
            'xarpages', 'user', 'gettype',
            array('ptid' => $ptid, 'dd_flag' => false)
        );

        if (empty($type)) {
            // TODO: raise an error message.
            echo "NO PAGE TYPE";
            return;
        }

        $data['func'] = 'modify';

        // The modify hooks for the page type as an item.
        $modifyhooks = xarModCallHooks(
            'item', 'modify', $type['ptid'],
            array('module' => 'xarpages', 'itemtype' => $type_itemtype)
        );

        // Do config hooks for the page type as an item type.
        $confighooks = xarModCallHooks(
            'module', 'modifyconfig', 'xarpages',
            array('module' => 'xarpages', 'itemtype' => $type['ptid'])
        );
    } else {
        // Adding a new page type.

        // Default data for the page type form.
        $type = array(
            'ptid' => NULL,
            'name' => '',
            'desc' => ''
        );

        $data['func'] = 'create';
        $data['ptid'] = NULL;

        // The 'new' modify hooks for the page type as an item.
        $modifyhooks = xarModCallHooks(
            'item', 'new', '',
            array('module' => 'xarpages', 'itemtype' => $type_itemtype)
        );
    }

    // Format the hooks if required.
    if (empty($modifyhooks)) {
        $data['modifyhooks'] = '';
    } elseif (is_array($modifyhooks)) {
        $data['modifyhooks'] = trim(join('', $modifyhooks));
    }

    if (empty($confighooks)) {
        $data['confighooks'] = '';
    } elseif (is_array($modifyhooks)) {
        $data['confighooks'] = trim(join('', $confighooks));
    }

    // Pass the page type to the template.
    $data['type'] = $type;
    $data['ptid'] = $type['ptid'];

    // Return output
    return $data;
}

?>