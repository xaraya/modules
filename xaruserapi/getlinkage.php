<?php

function categories_userapi_getlinkage($args)
{
    extract($args);

    // Requires: module, itemtype, itemid (but not validated)

    if (!isset($itemid)) return array();
    if (empty($module)) {
        $module = xarModGetName();
    }

	$modid = xarMod::getID($module);

    $tables = xarDB::getTables();
    $q = new xarQuery('SELECT',$tables['categories_linkage']);
    $q->eq('module_id',$modid);
    if (!empty($itemtype)) {
	    if (is_array($itemtype)) {
			$q->in('itemtype',$itemtype);
		} else {
			$q->eq('itemtype',$itemtype);
		}
    }
    if (!empty($itemid)) {
	    if (is_array($itemid)) {
			$q->in('item_id',$itemid);
		} else {
			$q->eq('item_id',$itemid);
		}
    }
    if (!empty($basecid)) {
	    if (is_array($basecid)) {
			$q->in('basecategory',$basecid);
		} else {
			$q->eq('basecategory',$basecid);
		}
    }
    if (!empty($categoryid)) {
	    if (is_array($categoryid)) {
			$q->in('category_id',$categoryid);
		} else {
			$q->eq('category_id',$categoryid);
		}
    }
//    $q->qecho();
    if (!$q->run()) return array();
    return $q->output();
}

?>
