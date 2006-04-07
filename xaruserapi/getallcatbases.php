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
 * get category bases
 *
 * Explanation of the output formats:
 * 'cids': an array of category ids only; zero-indexed numeric keys
 * 'tree': a comprehensive array of category base details; more information below
 * 'flat': an array of category-base arrays; zero-indexed numeric keys
 *
 * @param $args['module'] the name of the module (optional)
 * @param $args['modid'] the id of the module (optional)
 * @param $args['itemtype'] the ID of the itemtype (optional)
 * @param $args['format'] return format: 'cids', 'tree' or 'flat' (default 'flat').
 * @param $args['order'] columns to order by (optional)
 * @return array Array with list of category bases
 */
/**
 * NOTE:
 * This function is over-complicated at the moment as it uses module
 * variables to store its info. It will be greatly implified when the
 * data is moved to a table of its own.
 */
function categories_userapi_getallcatbases($args)
{
    // Expand arguments from argument array
    extract($args);

    if (empty($order)) {
        $order = 'order';
    }

    if (empty($format)) {
        $format = 'flat';
    }

    // Either module(s) or modid(s) can be supplied.
    // 'module' takes precedance.
    // This section ensures there are two arrays or unset variables.
    $modlist = array();
    if (!empty($module)) {
        // Single module name supplied.
        $modlist[] = array(
            'module' => $module,
            'modid' => xarModGetIDfromName($module)
        );
    } elseif (!empty($modules) && is_array($modules)) {
        // Array of module names supplied.
        $mapfunc = create_function(
            '$module',
            'return array("module"=>$module,"modid"=>xarModGetIDfromName($module));'
        );
        $modlist = array_map($mapfunc, $modules);
    } elseif (!empty($modid) && is_numeric($modid)) {
        // Single module id supplied
        $modinfo = xarModGetInfo($modid);
        $modlist[] = array(
            'module' => $modinfo['name'],
            'modid' => $modid
        );
    } elseif (!empty($modids) && is_array($modids)) {
        // Array of modids supplied
        $mapfunc = create_function(
            '$modid',
            '$modinfo = xarModGetInfo($modid);'
            .'return array("module"=>$modinfo["name"],"modid"=>$modid);'
        );
        $modlist = array_map($mapfunc, $modids);
    } else {
        $itemtype = null;
        // Get an array of all modules hooked to categories.
        $hookedmodules = xarModAPIFunc(
            'modules', 'admin', 'gethookedmodules',
            array('hookModName' => 'categories')
        );
        $modlist = array();
        if (!empty($hookedmodules) && is_array($hookedmodules)) {
            foreach($hookedmodules as $module => $value) {
                $modlist[] = array(
                    'module' => $module,
                    'modid' =>  xarModGetIDfromName($module)
                );
            }
        }
    }

    // No modules - bail out now.
    if (empty($modlist)) {
        return;
    }

    // If itemtype is not set, then all item types
    // available (or all bases where there are no
    // item types for a module )are to be returned.
    // If the itemtype is set, then it will only be
    // used in conjunction with a specified module.

    // Security check
    // TODO: add a security check on each category base too.
    if(!xarSecurityCheck('ViewCategories')) {return;}

    $result = array();

    // Loop for each module.
    foreach($modlist as $modinfo) {
        $itemtypes = array();

        if (isset($itemtype) && is_numeric($itemtype)) {
            // If itemtype is set, then fetch just for that item type.
            $itemtypes[$itemtype] = array();
        } else {
            // If itemtype is not set, then fetch all itemtypes for the module.

            // Get list of item types.
            // Don't throw an exception if this function doesn't exist.
            //var_dump ($modinfo['name']); echo "<br/>";
            $modtypes = xarModAPIFunc(
                $modinfo['module'], 'user', 'getitemtypes',
                array(), 0
            );

            // Assume for now that there is a default, as 'getitemtypes' does not
            // necessarily return the default itemtype.
            $itemtypes[0] = array();

            if (!empty($modtypes) && is_array($modtypes)) {
                // Extract list of itemtypes.
                foreach($modtypes as $moditemtype => $moditemtypeinfo) {
                    $itemtypes[$moditemtype] = $moditemtypeinfo;
                }
            }
        }

        foreach($itemtypes as $currentitemtype => $currentitemtypeinfo) {
            if ($currentitemtype > 0) {
                $cidlist = xarModGetVar($modinfo['module'], 'mastercids.' . $currentitemtype);
            } else {
                $cidlist = xarModGetVar($modinfo['module'], 'mastercids');
            }

            if (empty($cidlist)) {
                // No base categories to return for this module and itemtype.
                continue;
            }

            if ($format == 'tree' && empty($result[$modinfo['modid']])) {
                $result[$modinfo['modid']] = $modinfo; // array
            }

            $cidlist = explode(';', $cidlist);

            // Initialise the psuedo base id.
            // These base IDs are only valid within a module and item type grouping.
            $bid = 1;

            foreach($cidlist as $cid) {
                // TODO: When implemented as tables, this will be a join to the categories table.
                $catinfo = xarModAPIFunc(
                    'categories', 'user', 'getcatinfo',
                    array('cid' => $cid)
                );

                if ($format == 'tree') {
                    // The tree format allows for loops to scan the cat bases in layers. It
                    // includes most expanded information that you may want to use.
                    //
                    // The main category base details are here:
                    //    [$moduleid]['itemtypes'][$itemtype]['catbases'][$baseid]
                    //
                    // The category for the base is expanded here:
                    //    [$moduleid]['itemtypes'][$itemtype]['catbases'][$baseid]['category']
                    //
                    // The module details can be found here:
                    //    [$moduleid]['module']
                    //
                    // Item type details can be found here:
                    //    [$moduleid]['itemtypes'][$itemtype]['itemtype']
                    //

                    $result[$modinfo['modid']]['itemtypes'][$currentitemtype]['itemtype'] = $currentitemtypeinfo;
                    $result[$modinfo['modid']]['itemtypes'][$currentitemtype]['catbases'][$bid] = array(
                        'bid' => $bid,
                        'name' => '',
                        'order' => $bid,
                        'display' => true,
                        'multiple' => true,
                        'category' => array(
                            'cid' => $catinfo['cid'],
                            'name' => $catinfo['name'],
                            'description' => $catinfo['description'],
                            'image' => $catinfo['image']
                        )
                     );
                } else {
                    $result[] = array(
                        'bid' => $bid,
                        'name' => '',
                        'order' => $bid,
                        'display' => true,
                        'multiple' => true,
                        'cid' => $cid,
                        'catname' => $catinfo['name'],
                        'module' => $modinfo['module'],
                        'modid' => $modinfo['modid'],
                        'itemtype' => $currentitemtype
                    );
                }

                $bid += 1;
            }
        }
    }

    if (empty($result)) {
        return;
    }

    // Do some final ordering.
    // When table-based, this can be done in the query.
    if ($format != 'tree') {
        $order = explode(',', $order);
        foreach($order as $orderelement) {
            // TODO: allow reverse order using '-orderelement', e.g. '-cid'.
            if ($orderelement == 'order' || $orderelement == 'cid' || $orderelement == 'module' || $orderelement == 'modid') {
                $sortfunc = create_function(
                    '$a,$b',
                    'if ($a["'.$orderelement.'"]==$b["'.$orderelement.'"]) {return 0;}'
                    .' return ($a["'.$orderelement.'"] < $b["'.$orderelement.'"]) ? -1 : 1;');
                usort($result, $sortfunc);
            }
            // TODO: fix - can only order by one element, so skip the rest.
            // This will be supported when it is a table.
            break;
        }

        if ($format == 'cids') {
            $cidlist = array();
            // Return an array of cids only for legacy support.
            foreach($result as $resultelement) {
                $cidlist[] = (int) $resultelement['cid'];
            }
            $result = $cidlist;
        }
    }
    return $result;
}
?>
