<?php

/**
 * update an autolink
 * @param $args['lid'] the ID of the link
 * @param $args['keyword'] the new keyword of the link (optional)
 * @param $args['title'] the new title of the link (optional)
 * @param $args['url'] the new url of the link (optional)
 * @param $args['comment'] the new comment of the link (optional)
 * @param $args['sample'] sample link string (optional)
 * @param $args['name'] name of the link (optional)
 * @param $args['tid'] link type ID (optional)
 */
function autolinks_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Array of column set statements.
    $set = array();

    // TODO: use xarVarFetch to validate the parameters.

    if (isset($tid)) {
        // Map tid (for the API) to type_tid (for the table).
        $type_tid = $tid;
    }

    // String parameters.
    foreach(array('keyword', 'title', 'url', 'comment', 'sample', 'name', 'type_tid') as $parameter)
    {
        if (isset($$parameter))
        {
            $set[] = "xar_$parameter = '" . xarVarPrepForStore($$parameter) . "'";
        }
    }
    
    // Numeric parameters.
    foreach(array('enabled'=>0, 'match_re'=>0) as $parameter => $default)
    {
        if (isset($$parameter))
        {
            if ($$parameter == "0" || $$parameter == "1")
            {
                $set[] = "xar_$parameter = " . xarVarPrepForStore($$parameter) . "";
            } else {
                $set[] = "xar_$parameter = " . xarVarPrepForStore($default) . "";
            }
        }
    }
    
    // Argument check
    if (!isset($lid) || empty($set)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ', $args), 'admin', 'update', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Create the 'set' statement.
    $set = implode(', ', $set);

    // The user API function is called
    $link = xarModAPIFunc(
        'autolinks', 'user', 'get',
        array('lid' => $lid)
    );

    if ($link == false) {
        $msg = xarML('No Such Link Present',
                    'autolinks');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return;
    }

    if (!xarSecurityCheck('EditAutolinks')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];

    // Update the link
    $query = 'UPDATE ' . $autolinkstable . ' SET ' . $set
            . ' WHERE xar_lid = ' . xarVarPrepForStore($lid);
    $result =& $dbconn->Execute($query);
    if (!$result) {return;}

    // Call hooks to update DD etc.
    xarModCallHooks(
        'item', 'update', $lid,
        array('itemtype' => $link['itemtype'], 'module' => 'autolinks')
    );
    
    // Now recompile the cache for autolink. Do this after the hooks
    // are called as the cache may make use of DD property values.
    $result = xarModAPIfunc(
        'autolinks', 'admin', 'updatecache',
        array('lid' => $lid)
    );
    if (!$result) {return;}

    // Let the calling process know that we have finished successfully
    return true;
}

?>