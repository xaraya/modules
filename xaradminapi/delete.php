<?php

/**
 * delete an autolink
 * @param $args['lid'] ID of the link
 * @returns bool
 * @return true on success, false on failure
 */
function autolinks_adminapi_delete($args)
{
    // Security check
    if(!xarSecurityCheck('DeleteAutolinks')) {return;}

    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($lid) || !is_numeric($lid)) {
        $msg = xarML('Invalid Parameter Count in #(3)_#(1)_#(2)', 
            'admin', 'delete', 'Autolinks'
        );
        xarErrorSet(
            XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg)
        );
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc(
        'autolinks', 'user', 'get',
        array('lid' => $lid)
    );

    if ($link == false) {
        $msg = xarML('No such link present');
        xarErrorSet(
            XAR_USER_EXCEPTION, 
            'MISSING_DATA',
            new DefaultUserException($msg)
        );
        return;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];

    // Delete the item
    $query = 'DELETE FROM ' . $autolinkstable
          . ' WHERE xar_lid = ?';
    $result =& $dbconn->Execute($query, array($lid));
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks(
        'item', 'delete', $lid, 
        array('itemtype' => $link['itemtype'], 'module' => 'autolinks')
    );

    // Let the calling process know that we have finished successfully
    return true;
}

?>