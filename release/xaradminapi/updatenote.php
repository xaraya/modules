<?php

function release_adminapi_updatenote($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if  (!isset($rnid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'update', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('release',
                          'user',
                          'getnote',
                          array('rnid' => $rnid));

    if ($link == false) {
        $msg = xarML('No Such Release Note Present',
                    'release');
        xarExceptionSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    // Security Check
    if(!xarSecurityCheck('EditRelease')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasenotetable = $xartable['release_notes'];

    // Update the link
    $query = "UPDATE $releasenotetable
            SET xar_version = '" . xarVarPrepForStore($version) . "',
                xar_priceterms = '" . xarVarPrepForStore($priceterms) . "',
                xar_demolink = '" . xarVarPrepForStore($demolink) . "',
                xar_priceterms = '" . xarVarPrepForStore($priceterms) . "',
                xar_dllink = '" . xarVarPrepForStore($dllink) . "',
                xar_supportlink = '" . xarVarPrepForStore($supportlink) . "',
                xar_changelog = '" . xarVarPrepForStore($changelog) . "',
                xar_notes = '" . xarVarPrepForStore($notes) . "',
                xar_enotes = '" . xarVarPrepForStore($enotes) . "',
                xar_certified = '" . xarVarPrepForStore($certified) . "',
                xar_approved = '" . xarVarPrepForStore($approved) . "'
            WHERE xar_rnid = " . xarVarPrepForStore($rnid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'update', $rnid, 'rnid');

    // Return the id of the newly created user to the calling process
    return $rnid;
}

?>