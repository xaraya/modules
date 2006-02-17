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
 * Get the user groups for a given survey.
 * The array returned will be a summary of question groups for
 * the specified user survey, providing a summary of progress
 * within that survey.
 * usid: user survey ID (instance of a survey in progress)
 */

function surveys_userapi_getusergroups($args) {
    extract($args);

    // User-survey ID is mandatory.
    if (!isset($usid)) {return;}

    // Database details.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $bind = array();
    $where = array();

    if (isset($usid)) {
        $where[] = 'xar_user_survey_id = ?';
        $bind[] = (int)$usid;
    }

    if (isset($ugid)) {
        $where[] = 'xar_ugid = ?';
        $bind[] = (int)$ugid;
    }

    if (isset($gid)) {
        $where[] = 'xar_group_id = ?';
        $bind[] = (int)$gid;
    }

    // Query all response groups for this user survey.
    $query = 'SELECT xar_ugid, xar_group_id, xar_user_survey_id, xar_status'
        . ' FROM ' . $xartable['surveys_user_groups']
        . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '');
    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    $items = array();
    while (!$result->EOF) {
        // Get columns.
        list($ugid, $gid, $user_survey_id, $status) = $result->fields;
        $gid = (int)$gid;

        $items[$gid] = array(
            'gid' => $gid,
            'status' => $status
        );

        // Get next item.
        $result->MoveNext();
    }

    return $items;
}

?>