<?php

/**
 * get a specific headline
 * @poaram $args['id'] id of item to get
 * @returns array
 * @return link array, or false on failure
 */
function ping_userapi_get($args)
{
    extract($args);
    if (empty($id) || !is_numeric($id)) {
        $msg = xarML('Invalid Ping ID');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Security Check
	if(!xarSecurityCheck('Readping')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pingtable = $xartable['ping'];
    // Get headline
    $query = "SELECT xar_id,
                     xar_url,
                     xar_method
            FROM $pingtable
            WHERE xar_id = " . xarVarPrepForStore($id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    list($id, $url, $method) = $result->fields;
    $result->Close();
    $link = array('id'      => $id,
                  'url'     => $url,
                  'method'  => $method);
    return $link;
}
?>