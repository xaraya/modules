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
function trackback_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'object id', 'admin', 'updatehook', 'trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'extrainfo', 'admin', 'updatehook', 'trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'module name', 'admin', 'updatehook', 'trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'admin', 'updatehook', 'trackback');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    // Do we need to process further?
    if (isset($extrainfo['tb_pingurl']) && is_string($extrainfo['tb_pingurl'])) {
        $pingurl = $extrainfo['tb_pingurl'];
    } else {
        $pingurl = xarVarCleanFromInput('tb_pingurl');
    }
    if (empty($pingurl)) {
        return $extrainfo;
    }

    if (isset($extrainfo['tb_excerpt'])) {
        $excerpt = $extrainfo['tb_excerpt'];
        $excerpt = (strlen($excerpt > 255) ? substr($excerpt, 0, 252) ."..." : $excerpt);
    } else {
        $excerpt = xarVarCleanFromInput('tb_excerpt');
        $excerpt = (strlen($excerpt > 255) ? substr($excerpt, 0, 252) ."..." : $excerpt);
    }
    if (empty($excerpt)) {
        if (isset($extrainfo['summary'])) {
            $excerpt = $extrainfo['summary'];
            $excerpt = (strlen($excerpt > 255) ? substr($excerpt, 0, 252) ."..." : $excerpt);
        } else {
            $excerpt = xarVarCleanFromInput('summary');
            $excerpt = (strlen($excerpt > 255) ? substr($excerpt, 0, 252) ."..." : $excerpt);
        }
        if (empty($excerpt)) {
            $excerpt = '';
        }
    }

    if (isset($extrainfo['title'])) {
        $title = $extrainfo['title'];
    } else {
        $title = xarVarCleanFromInput('title');
    }
    if (empty($title)) {
        $title = '';
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

    if (!xarModAPIFunc('trackback','admin','ping',
                         array('pingurl'    => $pingurl, 
                               'permalink'  => $item['url'], 
                               'title'      => $title, 
                               'excerpt'    => $excerpt))) {
        return $extrainfo;
    }

    return $extrainfo;
}

?>