<?php

/**
 * get a specific smiley
 * @poaram $args['sid'] id of smiley to get
 * @returns array
 * @return link array, or false on failure
 */
function smilies_userapi_get($args)
{
    extract($args);

    if (!isset($sid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'smilies');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $smiliestable = $xartable['smilies'];

    // Get link
    $query = "SELECT xar_sid,
                   xar_code,
                   xar_icon,
                   xar_emotion
            FROM $smiliestable
            WHERE xar_sid = " . xarVarPrepForStore($sid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($sid, $code, $icon, $emotion) = $result->fields;
    $result->Close();

    // Security Check
	if(!xarSecurityCheck('OverviewSmilies')) return;

    $link = array('sid'     => $sid,
                  'code'    => $code,
                  'icon'    => $icon,
                  'emotion' => $emotion);

    return $link;
}
?>