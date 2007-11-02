<?php

/**
 * Get articles.
 *
 * @param aid integer Article ID
 * @param aids array Array of integer srticle IDs
 * @param ref string Article reference
 * @param mid integer Magazine ID (likely not used)
 * @param iid integer Issue ID (unique across all magazines)
 * @param sid integer Series ID
 * @param fields string Limits the fields returned; 'TOC'=minimal fields for a table of contents; default=all available fields
 *
 */

function mag_userapi_getarticles($args)
{
    extract($args);
    $return = array();

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,modid,itemtype_articles,base_image_vpath,article_fieldset_toc,sort_default_articles,image_article_main_vpath'
        )
    ));

    // Only used for some text escaping methods.
    $dbconn =& xarDBGetConn();

    if (!isset($sort)) $sort = $sort_default_articles;

    // Search criteria.
    $params = array (
        'module' => $module,
        'itemtype' => $itemtype_articles,
        'sort' => $sort,
    );

    if (!empty($fieldset)) {
        if ($fieldset == 'TOC') $params['fieldlist'] = $article_fieldset_toc;
    }

    $where = array();

    // Status
    if (!empty($status)) {
        if (is_string($status)) $status = array($status);
        $where[] = "status in ('" . implode("','", $status) . "')";
    }

    // Issue ID
    if (!empty($iid) && is_numeric($iid)) {
        $where[] = 'issue_id eq ' . (integer)$iid;
    }

    // Article ID
    if (!empty($aid) && is_numeric($aid)) {
        $where[] = 'aid eq ' . (integer)$aid;
    }

    // Article IDs
    if (!empty($aids) && is_array($aids)) {
        $where[] = 'aid in (' . implode(",", $aids) . ")";
    }

    // Article reference
    if (!empty($ref) && is_strimng($ref)) {
        $where[] = 'ref eq ' . $dbconn->qstr($ref);
    }

    // Single series ID
    if (!empty($sid) && is_numeric($sid)) {
        $where[] = 'series_id eq ' . (integer)$sid;
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
        $articles = xarModAPIfunc('dynamicdata', 'user', 'getitems', $params);

        $return = $articles;
    }

    return $return;
}

?>