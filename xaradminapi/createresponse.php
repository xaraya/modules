<?php
/**
 * Surveys table definitions function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/*
 * Short Description [REQUIRED one line description]
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 *
 * @return int  type and name returned                       [OPTIONAL A REQURIED]
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
/*
 * Create a survey 'type' record.
 * It is added to the group hierarchy.
 */

function surveys_adminapi_createresponse($args) {
    extract($args);

    // TODO: validate arguments

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $idname = 'xar_rid';
    $tablename = $xartable['surveys_user_responses'];

    // Insert the user response.
    $query = 'INSERT INTO ' . $tablename
        . ' (xar_rid, xar_user_survey_id, xar_question_id, xar_status, xar_value1, xar_value2, xar_value3)'
        . ' VALUES(?, ?, ?, ?, ?, ?, ?)';
    $nextID = $dbconn->GenId($tablename);
    $result = $dbconn->execute($query,
        array($nextID, (int)$user_survey_id, (int)$question_id, $status, $value1, $value2, $value3)
    );
    if (!$result) {return;}
    $rid = (int)$dbconn->PO_Insert_ID($tablename, $idname);

    // TODO: hooks for the creation of a response?
    // Need to look up the response type, based on the question details.

    return $rid;
}

?>