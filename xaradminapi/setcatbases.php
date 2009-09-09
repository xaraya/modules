<?php

/**
 * set category bases
 *
 * @param $args['module'] the name of the module
 * @param $args['itemtype'] the ID of the itemtype
 * @param $args['cids'] an array of category ids only; zero-indexed numeric keys
 * @returns bool
 * @return true on success
 */
function categories_adminapi_setcatbases($args)
{
    extract($args);
    // Argument check
    if (empty($module)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'module', 'admin', 'setcatbases', 'categories');
        throw new Exception($msg);
    }
    if (empty($itemtype)) {
        $itemtype = 0;
    }
    if (empty($cids)) {
        $cids = array();
    }

    sys::import('modules.query.class.query');
    xarMod::loadDbInfo('categories');
    $xartable = xarDB::getTables();

    // Remove all the entries for this module and itemtype
    $q = new xarQuery('DELETE', $xartable['categories_basecategories']);
    $q->eq('module_id',xarMod::getID($module));
    if (isset($itemtype)) {
        $q->eq('itemtype',$itemtype);
    }
    if (!$q->run()) return;

// CHECKME: what about this old 'basecids' stuff ?
/*
    $cidstring = serialize($cids);
    if (empty($itemtype)) {
        xarModVars::set($module,'basecids',$cidstring);
    } else {
        // FIXME: this doesn't work for itemtype == _XAR_ID_UNREGISTERED !
        xarModUserVars::set($module,'basecids',$cidstring,$itemtype);
    }
*/

    if (empty($cids)) {
        return;
    }

// CHECKME: allow passing base category name too someday ?
    $catinfo = xarModAPIFunc('categories','user','getcatinfo',
                             array('cids' => $cids));

    foreach ($cids as $cid) {
        if (empty($catinfo[$cid])) continue;

        $q = new xarQuery('INSERT', $xartable['categories_basecategories']);
        $q->addfield('module_id',xarMod::getID($module));
        $q->addfield('itemtype',$itemtype);
// CHECKME: allow passing base category name too someday ?
        $q->addfield('name',$catinfo[$cid]['name']);
        $q->addfield('category_id',$cid);
        if (!$q->run()) return;
    }

    return true;
}

?>
