<?php

/**
 * return the field names and correct values for joining on hitcount table
 * example : SELECT ..., $moduleid, $itemid, $hits,...
 *           FROM ...
 *           LEFT JOIN $table
 *               ON $field = <name of itemid field>
 *           WHERE ...
 *               AND $hits > 1000
 *               AND $where
 *
 * @param $args['modname'] name of the module you want items from, or
 * @param $args['modid'] ID of the module you want items from
 * @param $args['itemids'] optional array of itemids that we are selecting on
 * @returns array
 * @return array('table' => 'nuke_hitcount',
 *               'field' => 'nuke_hitcount.xar_itemid',
 *               'where' => 'nuke_hitcount.xar_itemid IN (...)
 *                           AND nuke_hitcount.xar_moduleid = 123',
 *               'moduleid'  => 'nuke_hitcount.xar_moduleid',
 *               ...
 *               'hits'  => 'nuke_hitcount.xar_hits')
 */
function hitcount_userapi_leftjoin($args)
{
    // Get arguments from argument array
    extract($args);

    // Optional argument
    if (!isset($modname)) {
        $modname = '';
    } else {
        $modid = xarModGetIDFromName($modname);
    }
    if (!isset($modid)) {
        $modid = '';
    }
    if (!isset($itemids)) {
        $itemids = array();
    }

    // Security check
    if (count($itemids) > 0) {
        foreach ($itemids as $itemid) {
			if(!xarSecurityCheck('ViewHitcountItems',1,'Item',"$modname:All:$itemid")) return;
        }
    } else {
		if(!xarSecurityCheck('ViewHitcountItems',1,'Item',"$modname:All:All")) return;
    }

    // Table definition
    $xartable =& xarDBGetTables();
    $userstable = $xartable['hitcount'];

    $leftjoin = array();

    // Specify LEFT JOIN ... ON ... [WHERE ...] parts
    $leftjoin['table'] = $xartable['hitcount'];
    $leftjoin['field'] = '';
    if (!empty($modid)) {
        $leftjoin['field'] .= $xartable['hitcount'] . '.xar_moduleid = ' . $modid;
        $leftjoin['field'] .= ' AND ';
    }
    if (isset($itemtype)) { // could be 0 (= most likely)
        $leftjoin['field'] .= $xartable['hitcount'] . '.xar_itemtype = ' . $itemtype;
        $leftjoin['field'] .= ' AND ';
    }
    $leftjoin['field'] .= $xartable['hitcount'] . '.xar_itemid';

    if (count($itemids) > 0) {
        $allids = join(', ', $itemids);
        $leftjoin['where'] = $xartable['hitcount'] . '.xar_itemid IN (' .
                             xarVarPrepForStore($allids) . ')';
/*
        if (!empty($modid)) {
            $leftjoin['where'] .= ' AND ' .
                                  $xartable['hitcount'] . '.xar_moduleid = ' .
                                  $modid;
        }
*/
    } else {
/*
        if (!empty($modid)) {
            $leftjoin['where'] = $xartable['hitcount'] . '.xar_moduleid = ' .
                                 $modid;
        } else {
            $leftjoin['where'] = '';
        }
*/
        $leftjoin['where'] = '';
    }

    // Add available columns in the hitcount table
    $columns = array('moduleid','itemtype','itemid','hits');
    foreach ($columns as $column) {
        $leftjoin[$column] = $xartable['hitcount'] . '.xar_' . $column;
    }

    return $leftjoin;
}

?>
