<?php
/**
 * update a smilies
 * @param $args['id'] the ID of the code
 * @param $args['code'] the new code of the code
 * @param $args['name'] long name of the code
 * @param $args['description'] description
 * @param $args['transform'] transform
 */
function bbcode_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    if ((!isset($id)) ||
        (!isset($tag)) ||
        (!isset($name))) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('bbcode',
                          'user',
                          'get',
                          array('id' => $id));

    if ($link == false) {
        $msg = xarML('No Such :) Present');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }

    // Security Check
    if(!xarSecurityCheck('EditBBCode')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['bbcode'];

    // Update the link
    $query = "UPDATE $table
            SET xar_tag    = ?,
                xar_name    = ?,
                xar_description = ?,
                xar_transformed = ?
            WHERE xar_id = ?";
    $bindvars = array($tag, $name, $description, $transform, $id);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let the calling process know that we have finished successfully
    return true;
}
?>