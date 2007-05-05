<?php

/**
 * get category bases
 *
 * @param $args['module'] the name of the module (optional)
 * @param $args['modid'] the id of the module (optional)
 * @param $args['itemtype'] the ID of the itemtype (optional)
 * @param $args['format'] return format: 'cids', 'tree' or 'flat' (default 'flat').
 * @param $args['order'] columns to order by (optional)
 * @returns array of category bases
 * @return list of category bases
 */

/*
 * Explanation of the output formats:
 * 'cids': an array of category ids only; zero-indexed numeric keys
 * 'tree': a comprehensive array of category base details; more information below
 * 'flat': an array of category-base arrays; zero-indexed numeric keys
 */

/*
 * NOTE:
 * This function is over-complicated at the moment as it uses module
 * variables to store its info. It will be greatly implified when the
 * data is moved to a table of its own.
 */

function categories_userapi_getallcatbases($args)
{
    extract($args);
    $xartable = xarDB::getTables();
    //FIXME: needs to be unique
    $q = new xarQuery('SELECT', $xartable['categories_basecategories']);
    if (!empty($module)) {
        $info = xarMod::getBaseInfo($module);
        $q->eq('module_id',$info['systemid']);
    }
    if (!empty($itemtype)) $q->eq('itemtype',$itemtype);
//    $q->qecho();
    if (!$q->run()) return;
    return $q->output();
}

?>
