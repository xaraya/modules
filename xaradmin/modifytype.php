<?php

/**
 * Update page type.
 * @todo warn user if there is no 'page-{name}' fallback template available.
 * @todo the page type name must be unique - check this when updating and creating.
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
            $msg = xarML('Page type "#(1)" not found.', $ptid);
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;
        }

        // Security: check we are able to modify this page type.
        if (!xarSecurityCheck('EditPagetype', 1, 'Pagetype', $type['name'])) {
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

        // Security: allowed to create page types?
        if (!xarSecurityCheck('AdminPagetype', 1, 'Pagetype', 'All')) {
            return;
        }

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

    // Clear out any empty hooks, trim the remainder.
    foreach($modifyhooks as $key => $modifyhook) {
        if (trim($modifyhook) == '') {
            unset($modifyhooks[$key]);
        } else {
            $modifyhooks[$key] = trim($modifyhook);
        }
    }
    $data['modifyhooks'] =& $modifyhooks;

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
    $data['ptid'] = $type['ptid'];

    // Return output
    return $data;
}

?>