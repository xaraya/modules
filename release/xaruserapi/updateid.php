<?php

function release_userapi_updateid($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($rid)) ||
        (!isset($uid)) ||
        (!isset($name)) ||
        (!isset($type))) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'update', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('release',
                          'user',
                          'getid',
                          array('rid' => $rid));

    if ($link == false) {
        $msg = xarML('No Such Release ID Present',
                    'release');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    // Security Check
    if(!xarSecurityCheck('OverviewRelease')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    if (empty($approved)){
        $approved = '1';
    }

    $releasetable = $xartable['release_id'];

    // Update the link
    $query = "UPDATE $releasetable
            SET xar_uid = '" . xarVarPrepForStore($uid) . "',
                xar_name = '" . xarVarPrepForStore($name) . "',
                xar_type = '" . xarVarPrepForStore($type) . "',
                xar_desc = '" . xarVarPrepForStore($desc) . "',
                xar_certified = '" . xarVarPrepForStore($certified) . "',
                xar_approved = '" . xarVarPrepForStore($approved) . "'
            WHERE xar_rid = " . xarVarPrepForStore($rid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'update', $rid, 'rid');

    // Return the id of the newly created user to the calling process
    return $rid;
}

?>