<?php

/**
 * display xlink entry for a module item - hook for ('item','display','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xlink_user_main($args)
{
    xarVarFetch('base','isset',$base,'', XARVAR_DONT_SET);
    xarVarFetch('id','isset',$id,'', XARVAR_DONT_SET);
    extract($args);

    if (empty($base) && empty($id)) {
        return array('status' => 0);
    } elseif (empty($id)) {
        return array('status' => 1,
                     'base' => xarVarPrepForDisplay($base),
                     'where' => "basename eq '" . xarVarPrepForStore($base) ."'");
    } elseif (empty($base)) {
        $base = '';
    }
    xarLogMessage("Base: $base");
    xarLogMessage("Id: $id");
// TODO: show list of valid id's per base ?
    $item = xarModAPIFunc('xlink','user','getitem',
                          array('basename' => $base,
                                'refid' => $id));
    if (!isset($item)) return;
    if (!isset($item['moduleid'])) {
        return array('status' => 2);
    }

    $modinfo = xarModGetInfo($item['moduleid']);
    if (!isset($modinfo) || empty($modinfo['name'])) {
        return array('status' => 3);
    }

// TODO: make configurable per module/itemtype
    $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                               array('itemtype' => $item['itemtype'],
                                     'itemids' => array($item['itemid'])),
                               0);
    if (isset($itemlinks[$item['itemid']]) && !empty($itemlinks[$item['itemid']]['url'])) {
        $url = $itemlinks[$item['itemid']]['url'];
    } else {
        $url = xarModURL($modinfo['name'],'user','display',
                         array('itemtype' => $item['itemtype'],
                               'itemid' => $item['itemid']));
    }
    
    // FIXME: can we redirect without changing address?
    // if so, we could have complete replacement for short url encode/decode in data,
    // which would obviously be more flexible (user configurable)
// Well, you could always try to call the module function yourself here :)
// return xarModFunc($modinfo['name'],'user','display',
//                   array('itemtype' => $item['itemtype'],
//                         'itemid' => $item['itemid']));
// Of course, it would help if you knew which function and parameters to use,
// like DD does :-)
    xarResponseRedirect($url);
    return true;
}

?>
