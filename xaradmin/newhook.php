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
 * select categories for a new item - hook for ('item','new','GUI')
 *
 * @param $args['objectid'] ID of the object
 * @param $args['extrainfo'] extra information
 * @return bool true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function categories_admin_newhook($args)
{
    extract($args);

    if (!isset($extrainfo)) {
        $extrainfo = array();
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
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)','module name', 'admin', 'modifyhook', 'categories');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (empty($extrainfo['number_of_categories'])) {
        // get number of categories from current settings
        if (!empty($extrainfo['itemtype'])) {
            $numcats = (int) xarModGetVar($modname, 'number_of_categories.'.$extrainfo['itemtype']);
        } else {
            $numcats = (int) xarModGetVar($modname, 'number_of_categories');
        }
    } else {
        $numcats = (int) $extrainfo['number_of_categories'];
    }

    if (empty($numcats) || !is_numeric($numcats)) {
        $numcats = 0;
        // no categories to show here -> return empty output
        return '';
    }

    // Security check (return empty hook output if not allowed) - to be refined per cat
    if (!empty($extrainfo['itemtype'])) {
        $modtype = $extrainfo['itemtype'];
    } else {
        $modtype = 'All';
    }
    if (!xarSecurityCheck('SubmitCategoryLink',0,'Link',"$modid:$modtype:All:All")) return '';

    if (empty($extrainfo['mastercids']) || !is_array($extrainfo['mastercids'])) {
        // try to get master cids from current settings
        if (!empty($extrainfo['itemtype'])) {
            $cidlist = xarModGetVar($modname,'mastercids.'.$extrainfo['itemtype']);
        } else {
            $cidlist = xarModGetVar($modname,'mastercids');
        }
        if (empty($cidlist)) {
            $mastercids = array();
        } else {
            $mastercids = explode(';',$cidlist);
        }
    } else {
        $mastercids = $extrainfo['mastercids'];
    }

    // used e.g. for previews of new items
    if (empty($extrainfo['cids']) || !is_array($extrainfo['cids'])) {
        if (!empty($extrainfo['new_cids']) && is_array($extrainfo['new_cids'])) {
            $cids = $extrainfo['new_cids'];
        } else {
            // try to get cids from input
            xarVarFetch('new_cids', 'list:int:1:', $cids, NULL, XARVAR_NOT_REQUIRED);
            if (empty($cids) || !is_array($cids)) {
                $cids = array();
            }
        }
    } else {
        $cids = $extrainfo['cids'];
    }
    // get all valid cids
    $seencid = array();
    foreach ($cids as $cid) {
        if (empty($cid) || !is_numeric($cid)) {
            continue;
        }
        if (empty($seencid[$cid])) {
            $seencid[$cid] = 1;
        } else {
            $seencid[$cid]++;
        }
    }

    $items = array();
    for ($n = 0; $n < $numcats; $n++) {
        if (!isset($mastercids[$n])) {
            break;
        }
        $item = array();
        $item['num'] = $n + 1;
        $item['select'] = xarModAPIFunc('categories', 'visual', 'makeselect',
                                       array('cid' => $mastercids[$n],
                                             'multiple' => 1,
                                             'name_prefix' => 'new_',
                                             'return_itself' => true,
                                             'select_itself' => true,
                                             'values' => &$seencid));

        $items[] = $item;
    }
    unset($item);

    $labels = array();
    if ($numcats > 1) {
        $labels['categories'] = xarML('Categories');
    } else {
        $labels['categories'] = xarML('Category');
    }

    return xarTplModule('categories','admin','newhook',
                         array('labels' => $labels,
                               'numcats' => $numcats,
                               'items' => $items));
}

?>
