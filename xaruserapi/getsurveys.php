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
 * Get all survey records.
 * sid: survey ID
 * name: survey name
 * TODO: cache results
 */

function surveys_userapi_getsurveys($args) {
    // Expand arguments.
    extract($args);

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $bind = array();
    $where = array();

    // The sid is optional.
    if (isset($sid)) {
        $bind[] = (int)$sid;
        $where[] = 'xar_sid = ?';
    }

    // The name is optional.
    if (isset($name)) {
        $bind[] = $name;
        $where[] = 'xar_name = ?';
    }

    if (!isset($survey_key)) {$survey_key = 'index';}

    // Formulate the query.
    $query = 'SELECT xar_sid, xar_name, xar_desc, xar_group_id, xar_summary_template,'
        . ' xar_max_instances, xar_max_in_progress, xar_anonymous'
        . ' FROM ' . $xartable['surveys_surveys']
        . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '');

    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    $surveys = array();
    $index = 0;
    while (!$result->EOF) {
        list($sid, $name, $desc, $gid, $summary_template, $max_instances, $max_in_progress, $anonymous) = $result->fields;

        // TODO: merge in DD fields to allow surveys to be
        // given ML names.
        $survey = array(
            'sid' => (int)$sid,
            'name' => $name,
            'desc' => $desc,
            'gid' => (int)$gid,
            'summary_template' => $summary_template,
            'max_instances' => (int)$max_instances,
            'max_in_progress' => (int)$max_in_progress,
            'anonymous' => ($anonymous == 'Y' ? true : false)
        );

        // TODO: allow index to be specified - index/id
        $surveys[($survey_key=='id' ? $sid : $index)] =& $survey;
        unset($survey);

        // Get next survey.
        $result->MoveNext();
        $index++;
    }

    return $surveys;
}

?>