<?php
/**
 * create an entry for a module item - hook for ('item','create','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns array
 * @return extrainfo array
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarbb_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object id', 'admin', 'updatehook', 'xarbb');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'extrainfo', 'admin', 'updatehook', 'xarbb');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'module name', 'admin', 'updatehook', 'xarbb');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }

    if (!empty($extrainfo['itemtype'])) {
        $itemtype = $extrainfo['itemtype'];
    } else {
        $itemtype = 0;
    }

    if (!empty($extrainfo['itemid'])) {
        $itemid = $extrainfo['itemid'];
    } else {
        $itemid = $objectid;
    }
    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'item id', 'admin', 'updatehook', 'xarbb');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    // Do we need to process further?
    if (isset($extrainfo['xarbb_forum']) && is_int($extrainfo['xarbb_forum'])) {
        $fid = $extrainfo['xarbb_forum'];
    } else {
        $fid = xarVarCleanFromInput('xarbb_forum');
    }
    if (empty($fid)) {
        // We are not attaching this to a forum, so no need to go further.
        return $extrainfo;
    }

    if (isset($extrainfo['summary'])) {
        $tpost = $extrainfo['summary'];
    } else {
        $tpost = xarVarCleanFromInput('summary');
    }
    if (empty($tpost)) {
        // No summary, no forum post.
        return $extrainfo;
    }

    if (isset($extrainfo['title'])) {
        $ttitle = $extrainfo['title'];
    } else {
        $ttitle = xarVarCleanFromInput('title');
    }
    if (empty($ttitle)) {
        // No title, no forum post.
        return $extrainfo;
    }

    $itemlinks = xarModAPIFunc($modname,'user','getitemlinks',
                               array('itemtype' => $itemtype,
                                     'itemids' => array($itemid)),
                               0);

    if (isset($itemlinks[$itemid]) && !empty($itemlinks[$itemid]['url'])) {
        // normally we should have url, title and label here
        foreach ($itemlinks[$itemid] as $field => $value) {
            $item[$field] = $value;
        }
    } else {
        $item['url'] = xarModURL($modname,'user','display',
                                 array('itemtype' => $itemtype,
                                       'itemid' => $itemid));
    }

/*
    $tpostfull = $tpost;
    $tpostfull .= "\n\n";
    $tpostfull .= xarML('Source');
    $tpostfull .= ': <a href="';
    $tpostfull .= $item['url'];
    $tpostfull .= '">';
    $tpostfull .= $ttitle;
    $tpostfull .= '</a>';
*/
    $tposter = xarUserGetVar('uid');
    $tstatus = '';

    if (!xarModAPIFunc('xarbb',
                       'user',
                       'createtopic',
                       array('fid'      => $fid,
                             'ttitle'   => $ttitle,
                             'tpost'    => $tpost,
                             'tposter'  => $tposter,
                             'tstatus'  => $tstatus))) {
        return $extrainfo;
    }

    if (!xarModAPIFunc('xarbb',
                       'user',
                       'updateforumview',
                       array('fid'      => $fid,
                             'topics'   => 1,
                             'move'     => 'positive',
                             'replies'  => 1,
                             'fposter'  => $tposter))) {
        return $extrainfo;
    }

    return $extrainfo;
}
?>