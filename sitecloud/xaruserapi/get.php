<?php
/**
 * get a specific headline
 * @poaram $args['id'] id of headline to get
 * @returns array
 * @return link array, or false on failure
 */
function sitecloud_userapi_get($args)
{
    extract($args);
    if (empty($id) || !is_numeric($id)) {
        $msg = xarML('Invalid Cloud ID');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Security Check
	if(!xarSecurityCheck('Overviewsitecloud')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitecloudtable = $xartable['sitecloud'];
    // Get headline
    $query = "SELECT xar_id,
                     xar_title,
                     xar_url,
                     xar_string,
                     xar_date
            FROM $sitecloudtable
            WHERE xar_id = " . xarVarPrepForStore($id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($id, $title, $url, $string, $date) = $result->fields;
    $result->Close();
    $data = array('id'      => $id,
                  'title'   => $title,
                  'url'     => $url,
                  'string'  => $string,
                  'date'    => $date);
    // Get categories (if any)
    if (xarModIsHooked('categories','sitecloud')) {
        $cids = xarModAPIFunc('categories','user','getlinks',
                              array('iids' => array($id),
                                    //'itemtype' => 0, // not needed here
                                    'modid' => xarModGetIDFromName('sitecloud'),
                                    'reverse' => 1));
        if (isset($cids[$id]) && is_array($cids[$id])) {
            $data['cids'] = $cids[$id];
            $date['catid'] = join('+',$cids[$id]);
        }
    }
    return $data;
}
?>
