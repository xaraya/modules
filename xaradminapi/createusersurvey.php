<?php
/**
 * Surveys create a new survey for one user
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/**
 * Create a new user survey record.
 *
 * Called from user-startsurvey. This function records when a user has
 * started a new survey. It creates the link between the user and the survey
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param date $startdate OPTIONAL
 * @param id    $sid  survey id
 * @param id    $uid The userid that starts this survey
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
 */

function surveys_adminapi_createusersurvey($args) {
    extract($args);

    // TODO: check validate arguments (sid, uid)
    // Validation
    $invalid = array();
    if (!isset($sid) || !is_numeric($sid)) {
        $invalid[] = 'sid';
    }
    if (!isset($uid) || !is_numeric($uid)) {
        $invalid[] = 'uid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'createusersurvey', 'Surveys');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

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