<?php

/**
 * get a specific link
 * @param $args['lid'] id of link to get
 * @returns array
 * @return link array, or false on failure
 */
function autolinks_userapi_get($args)
{
    extract($args);

    if (!isset($lid)) {
        $msg = xarML('Invalid Parameter Count',
                    'userapi', 'get', 'autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];

    // Get link
    $query = "SELECT xar_lid,
                   xar_keyword,
                   xar_title,
                   xar_url,
                   xar_comment,
                   xar_enabled,
                   xar_valid,
                   xar_match_re,
                   xar_cache_replace,
                   xar_sample
            FROM $autolinkstable
            WHERE xar_lid = " . xarVarPrepForStore($lid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($lid, $keyword, $title, $url, $comment, $enabled, $valid, $match_re, $cache_replace, $sample) = $result->fields;
    $result->Close();

    // Security Check
    if(!xarSecurityCheck('ReadAutolinks')) return;

    $link = array('lid' => $lid,
                  'keyword' => $keyword,
                  'title' => $title,
                  'url' => $url,
                  'comment' => $comment,
                  'enabled' => $enabled,
                  'valid' => $valid,
                  'match_re' => $match_re,
                  'cache_replace' => $cache_replace,
                  'sample' => $sample);

    return $link;
}

?>