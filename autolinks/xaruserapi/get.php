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

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];
    $autolinkstypestable = $xartable['autolinks_types'];

    // Get link
    $query = 'SELECT xar_lid,
                    xar_keyword,
                    xar_title,
                    xar_url,
                    xar_comment,
                    xar_enabled,
                    xar_match_re,
                    xar_cache_replace,
                    xar_sample,
                    xar_name,
                    xar_tid,
                    xar_dynamic_replace,
                    xar_template_name,
                    xar_type_name,
                    xar_type_desc,
                    xar_link_itemtype
            FROM    ' . $autolinkstable
        . ' LEFT JOIN ' . $autolinkstypestable 
        . ' ON xar_tid = xar_type_tid
            WHERE xar_lid = ' . xarVarPrepForStore($lid);
    $result =& $dbconn->Execute($query);

    if (!$result || $result->EOF) {return;}

    list(
        $lid, $keyword, $title, $url, $comment, $enabled, $match_re, $cache_replace, $sample, $name,
        $tid, $dynamic_replace, $template_name, $type_name, $type_desc, $itemtype
    ) = $result->fields;
    $result->Close();

    // Security Check
    if(!xarSecurityCheck('ReadAutolinks')) {return;}

    $link = array(
        'lid' => $lid,
        'keyword' => $keyword,
        'title' => $title,
        'url' => $url,
        'comment' => $comment,
        'enabled' => $enabled,
        'match_re' => $match_re,
        'cache_replace' => $cache_replace,
        'sample' => $sample,
        'name' => $name,
        'tid' => $tid,
        'dynamic_replace' => $dynamic_replace,
        'template_name' => $template_name,
        'type_name' => $type_name,
        'type_desc' => $type_desc,
        'itemtype' => $itemtype
    );

    return $link;
}

?>