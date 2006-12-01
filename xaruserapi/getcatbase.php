<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * Get a category base
 *
 * NOTE:
 * The modid and itemtype are only needed for the moment while
 * base IDs are not unique across the system.
 *
 * @param $args['bid'] base ID
 * @param $args['modid'] the id of the module (temporary)
 * @param $args['module'] the name of the module (temporary)
 * @param $args['itemtype'] the ID of the itemtype (temporary)
 * @return array details of a category base
 */
function categories_userapi_getcatbase($args)
{
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
        $cidlist = xarModGetVar($module, 'mastercids.' . $itemtype);
    } else {
        $cidlist = xarModGetVar($module, 'mastercids');
    }

    // Return just the cid we need.
    $cidlist = explode(';', $cidlist);
    $cid = $cidlist[$bid - 1];

    if (!empty($cid)) {
        $result['cid'] = $cid;
    }

    // Create some dummy values to be used later.
    // TODO: create required list.
    $result['bid'] = $bid;
    $result['name'] = '';
    $result['order'] = $bid;
    $result['display'] = true;
    $result['multiple'] = true;

    // These will be stored with the cat base, rather than being passed in.
    $result['modid'] = $modid;
    $result['itemtype'] = $itemtype;

    return $result;
}

?>
