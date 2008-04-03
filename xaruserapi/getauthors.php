<?php

/**
 * Get authors.
 *
 * @param aid integer Article ID
 * @param aids array List of integer article IDs [not used]
 * @param auid integer Author ID
 * @param auids array List of integer author IDs
 * @param mid integer Magazine ID
 * @param iid integer Issue ID
 * @param sid integer Series ID
 * @param status_group string PUBLISHED or DRAFT (default PUBLISHED)
 * @param docount boolean If set, speficies that a count should be returned instead.
 * @param groupby string Indicates grouping of the authors. Values include 
 * @param sort string List of sortcriteria (options: name [ASC|DESC], auid [ASC|DESC], articles [ASC|DESC])
 *
 * Return a list of authors based on one of a number of criteria:
 * - writing for a particular article
 * - writing for a magazine
 * - writing for an issue of a magazine
 * - writing for a series of a magazine
 *
 * This is an end-user function (not admin) and so any magazines,
 * articles and series selected must be active and published.
 * This applies only when additional details are selected, i.e. when selecting
 * by anything other than author ID and numitems/startnum.
 *
 * @todo If authors have been selected for a single article, then fetch their roles in that article.
 *
 */

function mag_userapi_getauthors($args)
{
    extract($args);
    $return = array();

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,modid,itemtype_authors,image_author_photo_vpath,image_author_icon_vpath,sort_default_authors'
        )
    ));

    // Used for some text escaping methods and queries..
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();

    // Validate the groupby parameter.
    if (!xarVarValidate('enum:author:article', $groupby, true)) $groupby = 'author';

    // Handle numitems.
    // TODO: remove the limit here - it should be handled at a higher level.
    if (empty($numitems)) $numitems = 1000;
    if (empty($startnum)) $startnum = 1;

    // Handle sorting, which can include data in other tables.
    // Note: can only sort by columns that are selected in some databases.
    // TODO: include the ability to sort by article ID and article publication dates
    if (!empty($sort) && xarVarValidate('strlist:,:pre:trim:lower:passthru:enum:name:name asc:name desc:auid:auid asc:auid desc:articles: articles asc:articles desc', $sort, true)) {
        // COnvert the keywords into column names.
        $sorting = str_replace(array('name', 'auid'), array('a.name', 'a.auid'), $sort);
    } else {
        $sorting = strtolower($sort_default_authors);
    }

    // Set a flag to say we're sorting by the author's article count
    $sortby_articles = 0;
    if (preg_match('/.*articles.*/', $sorting)) {
        $sorting = str_replace('articles','article_count', $sorting);
        $sortby_articles = 1;
    }

    // If group by is to article then ignore sorting
    if ($groupby == "article") {
        unset($sorting);
        $sortby_articles = 0;
    }
    
    $bind = array();
    $where = array();

    // Create SQL for pre-selecting of author IDs
    if (!empty($docount)) {
        $sql = 'SELECT COUNT(DISTINCT a.auid)';
    } else {
        $sql = 'SELECT DISTINCT a.auid';
        if ($groupby == 'article') $sql .= ', art.aid';
        if ($sortby_articles == 1) $sql .= ', COUNT( DISTINCT art.aid ) AS article_count';
    }

    $sql .= ' FROM ' . $tables['mag_authors'] . ' AS a';

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
    if (!empty($aid) || !empty($aids) || !empty($iid) || !empty($iids) || !empty($mid) || !empty($sid)) {
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
    }

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

    // Article ID
    if (!empty($aid) && is_numeric($aid)) {
        $where[] = 'aa.article_id = ?';
        $bind[] = (integer)$aid;
    }

    // Multiple article IDs
    if (!empty($aids) && is_array($aids)) {
        $where[] = 'aa.article_id in (?' . str_repeat(',?', count($aids) -1) . ')';
        $bind = array_merge($bind, $aids);
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
    
    if ($sortby_articles == 1) $sql .= ' GROUP BY a.auid';

    if (!empty($sorting)) $sql .= ' ORDER BY ' . $sorting;

    // Fetch the authors.
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum - 1, $bind);

    $author_ids = array();
    $articles = array();

    if ($result) {
        if (!empty($docount)) {
            // If we are doing a count, then just return that count.
            list($count) = $result->fields;
            return (integer)$count;
        } else {
            while (!$result->EOF) {
                
                if ($groupby == 'article') {
                    list($author_id, $article_id) = $result->fields;
                } else {
                    list($author_id) = $result->fields;
                }
                $result->MoveNext();

                // Store the author ID
                if (!in_array($author_id, $author_ids)) $author_ids[] = $author_id;

                // If we have an article ID, then group the author in with that.
                if (!empty($article_id) && !isset($articles)) $articles[$article_id] = array();
                if (!empty($article_id)) $articles[$article_id][] = $author_id;
            }
        }
    }

    // If we have a list of author IDs, then use them to fetch the 
    // author details.
    if (!empty($author_ids)) {
        // Selection criteria.
        $params = array (
            'module' => $module,
            'itemtype' => $itemtype_authors,
            'where' => 'auid in (' . implode(',', $author_ids) . ')',
        );

        $author_details = xarModAPIfunc('dynamicdata', 'user', 'getitems', $params);

        if (!empty($author_details)) {
            $authors = array();

            foreach($author_ids as $author_id) {
                if (isset($author_details[$author_id])) {
                    $authors[$author_id] = $author_details[$author_id];

                    // Expand the photo and icon (thumbnail) paths.
                    // Main photo.
                    if (isset($authors[$author_id]['photo'])) {
                        $authors[$author_id]['photo_path'] = xarModAPIfunc(
                            'mag', 'user', 'imagepaths',
                            array(
                                'path' => $image_author_photo_vpath,
                                'fields' => array(
                                    'author_id' => $author_id,
                                    'photo' => $authors[$author_id]['photo'],
                                )
                            )
                        );

                        // Icon version.
                        $authors[$author_id]['photo_icon'] = xarModAPIfunc(
                            'mag', 'user', 'imagepaths',
                            array(
                                'path' => $image_author_icon_vpath,
                                'fields' => array(
                                    'author_id' => $author_id,
                                    'photo' => $authors[$author_id]['photo'],
                                )
                            )
                        );
                    }
                }
            }

            // Group the authors by whatever.
            if ($groupby == 'article') {
                foreach($articles as $key1 => $article) {
                    foreach($article as $key2 => $author_id) {
                        if (isset($authors[$author_id])) {
                            $articles[$key1][$key2] = $authors[$author_id];
                        } else {
                            // Uathor is missing - we found a link, but there is no author record.
                            unset($articles[$key1][$key2]);
                        }
                    }
                }
                $return = $articles;
            } else {
                // Default grouping is just by author: just return the list of authors.
                $return = $authors;
            }
        }
    }

    return $return;
}

?>