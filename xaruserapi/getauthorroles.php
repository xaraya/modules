<?php

/**
 * Get the roles an author has on specified articles.
 *
 * @param aid integer Article ID
 * @param auid integer Author ID
 * @param auids array List of author IDs
 * @return array List of author role records
 *
 */

function mag_userapi_getauthorroles($args)
{
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,$modid,itemtype_articles_authors'
        )
    ));

    $return = array();

    // Search criteria.
    $params = array (
        'module' => $module,
        'itemtype' => $itemtype_articles_authors,
    );

    $where = array();

    // Article ID
    if (!empty($aid) && is_numeric($aid)) {
        $where[] = 'article_id eq ' . (integer)$aid;
    }

    // Article IDs
    if (xarVarValidate('list:id', $aids, true) && !empty($aids)) {
        $where[] = 'article_id in (' . implode(',', $aids) . ')';
    }

    // Author ID
    if (!empty($auid) && is_numeric($auid)) {
        $where[] = 'author_id eq ' . (integer)$auid;
    }

    // Author IDs
    if (xarVarValidate('list:id', $auids, true) && !empty($auids)) {
        $where[] = 'author_id in (' . implode(',', $auids) . ')';
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

        // Fetch the matching issues.
        $roles = xarModAPIfunc('dynamicdata', 'user', 'getitems', $params);

        $return = $roles;
    }

    return $return;
}

?>