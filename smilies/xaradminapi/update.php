<?php
/**
 * update a smilies
 * @param $args['sid'] the ID of the :)
 * @param $args['code'] the new code of the :)
 * @param $args['icon'] the new icon of the :)
 * @param $args['emotion'] the new emotion of the :)
 */
function smilies_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    if ((!isset($sid)) ||
        (!isset($code)) ||
        (!isset($icon)) ||
        (!isset($emotion))) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('smilies',
                          'user',
                          'get',
                          array('sid' => $sid));

    if ($link == false) {
        $msg = xarML('No Such :) Present', 'smilies');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }

    // Security Check
	if(!xarSecurityCheck('EditSmilies')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $smiliestable = $xartable['smilies'];

    // Update the link
    $query = "UPDATE $smiliestable
            SET xar_code    = '" . xarVarPrepForStore($code) . "',
                xar_icon    = '" . xarVarPrepForStore($icon) . "',
                xar_emotion = '" . xarVarPrepForStore($emotion) . "'
            WHERE xar_sid = " . xarVarPrepForStore($sid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let the calling process know that we have finished successfully
    return true;
}
?>