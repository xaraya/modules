<?php

/**
 * Get a list of articles that an author has been involved in.
 *
 * @param auid integer Author ID
 * @param auids array List of integer author IDs
 * @param mid integer Magazine ID
 * @param iid integer Issue ID
 * @param sid integer Series ID
 * @param article_status array List of article statuses; default PUBLISHED
 * @param issue_status array List of issue statuses; default PUBLISHED
 * @param mag_status array List of magazine statuses; default ACTIVE
 * @param status_group string PUBLISHED or DRAFT; sets statuses at all levels appropriately
 * @param docount boolean If set, specifies that a count should be returned instead.
 *
 */

function mag_userapi_authorarticles($args)
{
    extract($args);
    $return = array();

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,modid,itemtype_articles,default_author_articles'
        )
    ));

    // Used for some text escaping methods and queries..
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();

    // Handle numitems.
    // TODO: make this a parameter.
    // FIXME: perhaps this is not the place to set limits?
    if (empty($numitems)) $numitems = $default_author_articles;
    if (empty($startnum)) $startnum = 1;

    // TODO: handle sorting, which can include data in other tables.
    // Note: can only sort by columns that are selected in some databases.
    $bind = array();
    $where = array();

    // We must have some limit on the articles, limiting it to a single
    // magazine.
    if (empty($iid) && empty($iids) && empty($mid) && empty($sid)) {
        return $return;
    }

    // Create SQL for pre-selecting of article IDs
    if (!empty($docount)) {
        $sql = 'SELECT COUNT(DISTINCT art.aid)';
    } else {
        $sql = 'SELECT DISTINCT art.aid';
    }

    $sql .= ' FROM ' . $tables['mag_authors'] . ' AS a';

    // Statuses of various records.
    // This allows the status checks to be optional, or to cover other ststuses
    // so that administrators can preview unpublished articles.
    if (!empty($status_group)) {
        if ($status_group == 'PUBLISHED') {
            $article_status = array('PUBLISHED');
            $issue_status = array('PUBLISHED');
            $mag_status = array('ACTIVE');
        } elseif ($status_group == 'DRAFT') {
            $article_status = array();
            $issue_status = array();
            $mag_status = array();
        }
    }
    if (!isset($article_status)) $article_status = array('PUBLISHED');
    if (!isset($issue_status)) $issue_status = array('PUBLISHED');
    if (!isset($mag_status)) $mag_status = array('ACTIVE');

    // Link to the articles (lots of reasons to do this).
    $sql .= ' INNER JOIN ' . $tables['mag_articles_authors'] . ' AS aa'
        . ' ON aa.author_id = a.auid'
        . ' INNER JOIN ' . $tables['mag_articles'] . ' AS art'
        . ' ON art.aid = aa.article_id'
        . (!empty($article_status) ? ' AND art.status in (?' . str_repeat(',?', count($article_status)-1) . ')' : '')
        . ' INNER JOIN ' . $tables['mag_issues'] . ' AS i'
        . ' ON i.iid = art.issue_id'
        . (!empty($issue_status) ? ' AND i.status in (?' . str_repeat(',?', count($issue_status)-1) . ')' : '')
        . ' INNER JOIN ' . $tables['mag_mags'] . ' AS m'
        . ' ON m.mid = i.mag_id'
        . (!empty($mag_status) ? ' AND m.status in (?' . str_repeat(',?', count($mag_status)-1) . ')' : '');

    // Add the bind data, if statuses are being checked.
    if (!empty($article_status)) $bind = array_merge($bind, $article_status);
    if (!empty($issue_status)) $bind = array_merge($bind, $issue_status);
    if (!empty($mag_status)) $bind = array_merge($bind, $mag_status);


    // Extra join when fetching for series.
    if (!empty($sid)) {
        $sql .= ' INNER JOIN ' . $tables['mag_series'] . ' AS s'
            . ' ON s.mag_id = m.mid AND s.sid = art.series_id AND s.status = ?';
        $bind[] = 'ACTIVE';
    }

    // Series.
    if (!empty($sid) && is_numeric($sid)) {
        $where[] = 's.sid = ?';
        $bind[] = (integer)$sid;
    }
    
    // CHECKME: should the authors be mandatory?
    // Single author ID.
    if (!empty($auid) && is_numeric($auid)) {
        $where[] = 'a.auid = ?';
        $bind[] = (integer)$auid;
    }

    // Multiple author IDs.
    if (!empty($auids) && is_array($auids)) {
        $where[] = 'a.auid in (?' . str_repeat(',?', count($auids) -1) . ')';
        $bind = array_merge($bind, $auids);
    }

    // Issue ID
    if (!empty($iid) && is_numeric($iid)) {
        $where[] = 'i.iid = ?';
        $bind[] = (integer)$iid;
    }

    // Magazine ID
    if (!empty($mid) && is_numeric($mid)) {
        $where[] = 'm.mid = ?';
        $bind[] = (integer)$mid;
    }

    if (!empty($where)) $sql .= ' WHERE ' . implode(' AND ', $where);

    // Fetch the authors.
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum - 1, $bind);

    $article_ids = array();

    if ($result) {
        if (!empty($docount)) {
            // If we are doing a count, then just return that count.
            list($count) = $result->fields;
            return (integer)$count;
        } else {
            while (!$result->EOF) {
                list($article_id) = $result->fields;
                $result->MoveNext();
                $article_ids[] = $article_id;
            }
        }
    }

    // If we have a list of article IDs, then use them to fetch the 
    // article details.
    if (!empty($article_ids)) {
        // Selection criteria.
        $return = xarModAPIfunc($module, 'user', 'getarticles', array('aids' => $article_ids, 'fields' => 'TOC'));
/*
        $params = array (
            'module' => $module,
            'itemtype' => $itemtype_articles,
            'where' => 'aid in (' . implode(',', $article_ids) . ')',
            'fields' => 'TOC', // Get minimal fields for speed
        );

        $article_details = xarModAPIfunc('dynamicdata', 'user', 'getitems', $params);

        if (!empty($article_details)) {
            $article = array();

            foreach($article_ids as $article_id) {
                if (isset($article_details[$article_id])) {
                    $articles[$article_id] = $article_details[$article_id];
                }
            }
            $return = $articles;
        }
*/
    }

    return $return;
}

?>