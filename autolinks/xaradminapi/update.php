<?php

/**
 * update an autolink
 * @param $args['lid'] the ID of the link
 * @param $args['keyword'] the new keyword of the link
 * @param $args['title'] the new title of the link
 * @param $args['url'] the new url of the link
 * @param $args['comment'] the new comment of the link
 */
function autolinks_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Array of column set statements.
    $set = array();

    // TODO: use xarVarFetch to validate the parameters.

    // String parameters.
    foreach(array('keyword', 'title', 'url', 'comment') as $parameter)
    {
        if (isset($$parameter))
        {
            $set[] = "xar_$parameter = '" . xarVarPrepForStore($$parameter) . "'";
        }
    }
    
    // Numeric parameters.
    foreach(array('enabled'=>0, 'valid'=>0, 'match_re'=>0) as $parameter => $default)
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
                    join(', ',$invalid), 'admin', 'update', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Create the 'set' statement.
    $set = implode(', ', $set);

    // The user API function is called
    $link = xarModAPIFunc('autolinks',
                         'user',
                         'get',
                         array('lid' => $lid));

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
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];

    // Update the link
    $query = 'UPDATE ' . $autolinkstable . ' SET ' . $set
            . ' WHERE xar_lid = ' . xarVarPrepForStore($lid);
    $result =& $dbconn->Execute($query);
    if (!$result) {return;}

    // Let the calling process know that we have finished successfully
    return true;
}

?>