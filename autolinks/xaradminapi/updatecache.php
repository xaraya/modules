<?php

/**
 * update the replacement cache sring for an autolink or range of autolinks
 * @param $args['lid'] the ID of the link; 0 for all links (optional)
 * @param $args['tid'] the ID of the link type (optional)
 */
function autolinks_adminapi_updatecache($args)
{
    // Get arguments from argument array
    extract($args);

    if (!isset($lid) || !is_numeric($lid)) {
        $lid = 0;
    }
    
    if (!isset($tid) || !is_numeric($tid)) {
        $tid = 0;
    }

    // Argument check
    if (empty($lid) && empty($tid)) {
        $msg = xarML(
            'Invalid Parameter Count',
            'admin', 'updatecache', 'Autolinks'
        );
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // The user API function is called to get the link(s).
    // Return an array of one or more links to the next stage.
    if (!empty($lid)) {
        $links = xarModAPIFunc(
            'autolinks', 'user', 'get',
            array('lid' => $lid)
        );
        if ($links) {
            $links = array($lid => $links);
        }
    } else {
        $links = xarModAPIFunc(
            'autolinks', 'user', 'getall',
            array('tid' => $tid)
        );
    }

    if (is_array($links)) {
        if (!xarSecurityCheck('EditAutolinks')) {return;}

        // Get database setup
        $dbconn =& xarDBGetConn();
        $xartable =& xarDBGetTables();

        $autolinkstable = $xartable['autolinks'];

        foreach($links as $lid => $link) {
            // Get the replace string.
            $replace = xarModAPIfunc(
                'autolinks', 'user', 'getreplace',
                array('link'=> &$link)
            );

            $query = 'UPDATE ' . $autolinkstable
                . ' SET xar_cache_replace = ?'
                . ' WHERE xar_lid = ' . $lid 
                . ' AND (xar_cache_replace <> ?'
                . ' OR xar_cache_replace IS NULL)';
            $result =& $dbconn->Execute($query, array($replace, $replace));
        }
    }

    // Let the calling process know that we have finished successfully
    return true;
}

?>