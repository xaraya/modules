<?php

/**
 * display xlink entry
 *
 * @param $args['itemid'] item id of the xlink entry
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xlink_user_display($args)
{
    xarVarFetch('itemid','isset',$itemid,'', XARVAR_DONT_SET);
    extract($args);

    if (empty($itemid)) {
        return array();
    }
    $item = xarModAPIFunc('xlink','user','getitem',
                          array('id' => $itemid));
    if (!isset($item)) return;
    if (count($item) == 0 || empty($item['moduleid'])) return array();

    $modinfo = xarModGetInfo($item['moduleid']);
    if (!isset($modinfo) || empty($modinfo['name'])) return array();

    $itemlinks = xarModAPIFunc($modinfo['name'],'user','getitemlinks',
                               array('itemtype' => $item['itemtype'],
                                     'itemids' => array($item['itemid'])),
                               0);

    if (isset($itemlinks[$item['itemid']]) && !empty($itemlinks[$item['itemid']]['url'])) {
        // normally we should have url, title and label here
        foreach ($itemlinks[$item['itemid']] as $field => $value) {
            $item[$field] = $value;
        }
    } else {
        $item['url'] = xarModURL($modinfo['name'],'user','display',
                                 array('itemtype' => $item['itemtype'],
                                       'itemid' => $item['itemid']));
    }
    return $item;
}

?>
