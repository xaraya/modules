<?php
/**
 * File: $Id:
 * 
 * Get a database connection to a Bible text
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf 
 */
/**
 * get a Bible database connection
 * 
 * @author curtisdf 
 * @param  $args ['tid'] (optional) id of text to get
 * @returns array
 * @return text array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_getdbconn($args)
{ 
    extract($args); 

    if (isset($tid) && !is_numeric($tid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // prepare database connection
    if (xarModGetVar('bible', 'altdb')) {

        // use alternate database
        $dbinfo = array('databaseType' => xarModGetVar('bible', 'altdbtype'),
                        'databaseHost' => xarModGetVar('bible', 'altdbhost'),
                        'databaseName' => xarModGetVar('bible', 'altdbname'),
                        'userName' => xarModGetVar('bible', 'altdbuname'),
                        'password' => xarModGetVar('bible', 'altdbpass')
                       );
        $dbconn = xarDBNewConn($dbinfo);

    } else {

        // use same database as Xar
        $dbconn = xarDBGetConn();

    }

    // generate table name if requested
    if (isset($tid)) {

        // return both db connection and table name
        $xartable = xarDBGetTables();
        $texttable = $xartable['bible_text'];
        $texttable .= '_'.$tid;

        return array($dbconn, $texttable);

    } else {

        // return db connection only
        return $dbconn;
    }

} 

?>
