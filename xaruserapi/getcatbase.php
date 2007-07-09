<?php

/**
 * Get a category base
 *
 * @param $args['bid'] base ID
 * @param $args['modid'] the id of the module (temporary)
 * @param $args['module'] the name of the module (temporary)
 * @param $args['itemtype'] the ID of the itemtype (temporary)
 * @returns details of a category base
 * @return category base
 */

/*
 * NOTE:
 * The modid and itemtype are only needed for the moment while
 * base IDs are not unique across the system.
 */

function categories_userapi_getcatbase($args)
{
    extract($args);

    $xartable = xarDB::getTables();
    $q = new xarQuery('SELECT', $xartable['categories_basecategories']);
    if (!empty($module)) $q->eq('module_id',xarMod::getID($module));
    if (!empty($itemtype)) $q->eq('itemtype',$itemtype);
    if (!empty($id)) $q->eq('id',$id);
    if (!empty($name)) $q->eq('name',$name);
//    $q->qecho();
    if (!$q->run()) return;
    return $q->row();

/*
// Expand arguments from argument array
    extract($args);

    // Either module or modid can be supplied.
    // 'module' takes precedance.

    if (empty($module) && !empty($modid)) {
        $modinfo = xarModGetInfo($modid);
        $module = $modinfo['name'];
    }

    // Security check
    // TODO: add a security check on each category base too.
    if(!xarSecurityCheck('ViewCategories')) {return;}

    $result = array();

    // Get the list of cids
    if ($itemtype > 0) {
        $cidlist = xarModVars::get($module, 'mastercids.' . $itemtype);
    } else {
        $cidlist = xarModVars::get($module, 'mastercids');
    }

    // Return just the cid we need.
    $cidlist = explode(';', $cidlist);
    $cid = $cidlist[$bid - 1];

    if (!empty($cid)) {
        $result['category_id'] = $cid;
    }

    // Create some dummy values to be used later.
    // TODO: create required list.
    $result['bid'] = $bid;
    $result['name'] = '';
    $result['order'] = $bid;
    $result['display'] = true;
    $result['multiple'] = true;

    // These will be stored with the cat base, rather than being passed in.
    $result['module_id'] = $modid;
    $result['itemtype'] = $itemtype;

    return $result;
    */
}

?>
