<?php

/**
 * get category bases
 *
 * @param $args['module'] the name of the module (optional)
 * @param $args['itemtype'] the ID of the itemtype (optional)
 * @returns array of category bases
 * @return list of category bases
 */

/*
 * Explanation of the output formats:
 * 'cids': an array of category ids only; zero-indexed numeric keys
 * 'tree': a comprehensive array of category base details; more information below
 * 'flat': an array of category-base arrays; zero-indexed numeric keys
 */

function categories_userapi_getallcatbases($args)
{
    extract($args);
    $xartable = xarDB::getTables();
    //FIXME: needs to be unique
    $q = new xarQuery('SELECT', $xartable['categories_basecategories']);
    if (!empty($module)) {
        $q->eq('module_id',xarMod::getID($module));
    }
    if (!empty($itemtype)) $q->eq('itemtype',$itemtype);
//    $q->qecho();
    if (!$q->run()) return;
    return $q->output();
}

?>
