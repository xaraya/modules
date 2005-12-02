<?php
/**
 * File: $Id:
 * 
 * Create an index on a text
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
 * create an index on a text
 * 
 * @author curtisdf 
 * @param  $args ['tid'] text ID to apply change to
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function bible_adminapi_textindex($args)
{
    extract($args);

    $invalid = array();
    if (!isset($tid) || !is_numeric($tid)) {
        $invalid[] = 'tid';
    } 
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'textindex', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    } 

    // need to get short name of this text before we do security check
    $text = xarModAPIFunc('bible', 'user', 'get', array('tid' => $tid));

    // security check
    if (!xarSecurityCheck('AddBible', 1, 'Text', "$text[sname]:$text[tid]")) {
        return;
    }

    // get database and table parameters
    list($dbconn,
         $texttable) = xarModAPIFunc('bible', 'user', 'getdbconn', array('tid' => $tid));



    return true;
} 

?>
