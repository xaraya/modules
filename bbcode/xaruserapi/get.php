<?php
/**
 * get a specific link
 * @poaram $args['cid'] id of link to get
 * @returns array
 * @return link array, or false on failure
 */
function bbcode_userapi_get($args)
{
    extract($args);
    if (!isset($id)) {
        $msg = xarML('Invalid Parameter Count in #(3)_#(1)_#(2).php', 'userapi', 'get', 'bbcode');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    if(!xarSecurityCheck('OverviewBBCode')) return;
    $table = $xartable['bbcode'];

    // Get link
    $query = "SELECT xar_id,
                   xar_tag,
                   xar_name,
                   xar_description,
                   xar_transformed
            FROM $table
            WHERE xar_id = ?";
    $bindvars = array($id);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    list($id, $tag, $name, $description, $transform) = $result->fields;
    $item   = array('id'               => $id,
                    'tag'              => $tag,
                    'name'             => $name,
                    'description'      => $description,
                    'transform'        => $transform);
    $result->Close();
    return $item;
}
?>