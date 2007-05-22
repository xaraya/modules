<?php

/**
 * get a specific URL
 * @poaram $args['id'] id of item to get
 * @returns array
 * @return link array, or false on failure
 */
function ping_userapi_get($args)
{
    extract($args);
    if (empty($id) || !is_numeric($id)) {
        $msg = xarML('Invalid Ping ID');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Security Check
    if(!xarSecurityCheck('Readping')) return;
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $pingtable = $xartable['ping'];
    // Get URL
    $query = "SELECT xar_id,
                     xar_url,
                     xar_method
            FROM $pingtable
            WHERE xar_id = ?";
    $result =& $dbconn->Execute($query, array((int)$id));
    if (!$result) return;
    list($id, $url, $method) = $result->fields;
    $result->Close();
    $link = array('id'      => $id,
                  'url'     => $url,
                  'method'  => $method);
    return $link;
}
?>