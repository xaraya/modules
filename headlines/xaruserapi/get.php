<?

/**
 * get a specific headline
 * @poaram $args['lid'] id of link to get
 * @returns array
 * @return link array, or false on failure
 */
function headlines_userapi_get($args)
{
    extract($args);

    if (!isset($hid)) {
        $msg = xarML('Invalid Parameter Count', join(', ',$invalid), 'userapi', 'get', 'Headlines');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security Check
	if(!xarSecurityCheck('OverviewHeadlines')) return;

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $headlinestable = $xartable['headlines'];

    // Get link
    $categoriesdef = xarModAPIFunc('categories','user','leftjoin',
                                   array('cids' => array(),
                                         'modid' => xarModGetIDFromName('headlines')));

    // Get link
    $query = "SELECT xar_hid,
                     xar_title,
                     xar_desc,
                     xar_url,
                     xar_order,
                     {$categoriesdef['cid']}
            FROM $headlinestable
            LEFT JOIN {$categoriesdef['table']} ON {$categoriesdef['field']} = $headlinestable.xar_hid
            {$categoriesdef['more']}
            WHERE {$categoriesdef['where']} AND xar_hid = " . xarVarPrepForStore($hid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($hid, $title, $desc, $url, $order) = $result->fields;
    $result->Close();

    $link = array('hid'     => $hid,
                  'title'   => $title,
                  'desc'    => $desc,
                   'url'     => $url,
                  'order'   => $order);

    return $link;
}
?>