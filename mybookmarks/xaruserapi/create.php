<?php
/**
 * create a new bookmark
 * @param $args['url'] url of the item
 * @param $args['name'] name of the item
 * @param $args['uid'] uid of the user.
 * @returns int
 * @return ID on success, false on failure
 */
function mybookmarks_userapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($url)) ||
        (!isset($name)) ||
        (!isset($uid))) {
        $msg = xarML('Invalid Parameter Count Create');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Security Check
    if(!xarSecurityCheck('EditBBCode')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['mybookmarks'];
    // Get next ID in table
    $nextId = $dbconn->GenId($table);
    // Add item
    $query = "INSERT INTO $table (
              xar_bm_id,
              xar_user_name,
              xar_bm_name,
              xar_bm_url)
            VALUES (
                  ?,
                  ?,
                  ?,
                  ?)";
    $bindvars = array($nextId, $uid, $name, $url);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Get the ID of the item that we inserted
    $id = $dbconn->PO_Insert_ID($table, 'xar_bm_id');
    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $id, 'id');
    // Return the id of the newly created link to the calling process
    return $id;
}
?>