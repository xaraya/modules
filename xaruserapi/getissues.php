<?php

/**
 * Get magazine issues.
 *
 * @param mid integer Magazine ID
 * @param startnum integer Start number to fetch from
 * @param numitems integer Number of items to fetch
 * @param startdate integer Start timestamp (pubdate)
 * @param enddate integer End timestamp (pubdate)
 * @param iid integer Issue ID
 * @param iids array Array of issue IDs
 * @param ref string Issue reference
 * @param docount boolean If set, count issues instead of returning them.
 *
 * @todo Support 'count'
 */

function mag_userapi_getissues($args)
{
    extract($args);

    // Get module parameters
    extract(xarModAPIfunc('mag', 'user', 'params',
        array(
            'knames' => 'module,modid,itemtype_issues,image_issue_cover_vpath,sort_default_issues'
        )
    ));

    // Only used for some text escaping methods.
    $dbconn =& xarDBGetConn();

    $return = array();

    // Get the magazine.
    // Force the selection of a magazine - must not mix issues from different mags.
    $current_mag = xarModAPIfunc($module, 'user', 'currentmag', $args);

    if (!empty($current_mag)) {
        extract($current_mag);

        // Default sort order
        $sort = $sort_default_issues;

        // Initialise return value.
        $return = array();

        // Search criteria.
        $params = array (
            'module' => $module,
            'itemtype' => $itemtype_issues,
            'sort' => $sort,
        );

        $where = array();

        // Start date
        if (isset($startdate) && is_numeric($startdate)) {
            $where[] = 'pubdate ge ' . (integer)$startdate;
        }

        // End date
        if (isset($enddate) && is_numeric($enddate)) {
            $where[] = 'pubdate le ' . (integer)$enddate;
        }

        // Status
        if (!empty($status)) {
            if (is_string($status)) $status = array($status);
            $where[] = "status in ('" . implode("','", $status) . "')";
        }

        // Magazine ID
        // TODO: mandatory.
        if (!empty($mid) && is_numeric($mid)) {
            $where[] = 'mag_id eq ' . (integer)$mid;
        }

        // Issue ID
        if (!empty($iid) && is_numeric($iid)) {
            $where[] = 'iid eq ' . (integer)$iid;
        }

        // Issue IDs
        if (xarVarValidate('list:id', $iids, true) && !empty($iids)) {
            $where[] = 'iid in (' . implode(',', $iids) . ')';
        }

        // Issue ref
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

            // Fetch the matching issues.
            $issues = xarModAPIfunc('dynamicdata', 'user', 'getitems', $params);
        }

        if (!empty($issues)) {
            foreach($issues as $issue) {
                // Add some additional useful information into the list of issues.
                // Substitution variables are used so that path can be varied as required.
                $issue['cover_img_path'] = xarModAPIfunc(
                    'mag', 'user', 'imagepaths',
                    array(
                        'path' => $image_issue_cover_vpath,
                        'fields' => array(
                            'mag_ref' => $mag['ref'],
                            'issue_ref' => $issue['ref'],
                            'issue_cover' => $issue['cover_img'],
                        )
                    )
                );

                $return[$issue['iid']] = $issue;
            }
        }
    } else {
        // No current magazine selected.
        if (!empty($docount)) return 0;
    }

    return $return;
}

?>