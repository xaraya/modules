<?php
/**
 * Surveys create a new survey for on user
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team 
 */
/*
 * Create a new user survey record.
 *
 * Called from user-startsurvey
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param date $startdate OPTIONAL
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return id  $usid The user survey ID
 *
 * @throws      exceptionclass  [description]                [OPTIONAL A REQURIED]
 *
 * @access      public                                       [OPTIONAL A REQURIED]
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 * @since      [Date of first inclusion long date format ]   [REQURIED]
 * @deprecated Deprecated [release version here]             [AS REQUIRED]
 */

function surveys_adminapi_createusersurvey($args) {
    extract($args);

    // TODO: validate arguments (sid, uid)

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    if (!isset($start_date)) {
        $start_date = time();
    }

    $idname = 'xar_usid';
    $tablename = $xartable['surveys_user_surveys'];

    // Insert the user survey.
    $query = 'INSERT INTO ' . $tablename
        . ' (xar_usid, xar_user_id, xar_survey_id, xar_status, xar_start_date)'
        . ' VALUES(?, ?, ?, ?, ?)';
    $nextID = $dbconn->GenId($tablename);
    $result = $dbconn->execute($query,
        array($nextID, $uid, (int)$sid, 'PROGRESS', $start_date)
    );
    if (!$result) {return;}
    $usid = (int)$dbconn->PO_Insert_ID($tablename, $idname);

    // TODO: hooks for the creation of a user survey? Probably not necessary, for now.

    return $usid;
}

?>