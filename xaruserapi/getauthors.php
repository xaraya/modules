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
 * @param docount boolean If set, speficies that a count should be returned instead.
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
            'knames' => 'module,modid,itemtype_authors,image_author_photo_vpath,image_author_icon_vpath'
        )
    ));

    // Used for some text escaping methods and queries..
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();

    // Handle numitems.
    // TODO: make this a parameter.
    if (empty($numitems)) $numitems = 100;
    if (empty($startnum)) $startnum = 1;

    // TODO: handle sorting, which can include data in other tables.
    // Note: can only sort by columns that are selected in some databases.
    $bind = array();
    $where = array();

    // Create SQL for pre-selecting of author IDs
    if (!empty($docount)) {
        $sql = 'SELECT COUNT(DISTINCT a.auid)';
    } else {
        $sql = 'SELECT DISTINCT a.auid';
    }

    $sql .= ' FROM ' . $tables['mag_authors'] . ' AS a';

    // Link to the articles (lots of reasons to do this).
    if (!empty($aid) || !empty($aids) || !empty($iid) || !empty($iids) || !empty($mid) || !empty($sid)) {
        $sql .= ' INNER JOIN ' . $tables['mag_articles_authors'] . ' AS aa'
            . ' ON aa.author_id = a.auid'
            . ' INNER JOIN ' . $tables['mag_articles'] . ' AS art'
            . ' ON art.aid = aa.article_id AND art.status = ?'
            . ' INNER JOIN ' . $tables['mag_issues'] . ' AS i'
            . ' ON i.iid = art.issue_id AND i.status = ?'
            . ' INNER JOIN ' . $tables['mag_mags'] . ' AS m'
            . ' ON m.mid = i.mag_id AND m.status = ?';

        $bind[] = 'PUBLISHED';  // Article
        $bind[] = 'PUBLISHED';  // Issue
        $bind[] = 'ACTIVE';     // Magazine
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

    // Fetch the authors.
    $result = $dbconn->SelectLimit($sql, $numitems, $startnum - 1, $bind);

    $author_ids = array();

    if ($result) {
        if (!empty($docount)) {
            // If we are doing a count, then just return that count.
            list($count) = $result->fields;
            return (integer)$count;
        } else {
            while (!$result->EOF) {
                list($author_id) = $result->fields;
                $result->MoveNext();
                $author_ids[] = $author_id;
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
            $return = $authors;
        }
    }

    return $return;
}

?>