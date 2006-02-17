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
 * Get all surveys for a user.
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * usid: user-survey ID; or
 * uid: user ID and
 * sid: survey ID; or name: survey name
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

function surveys_userapi_getusersurveys($args)
{
    // TODO: cache this in a global so that a user
    // survey update can flush it.
    static $cached_usid = -1;
    static $cached_results;

    // Expand arguments.
    extract($args);

    // If the 'current_user' flag is true, then extract only for the
    // current logged in user.
    if (!empty($current_user)) {
        // If the user is logged in, then use the UID, else use the
        // session ID to identify the user (for anonymous surveys).
        if (xarUserIsLoggedIn()) {
            $uid = (int)xarUserGetVar('uid');
        } else {
            $uid = xarSessionGetId();
        }
    }

    // Return the cached user survey details if available.
    // This function can be called many times in a survey page.
    if (isset($usid) && $usid == $cached_usid) {
        //echo " RETURN CACHED ";
        return $cached_results;
    }

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $bind = array();
    $where = array();
    $extra_status = '';
    $extra_user = '';

    // Join to the surveys status table.
    $extra_status .= ' INNER JOIN ' . $xartable['surveys_status'] . ' AS survey_status'
        . ' ON survey_status.xar_status = user_survey.xar_status'
        . ' AND survey_status.xar_type = \'SURVEY\'';

    // Requested statuses may be supplied as a CSV list or an array.
    if (isset($system_status)) {
        if (is_string($system_status)) {
            $system_status = explode(',', $system_status);
        }

        $bind = array_merge($bind, $system_status);

        // Join the status table to the user surveys table.
        $extra_status .= ' AND survey_status.xar_system_status IN (?' . str_repeat(',?', count($system_status)-1) . ')';
    }

    //$username = 'a%';
    // Username matching (wildcard)
    if (isset($username)) {
        // Need to join to the roles table.
        $extra_user .=
            ' INNER JOIN ' . $xartable['roles'] . ' AS roles'
            . ' ON roles.xar_uid = user_survey.xar_user_id'
            . ' AND roles.xar_type = 0'
            . ' AND (roles.xar_uname LIKE ? OR roles.xar_uname LIKE ? OR roles.xar_uname LIKE ?)';
        $bind[] = (string)$username;
        $bind[] = strtoupper($username);
        $bind[] = strtolower($username);
    }

    // User-survey ID should be enough on its own.
    if (isset($usid)) {
        $bind[] = (int)$usid;
        $where[] = 'user_survey.xar_usid = ?';
    }

    if (isset($uid)) {
        // Select for user.
        $bind[] = (string)$uid;
        $where[] = 'user_survey.xar_user_id = ?';
    }

    if (isset($sid)) {
        // Select for survey ID.
        $bind[] = (int)$sid;
        $where[] = 'survey.xar_sid = ?';
    }

    if (isset($name)) {
        // Select for survey name.
        $bind[] = $name;
        $where[] = 'survey.xar_name = ?';
    }

    if (isset($status)) {
        // Select for survey status (user status).
        // Supports CSV list of statuses.
        $status = explode(',', $status);
        $bind = array_merge($bind, $status);
        $where[] = 'user_survey.xar_status IN (?' . str_repeat(',?', count($status)-1) . ')';
    }

    // Formulate the query.
    $query = 'SELECT survey.xar_sid, user_survey.xar_usid, user_survey.xar_user_id,'
        . ' survey.xar_group_id,'
        . ' user_survey.xar_status, survey_status.xar_system_status, survey_status.xar_ssid,'
        . ' survey.xar_name, survey.xar_desc,'
        . ' survey_status.xar_desc, survey.xar_summary_template,'
        . ' xar_start_date, xar_submit_date, xar_closed_date, xar_last_updated'
        . ' FROM ' . $xartable['surveys_surveys'] . ' AS survey'
        . ' INNER JOIN ' . $xartable['surveys_user_surveys'] . ' AS user_survey'
        . ' ON user_survey.xar_survey_id = survey.xar_sid'
        . $extra_status
        . $extra_user
        . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '');
    //echo $query.'<br/><br/>'; var_dump($bind); echo '<br/><br/>';

    $result = $dbconn->execute($query, $bind);
    if (!$result) {return;}

    // List of survey IDs for doing DD lookups.
    $ssids = array();
    $sids = array();

    $usersurveys = array();
    while (!$result->EOF) {
        list($sid, $usid, $uid, $gid, $status, $system_status, $ssid, $name, $desc, $status_desc, $summary_template, $start_date, $submit_date, $closed_date, $last_updated) = $result->fields;

        $sid = (int)$sid;
        $usid = (int)$usid;
        $uid = (int)$uid;
        $gid = (int)$gid;
        $ssid = (int)$ssid;

        $usersurvey = array(
            'sid' => $sid,
            'usid' => $usid,
            'uid' => $uid,
            'gid' => $gid,
            'ssid' => $ssid,
            'status' => $status,
            'system_status' => $system_status,
            'name' => $name,
            'desc' => $desc,
            'status_desc' => $status_desc,
            'summary_template' => $summary_template,
            'start_date' => $start_date,
            'submit_date' => $submit_date,
            'closed_date' => $closed_date,
            'last_updated' => $last_updated
        );

        $usersurveys[] =& $usersurvey;
        unset($usersurvey);

        $ssids[$ssid] = $ssid;
        $sids[$sid] = $sid;

        // Get next survey.
        $result->MoveNext();
    }
    //var_dump($usersurveys);

    // Language suffix used to merge language-suffixed DD property data.
    $lang_suffix = xarModAPIfunc('surveys', 'user', 'getlanguagesuffix');

    // TODO: DD data for the survey.

    // If there are DD hooks for the survey, then fetch them now.
    $survey_type = xarModAPIfunc(
        'surveys', 'user', 'gettype',
        array('type' => 'S')
    );
    if (!empty($survey_type) && xarModIsHooked('dynamicdata', 'surveys', $survey_type['tid'])) {
        $dd_data_surveys = xarModAPIfunc(
            'dynamicdata', 'user', 'getitems',
            array('module' => 'surveys', 'itemtype' => $survey_type['tid'], 'itemids' => array_keys($sids))
        );
    }

    // If there are DD hooks for the statuses, then fetch them now.
    $status_type = xarModAPIfunc(
        'surveys', 'user', 'gettype',
        array('type' => 'T')
    );
    if (!empty($status_type) && xarModIsHooked('dynamicdata', 'surveys', $status_type['tid'])) {
        $dd_data_status = xarModAPIfunc(
            'dynamicdata', 'user', 'getitems',
            array('module' => 'surveys', 'itemtype' => $status_type['tid'], 'itemids' => array_keys($ssids))
        );
    }

    if (!empty($dd_data_surveys) || !empty($dd_data_status)) {
        foreach($usersurveys as $key => $usersurvey) {
            // Merge in survey DD properties.
            if (!empty($dd_data_surveys[$usersurvey['sid']])) {
                foreach ($dd_data_surveys[$usersurvey['sid']] as $dd_name => $dd_value) {
                    if (!isset($usersurvey[$dd_name])) {
                        $usersurveys[$key][$dd_name] = $dd_value;
                        // Copy language-suffixed fields (desc and name).
                        if (!empty($lang_suffix) && !empty($dd_value) && 'desc' . $lang_suffix == $dd_name) {
                            $usersurveys[$key]['desc'] = $dd_value;
                        }
                        if (!empty($lang_suffix) && !empty($dd_value) && 'name' . $lang_suffix == $dd_name) {
                            $usersurveys[$key]['name'] = $dd_value;
                        }
                    }
                }
            }

            // Merge in survey status DD properties.
            if (!empty($dd_data_status[$usersurvey['ssid']])) {
                foreach ($dd_data_status[$usersurvey['ssid']] as $dd_name => $dd_value) {
                    if (!isset($usersurvey[$dd_name])) {
                        $usersurveys[$key][$dd_name] = $dd_value;
                        // Copy language-suffixed fields (status_desc).
                        if (!empty($lang_suffix) && !empty($dd_value) && 'status_desc' . $lang_suffix == $dd_name) {
                            $usersurveys[$key]['status_desc'] = $dd_value;
                        }
                    }
                }
            }
        }
    }
    //var_dump($dd_data);

    // Save the result in the cache if the usid was supplied.
    if (isset($args['usid'])) {
        $cached_usid = $args['usid'];
        $cached_results = $usersurveys;
    }

    return $usersurveys;
}

?>