<?php

/**
 * Checks for most recent update and updates DB if needed.
 * 
 * @author J. Cox <niceguyeddie@xaraya.com>
 * @access private
 */
function sitecloud_schedulerapi_compare()
{
    // Security Check
	if(!xarSecurityCheck('Overviewsitecloud')) return;

    $links  = xarModAPIFunc('sitecloud', 'user', 'getall');
    foreach ($links as $link){
        // We need to grab the current url right now for the string and the date
        $filedata = xarModAPIFunc('base', 'user', 'getfile',
                                  array('url'       =>  $link['title'],
                                        'cached'    =>  false));

        $compare['string']     = md5($filedata);
        $compare['date']       = time();

        if ($compare['string'] != $link['string']){
            // Get datbase setup
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $sitecloudtable = $xartable['sitecloud'];

            // Update the link
            $query = "UPDATE $sitecloudtable
                      SET xar_string   = '" . xarVarPrepForStore($compare['string']) . "',
                          xar_date     = '" . xarVarPrepForStore($compare['date']) . "'
                      WHERE xar_id  = " . xarVarPrepForStore($link['id']);
            $result =& $dbconn->Execute($query);
            if (!$result) return;
        }
    }
    return true;
}
?>