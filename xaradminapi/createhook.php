<?php
/**
 * XarBB - A lightweight BB for Xaraya
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author John Cox, Mikespub, Jo Dalle Nogare
*/
/**
 * create an entry for a module item - hook for ('item','create','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return array extrainfo array
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarbb_adminapi_createhook($args)
{
    extract($args);

    if (!isset($objectid) || !is_numeric($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'object id', 'admin', 'updatehook', 'xarbb');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    if (!isset($extrainfo) || !is_array($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', 'extrainfo', 'admin', 'updatehook', 'xarbb');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        // we *must* return $extrainfo for now, or the next hook will fail
        //return false;
        return $extrainfo;
    }
    // Do we need to process further?
    if (!xarVarFetch('xarbb_forum', 'id', $fid, NULL, XARVAR_DONT_SET)) return;

     if (empty($fid) && isset($extrainfo['xarbb_forum'])) {
        $fid = $extrainfo['xarbb_forum'];
    }
    if (empty($fid)) {
        // no forum
        return $extrainfo;
    }

    if (isset($extrainfo['summary'])) {
        $tpost = $extrainfo['summary'];
    } else {
        if (!xarVarFetch('summary', 'str', $tpost, '', XARVAR_NOT_REQUIRED)) return;
    }
    if (empty($tpost)) {
        // No summary, no forum post.
        return $extrainfo;
    }

    if (isset($extrainfo['title'])) {
        $ttitle = $extrainfo['title'];
    } else {
        if (!xarVarFetch('ttitle', 'str', $ttitle, '', XARVAR_NOT_REQUIRED)) return;
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
    $tstatus = 0;

    $tid = xarModAPIFunc('xarbb',
                         'user',
                         'createtopic',
                         array('fid'      => $fid,
                               'ttitle'   => $ttitle,
                               'tpost'    => $tpost,
                               'tposter'  => $tposter,
                               'tstatus'  => $tstatus));
    if (empty($tid)) {
        return $extrainfo;
    }

    if (!xarModAPIFunc('xarbb',
                       'user',
                       'updateforumview',
                       array('fid'      => $fid,
                             'tid'      => $tid,
                             'ttitle'   => $ttitle,
                             'treplies' => 0,
                             'topics'   => 1,
                             'move'     => 'positive',
                             'replies'  => 1,
                             'fposter'  => $tposter))) {
        return $extrainfo;
    }

    return $extrainfo;
}
?>
