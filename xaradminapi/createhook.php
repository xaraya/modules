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
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return $extrainfo;
    }
    // Do we need to process further?
    if (isset($extrainfo['tb_pingurl']) && is_string($extrainfo['tb_pingurl'])) {
        $pingurl = $extrainfo['tb_pingurl'];
    } else {
        if (!xarVarFetch('tb_pingurl', 'str:1:', $pingurl, '', XARVAR_NOT_REQUIRED)) return;
    }
    if (empty($pingurl)) {
        return $extrainfo;
    }

    if (isset($extrainfo['tb_excerpt'])) {
        $excerpt = $extrainfo['tb_excerpt'];
        $excerpt = (strlen($excerpt > 255) ? substr($excerpt, 0, 252) ."..." : $excerpt);
    } else {
        if (!xarVarFetch('tb_excerpt', 'str:1:', $excerpt, '', XARVAR_NOT_REQUIRED)) return;
        $excerpt = (strlen($excerpt > 255) ? substr($excerpt, 0, 252) ."..." : $excerpt);
    }
    if (empty($excerpt)) {
        if (isset($extrainfo['summary'])) {
            $excerpt = $extrainfo['summary'];
            $excerpt = (strlen($excerpt > 255) ? substr($excerpt, 0, 252) ."..." : $excerpt);
        } else {
            if (!xarVarFetch('summary', 'str:1:', $excerpt, '', XARVAR_NOT_REQUIRED)) return;
            $excerpt = (strlen($excerpt > 255) ? substr($excerpt, 0, 252) ."..." : $excerpt);
        }
        if (empty($excerpt)) {
            $excerpt = '';
        }
    }

    if (isset($extrainfo['title'])) {
        $title = $extrainfo['title'];
    } else {
        if (!xarVarFetch('title', 'str:1:', $title, '', XARVAR_NOT_REQUIRED)) return;
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