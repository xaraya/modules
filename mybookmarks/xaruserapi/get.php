<?php
/**
 * get a specific link
 * @poaram $args['id'] id of link to get
 * @returns array
 * @return link array, or false on failure
 */
function mybookmarks_userapi_get($args)
{
    extract($args);
    if (!isset($id)) {
        $msg = xarML('Invalid Parameter Count);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    if(!xarSecurityCheck('Viewmybookmarks')) return;
    $table = $xartable['mybookmarks'];

    // Get link
    $query = "SELECT xar_bm_name,
                     xar_bm_url
            FROM $table
            WHERE xar_id = ?";
    $bindvars = array($id);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($name, $url) = $result->fields;
    $item   = array('xar_bm_name'      => $name,
                    'xar_bm_url'       => $url);
    $result->Close();
    return $item;
}
?>