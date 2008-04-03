<?php

/**
 * Get a list of articles that are related to any combination of author(s), magazine, series or issue.
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
 * @param sort string The sort criteria, passed directly on to the getarticles API. *only* use column names from the articles table.
 *
 */

function mag_userapi_relatedarticles($args)
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

    $sql .= ' FROM ' . $tables['mag_articles'] . ' AS art';

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

    // Link to the authors if we an author ID has been passed in for selection.
    if ((!empty($auid) && is_numeric($auid)) || (!empty($auids) && is_array($auids))) {
        $sql .= ' INNER JOIN ' . $tables['mag_articles_authors'] . ' AS aa'
            . ' ON art.aid = aa.article_id'
            . ' INNER JOIN ' . $tables['mag_authors'] . ' AS a'
            . ' ON a.auid = aa.author_id';
    }

    $sql .= ' INNER JOIN ' . $tables['mag_issues'] . ' AS i'
        . ' ON i.iid = art.issue_id'
        . (!empty($issue_status) ? ' AND i.status in (?' . str_repeat(',?', count($issue_status)-1) . ')' : '')
        . ' INNER JOIN ' . $tables['mag_mags'] . ' AS m'
        . ' ON m.mid = i.mag_id'
        . (!empty($mag_status) ? ' AND m.status in (?' . str_repeat(',?', count($mag_status)-1) . ')' : '');

    // Add the bind data, if statuses are being checked.
    if (!empty($issue_status)) $bind = array_merge($bind, $issue_status);
    if (!empty($mag_status)) $bind = array_merge($bind, $mag_status);


    // Extra join when fetching for series.
    if (!empty($sid)) {
        $sql .= ' INNER JOIN ' . $tables['mag_series'] . ' AS s'
            . ' ON s.mag_id = m.mid AND s.sid = art.series_id AND s.status = ?';
        $bind[] = 'ACTIVE';
    }

    // WHERE-clauses:-

    // Status of the articles.
    if (!empty($article_status)) {
        $where[] = 'art.status in (?' . str_repeat(',?', count($article_status)-1) . ')';
        $bind = array_merge($bind, $article_status);
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

    // Order-by clause.
    if (!empty($sort) && is_string($sort)) {
        // TODO: really must validate this string.
        // It is the same string passed into the getarticles, which is handled by
        // dynamicdata, validated, and used against DD property names rather than
        // database column names. We try to keep the two the same, so it should
        // work here.

        // Prefix each column name with 'art.'
        $sort_parts = preg_split('/[ \t]*,[ \t]*/', $sort);
        $sql .= ' ORDER BY ' . 'art.' . implode(', art.', $sort_parts);
    }

    // Fetch the articles.
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
        $params = array(
            'aids' => $article_ids,
            'fieldset' => 'TOC',
        );

        // Pass the sort criteria in, if requested.
        if (!empty($sort)) $params['sort'] = $sort;

        $return = xarModAPIfunc($module, 'user', 'getarticles', $params);
    }

    return $return;
}

?>