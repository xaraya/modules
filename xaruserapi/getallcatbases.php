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
    sys::import('modules.query.class.query');
    extract($args);
    $xartable = xarDB::getTables();

    $q = new Query('SELECT');
    $q->addtable($xartable['categories_basecategories'],'base');
    $q->addtable($xartable['categories'],'category');
    $q->leftjoin('base.category_id','category.id');
    $q->addfield('base.id AS id');
    $q->addfield('base.category_id AS category_id');
    $q->addfield('base.name AS name');
    $q->addfield('base.module_id AS module_id');
    $q->addfield('base.itemtype AS itemtype');
    $q->addfield('category.left_id AS left_id');
    $q->addfield('category.right_id AS right_id');
    if (!empty($module))  $q->eq('module_id',xarMod::getID($module));
    $q->eq('itemtype',$itemtype);
//    $q->qecho();
    if (!$q->run()) return;
    return $q->output();
}

?>
