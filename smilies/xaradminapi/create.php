<?php
/**
 * create a new smilies
 * @param $args['code'] code of the item
 * @param $args['icon'] icon of the item
 * @param $args['emotion'] description of the item
 * @returns int
 * @return smilies ID on success, false on failure
 */
function smilies_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($code)) ||
        (!isset($icon)) ||
        (!isset($emotion))) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Security Check
	if(!xarSecurityCheck('AddSmilies')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $smiliestable = $xartable['smilies'];
    // Get next ID in table
    $nextId = $dbconn->GenId($smiliestable);
    // Add item
    $query = "INSERT INTO $smiliestable (
              xar_sid,
              xar_code,
              xar_icon,
              xar_emotion)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($code) . "',
              '" . xarVarPrepForStore($icon) . "',
              '" . xarVarPrepForStore($emotion) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    // Get the ID of the item that we inserted
    $sid = $dbconn->PO_Insert_ID($smiliestable, 'xar_sid');
    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $sid, 'sid');
    // Return the id of the newly created link to the calling process
    return $sid;
}
?>