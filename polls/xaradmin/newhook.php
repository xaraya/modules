<?php

/**
 * create an entry for a module item - hook for ('item','new','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns string
 * @return hook output in HTML
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function polls_admin_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'admin', 'newhook', 'polls');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!isset($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object ID', 'admin', 'newhook', 'polls');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    // When called via hooks, the module name may be empty, so we get it from
    // the current module
    if (empty($extrainfo['module'])) {
        $modname = xarModGetName();
    } else {
        $modname = $extrainfo['module'];
    }

    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'newhook', 'polls');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    if (!empty($extrainfo['itemtype']) && is_numeric($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid']) && is_numeric($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }
    if (empty($itemid)) {
        $itemid = 0;
    }

// TODO: security check based on calling module + item type + item id ?
    if (!xarSecurityCheck('EditPolls',0)) {
        return '';
    }

    $optcount = xarModGetVar('polls', 'defaultopts');
    if (empty($optcount)) {
        $optcount = 6;
    }

    if (isset($extrainfo['poll'])) {
        $poll = $extrainfo['poll'];
    } else {
        $poll = xarVarCleanFromInput('poll');
    }
    if (empty($poll)) {
        $poll = array('title' => '',
                      'type' => 'single',
                      'private' => '',
                      'options' => array());
        for ($i = 1; $i <= $optcount; $i++) {
            $poll['options'][$i] = '';
        }
    }

    return xarTplModule('polls','admin','newhook',
                        array('optcount' => $optcount,
                              'poll' => $poll));
}

?>
