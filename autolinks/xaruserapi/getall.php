<?php

/**
 * get all links
 * @returns array
 * @return array of links, or false on failure
 */
function autolinks_userapi_getall($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    // Security Check
    if(!xarSecurityCheck('ReadAutolinks')) return;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];

    // Extra where-clause conditions.
    $where = array();

    // TODO: put where-clause stuff in a common function
    if (isset($enabled))
    {
        if (empty($enabled))
        {
            // Only return disabled links.
            $where[] = '(xar_enabled = 0 or xar_enabled is null)';
        } else {
            // Only return enabled links.
            $where[] = 'xar_enabled = 1';
        }
    }

    if (isset($valid))
    {
        if (empty($valid))
        {
            // Only return valid links.
            $where[] = '(xar_valid = 0 or xar_valid is null)';
        } else {
            // Only return invalid links.
            $where[] = 'xar_valid = 1';
        }
    }

    if (count($where) > 0)
    {
        $where = 'where ' . implode(' and ', $where);
    } else {
        $where = '';
    }

    // Initialise.
    $links = array();

    // Get links
    $query = 'SELECT xar_lid,
                   xar_keyword,
                   xar_title,
                   xar_url,
                   xar_comment,
                   xar_enabled,
                   xar_valid,
                   xar_match_re,
                   xar_cache_replace,
                   xar_sample
            FROM ' . $autolinkstable . ' ' . $where
        . ' ORDER BY xar_keyword';
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($lid, $keyword, $title, $url, $comment, $enabled, $valid, $match_re, $cache_replace, $sample) = $result->fields;
    	if(xarSecurityCheck('ReadAutolinks',0,'All',"$keyword:$lid")) {
            $links[] = array('lid' => $lid,
                             'keyword' => $keyword,
                             'title' => $title,
                             'url' => $url,
                             'comment' => $comment,
                             'enabled' => $enabled,
                             'valid' => $valid,
                             'match_re' => $match_re,
                             'cache_replace' => $cache_replace,
                             'sample' => $sample);
        }
    }

    $result->Close();

    return $links;
}

?>