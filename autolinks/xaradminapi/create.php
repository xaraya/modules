<?php

/**
 * create a new autolink
 * @param $args['keyword'] keyword of the item
 * @param $args['title'] title of the item
 * @param $args['url'] url of the item
 * @param $args['comment'] comment of the item
 * @returns int
 * @return autolink ID on success, false on failure
 */
function autolinks_adminapi_create($args)
{

    // Get arguments from argument array
    extract($args);

    // Optional arguments
    if (!isset($comment)) {
        $comment = '';
    }

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($keyword)) ||
        (!isset($title)) ||
        (!isset($url))) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'create', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if(!xarSecurityCheck('AddAutolinks')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];

    // Check if that username exists
    $query = "SELECT xar_lid FROM $autolinkstable
            WHERE xar_keyword='".xarVarPrepForStore($keyword)."';";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    if ($result->RecordCount() > 0) {
        $msg = xarML('The selected keyword already has an entry.');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($autolinkstable);

    // Add item
    $query = "INSERT INTO $autolinkstable (
              xar_lid,
              xar_keyword,
              xar_title,
              xar_url,
              xar_comment)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($keyword) . "',
              '" . xarVarPrepForStore($title) . "',
              '" . xarVarPrepForStore($url) . "',
              '" . xarVarPrepForStore($comment) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $lid = $dbconn->PO_Insert_ID($autolinkstable, 'xar_lid');

    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $lid, 'lid');

    // Return the id of the newly created link to the calling process
    return $lid;
}

?>