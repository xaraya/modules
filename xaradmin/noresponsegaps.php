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
function surveys_admin_noresponsegaps() {
    // Only for survey admins.
    if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
        // No privilege for editing survey structures.
        return false;
    }

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    // Get a list of all groups for the survey.
    $groups = xarModAPIfunc('surveys', 'user', 'getgroups', array('sid'=>2));
    //var_dump($groups['items']);

    $gids = array();
    foreach($groups['items'] as $key => $group) {
        $gids [] = $key;
    }
    //var_dump($gids);

    // Loop for all user surveys.
    $usersurveys = xarModAPIfunc('surveys', 'user', 'getusersurveys');
    //var_dump($usersurveys);

    $query1 = 'SELECT xar_ugid FROM ' . $xartable['surveys_user_groups']
        . ' INNER JOIN remas_surveys_user_surveys '
        . ' ON remas_surveys_user_surveys.xar_usid = remas_surveys_user_groups.xar_user_survey_id'
        . ' AND remas_surveys_user_surveys.xar_survey_id = 2'
        . ' WHERE xar_user_survey_id = ? AND xar_group_id = ?';

    $query2 = 'SELECT count(xar_ugid)
        FROM remas_surveys_user_groups
        INNER join remas_surveys_user_surveys
        ON remas_surveys_user_surveys.xar_usid = remas_surveys_user_groups.xar_user_survey_id
        AND remas_surveys_user_surveys.xar_survey_id = 2
        WHERE xar_user_survey_id = ?
        GROUP BY xar_user_survey_id';

    $count = 0;
    foreach($usersurveys as $usersurvey) {
        set_time_limit(20);
        $usid = $usersurvey['usid'];
        $groups_created = false;

        $result2 = $dbconn->execute($query2, array($usid));
        if (!$result2) return;
        list($group_count) = $result2->fields;

        if ($group_count < 108) {

        foreach($gids as $gid) {
            $result1 = $dbconn->execute($query1, array($usid, $gid));
            if (!$result1) return;
            if ($result1->EOF) {
                echo " missing usid=$usid gid=$gid ";
                $ugid = $dbconn->GenId($xartable['surveys_user_groups']);
                $query = 'INSERT INTO ' . $xartable['surveys_user_groups']
                    . ' (xar_ugid, xar_user_survey_id, xar_group_id, xar_status)'
                    . ' VALUES(?, ?, ?, ?)';
                $result = $dbconn->execute($query, array((int)$ugid, (int)$usid, (int)$gid, 'NORESPONSE'));
                $ugid = (int)$dbconn->PO_Insert_ID($xartable['surveys_user_groups'], 'xar_ugid');
                $groups_created = true;
            }
        }
        if ($groups_created) {
            $applyresult = xarModAPIfunc('surveys', 'admin', 'applyresponserules', array('usid' => $usid));
            echo "<br/>";
            return "exit";
        }
        $count += 1;
        if ($count == 10) {
            //break;
        }
        }
    }

    return "x";
}

?>
