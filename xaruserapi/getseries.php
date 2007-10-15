<?php

/**
 * Get article series records.
 *
 * @param sid integer Series ID
 * @param sids array Array of series IDs
 * @param ref string Series reference
 * @param mid integer Magazine ID
 *
 */

function mag_userapi_getseries($args)
{
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,modid,itemtype_series,sort_default_articles'
        )
    ));

    // Only used for some text escaping methods.
    $dbconn =& xarDBGetConn();

    if (!isset($sort)) $sort = $sort_default_series;

    // Search criteria.
    $params = array (
        'module' => $module,
        'itemtype' => $itemtype_series,
        'sort' => $sort,
    );

    $where = array();

    // Status
    if (!empty($status)) {
        if (is_string($status)) $status = array($status);
        $where[] = "status in ('" . implode("','", $status) . "')";
    }

    // Magazine ID
    if (!empty($mid) && is_numeric($mid)) {
        $where[] = 'mag_id eq ' . (integer)$mid;
    }

    // Single series ID
    if (!empty($sid) && is_numeric($sid)) {
        $where[] = 'sid eq ' . (integer)$sid;
    }

    // Multiple series IDs
    if (xarVarValidate('list:id', $sids, true) && !empty($sids)) {
        $where[] = 'sid in (' . implode(',', $sids) . ')';
    }

    // Series reference
    if (!empty($ref) && is_string($ref)) {
        $where[] = 'ref eq ' . $dbconn->qstr($ref);
    }

    if (!empty($where)) $params['where'] = implode(' AND ', $where);

    if (!empty($docount)) {
        // Just do a count.
        $count_items = xarModAPIfunc('dynamicdata', 'user', 'countitems', $params);
        return $count_items;
    } else {
        // startnum
        if (!empty($startnum) && is_numeric($startnum)) {
            $params['startnum'] = (integer)$startnum;
        }

        // numitems
        if (!empty($numitems) && is_numeric($numitems)) {
            $params['numitems'] = (integer)$numitems;
        }

        // Fetch the matching articles.
        $series = xarModAPIfunc('dynamicdata', 'user', 'getitems', $params);
    }

    $return = array();
    foreach($series as $value) {
        $return[$value['sid']] = $value;
    }

    return $return;
}

?>