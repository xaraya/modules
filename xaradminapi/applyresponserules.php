<?php
/**
 * Surveys apply rules for user survey
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
 * Apply the response rules for a user survey.
 *
 * The results are written back to the database so that
 * groups (and their responses) are physically disabled.
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param id    $usid  User Survey ID
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

function surveys_adminapi_applyresponserules($args)
{
    // Expand arguments.
    extract($args);

    // Get details of user survey.
    $usersurvey = xarModAPIfunc('surveys', 'user', 'getusersurvey', $args);
    if (!$usersurvey) {return;}

    // We want to get the ruleset, then apply the rules.
    // - disable user groups (and existing responses) where the group is NA
    //   - include all descendants.
    // - enable user groups (and existing responses) where the group is not NA
    //   - include all ancestors
    // - create user groups where a rule applies but those groups do not exist
    //   (i.e. where they have not been visited)

    // Get a list of all groups for this survey.
    // We will use this to help negotiate the groups tree.
    $groups = xarModAPIfunc(
        'surveys', 'user', 'getgroups',
        array('gid' => $usersurvey['gid'])
    );

    // Get the rules for this survey.
    $rules = xarModAPIfunc('surveys', 'user', 'getsurveyrules', array('sid' => $usersurvey['sid']));
    if (!$rules) {return;} // TODO: distinguish between no rules and an error in the rules.
    //var_dump($rules);

    // Now we can apply the rules, one-by-one.
    foreach ($rules as $rule) {
        // Just to make things a litter easier.
        $rulegid = (int)$rule['gid'];

        // If the group is already disabled, then there is no need to disable it further.
        if (isset($groups['items'][$rulegid]['disabled'])) {
            // Jump to the next rule in the list.
            continue;
        }

        // Loop for each rule condition.
        // If the logic is OR, then the first true condition will pass the rule.
        // If the logic is AND, then the first failed condition will fail the rule.
        // Set default result.
        $ruleresult = ($rule['logic'] == 'OR') ? false : true;

        foreach($rule['condition_array'] as $condition) {
            // The rule will be an array consisting of a command and parameters,
            // similar to core Xaraya validation rules.
            if (count($condition) < 1) {
                // There is nothing in this condition - skip it.
                continue;
            }
            // The first part of the condition is the name.
            $condition_name = array_shift($condition);

            // The result can be negated by prefixing the name with '!'
            if (strpos($condition_name, '!') === 0) {
                $condition_name = substr($condition_name, 1);
                $not_condition = true;
            } else {
                $not_condition = false;
            }

            // Attempt to execute the condition processing function.
            // Suppress 'not found' errors.
            $condition_result = xarModAPIfunc(
                'surveys', 'rules', $condition_name,
                array(
                    'sid' => $usersurvey['sid'],
                    'uid' => $usersurvey['uid'],
                    'usid' => $usersurvey['usid'],
                    'params' => $condition
                ),
                // Suppress errors being added to the error stack.
                false
            );
            // TODO: error handling:-
            //   NULL - the condition function does not exist (cannot make a decision either way).
            //   (-1) - the condition raised an internal error.
            //   true/false - the condition executed and passed or failed.
            if ($condition_result === NULL || $condition_result === -1) {continue;}

            // Reverse the condition result when using '!'
            $condition_result = ($not_condition xor $condition_result);

            if ($condition_result && $rule['logic'] == 'OR') {
                // First true OR condition sets the rule result.
                $ruleresult = true;
                break;
            }
            if (!$condition_result && $rule['logic'] == 'AND') {
                // First false AND condition sets the rule result.
                $ruleresult = false;
                break;
            }
        }

        if ($ruleresult && !isset($groups['items'][$rulegid]['disabled'])) {
            // The rule passed, so this group (and decendants) should be disabled.
            // ********************************************************************
            // If a group is newly disabled, then go through the responses and set
            // their status to 'NA'.
            // At the end of this process, go through the list of non-disabled
            // statuses and set those currently 'NA' to 'INVALID'.
            // That process will disable responses that have been answered, but
            // subsequently become irrelevant when a dependant question causes
            // the groups those questions are in to be disabled. Also vice-versa,
            // where a disabled response subseqently becomes re-enabled (and the
            // old value comes back, but probably need revalidating).
            // The two operations could probably be done in a single SQL statement.
            // Perhaps, as this is supposed to be a read-only function, the
            // updates can be done in an admin function, afer being passed the
            // complete site map.
            // ********************************************************************

            // Prepare to update descendants.
            $left = $groups['items'][$rulegid]['xar_left'];
            $right = $groups['items'][$rulegid]['xar_right'];

            // Loop through each group to disable descendants in the array.
            foreach($groups['items'] as $item_gid => $item) {
                // Continue if we have not reached the current item tree.
                if ($item['xar_left'] < $left) {continue;}
                // Break if we have gone beyond the current item tree.
                if ($item['xar_right'] > $right) {break;}

                // If not disabled then disable it now.
                if (!isset($item['disabled'])) {
                    $groups['items'][$item_gid]['disabled'] = 'disabled';
                    $groups['items'][$item_gid]['disabled'] = 'disabled';
                    //echo " disabling $item[gid] ";
                } else {
                    // If it is already disabled, then we can assume all children
                    // from that point are also disabled, and break out of the loop.
                    // We just want to avoid as many loop iterations as possible.
                    break;
                }
            }
        }

        // Now update the user survey (in the database).
        // Get two arrays - the enabled and the disabled group IDs.
        $enabled = array();
        $disabled = array();
        foreach ($groups['items'] as $item) {
            if (isset($item['disabled'])) {
                $disabled[(int)$item['gid']] = (int)$item['gid'];
            } else {
                $enabled[(int)$item['gid']] = (int)$item['gid'];
            }
        }
    }

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    // TODO: create blank group records where they do not exist.
    // This should normally be done when the survey is first initiated.
    if (count($disabled) > 0) {
        // Update the user survey groups.
        $query = 'UPDATE ' . $xartable['surveys_user_groups']
            . ' SET xar_status = \'NA\' WHERE xar_status <> \'NA\''
            . ' AND xar_user_survey_id = ?'
            . ' AND xar_group_id IN(?'.str_repeat(', ?', count($disabled)-1).')';

        // array_unshift() better?
        $bind = array_merge(array((int)$usersurvey['usid']), $disabled);
        $result = $dbconn->execute($query, $bind);
        if (!$result) {return;}

        // We also have to create rows with a status 'NA' where they do not yet
        // exist, otherwise the groups always show as 'NORESPONSE', even when
        // they should be disabled.

        $query = 'SELECT xar_group_id FROM ' . $xartable['surveys_user_groups']
            . ' WHERE xar_status = \'NA\''
            . ' AND xar_user_survey_id = ?'
            . ' AND xar_group_id IN(?'.str_repeat(', ?', count($disabled)-1).')';

        // Use the same bind values from the update statement.
        $result = $dbconn->execute($query, $bind);
        if (!$result) {return;}

        // Knock the retrieved rows off the 'disabled' array, one-by-one.
        while (!$result->EOF) {
            list($xar_group_id) = $result->fields;
            if (isset($disabled[$xar_group_id])) {
                unset($disabled[$xar_group_id]);
            }

            // Get next item.
            $result->MoveNext();
        }

        if (!empty($disabled)) {
            // There were one or more groups that do not yet exist, but
            // need to be disabled. Create those groups now.
            //var_dump($disabled);
            foreach($disabled as $new_gid) {
                // TODO: move this to an API function.
                $ugid = $dbconn->GenId($xartable['surveys_user_groups']);
                $query = 'INSERT INTO ' . $xartable['surveys_user_groups']
                    . ' (xar_ugid, xar_user_survey_id, xar_group_id, xar_status)'
                    . ' VALUES(?, ?, ?, ?)';
                $result = $dbconn->execute($query, array((int)$ugid, (int)$usersurvey['usid'], (int)$new_gid, 'NA'));
                $ugid = (int)$dbconn->PO_Insert_ID($xartable['surveys_user_groups'], 'xar_ugid');
            }
        }



        // Any rows updated? If so, update the responses too.
        $rowcount = $dbconn->Affected_Rows();

        if ($rowcount > 0 || !$dbconn->hasAffectedRows) {
            // Get the responses that have an NA group, and are not NA themselves.
            $query = 'SELECT qresponses.xar_rid'
                . ' FROM ' . $xartable['surveys_user_responses'] . ' AS  qresponses'
                . ' INNER JOIN ' . $xartable['surveys_question_groups'] . ' AS qgroups'
                . ' ON qgroups.xar_question_id = qresponses.xar_question_id'
                . ' INNER JOIN ' . $xartable['surveys_user_groups'] . ' AS usergroups'
                . ' ON usergroups.xar_user_survey_id = qresponses.xar_user_survey_id'
                . ' AND usergroups.xar_group_id = qgroups.xar_group_id'
                . ' WHERE usergroups.xar_user_survey_id = ?'
                . ' AND qresponses.xar_status <> \'NA\' AND usergroups.xar_status = \'NA\'';

            $result = $dbconn->execute($query, array((int)$usersurvey['usid']));
            if (!$result) {return;}

            $rids = array();
            while (!$result->EOF) {
                list($rid) = $result->fields;
                $rids[] = (int)$rid;

                // Get next item.
                $result->MoveNext();
            }

            if (count($rids) > 0) {
                // Update the responses.
                $query = 'UPDATE ' . $xartable['surveys_user_responses']
                    . ' SET xar_status = \'NA\''
                    . ' WHERE xar_rid IN(?'.str_repeat(', ?', count($rids)-1).')';

                $result = $dbconn->execute($query, $rids);
                if (!$result) {return;}
            }
        }
    } // count($disabled) > 0


    if (count($enabled) > 0) {
        // Update the user survey groups.
        // TODO: perhaps set to INVALID or NORESPONSE depending upon whether
        // there is a response to the group or not.
        $query = 'UPDATE ' . $xartable['surveys_user_groups']
            . ' SET xar_status = \'NORESPONSE\' WHERE xar_status = \'NA\''
            . ' AND xar_user_survey_id = ?'
            . ' AND xar_group_id IN(?'.str_repeat(', ?', count($enabled)-1).')';

        $bind = array_merge(array((int)$usersurvey['usid']), $enabled);
        $result = $dbconn->execute($query, $bind);
        if (!$result) {return;}

        // Any rows updated? If so, update the responses too.
        $rowcount = $dbconn->Affected_Rows();

        if ($rowcount > 0 || !$dbconn->hasAffectedRows) {
            // Get the responses that have a non-NA group, and are NA currently.
            $query = 'SELECT qresponses.xar_rid'
                . ' FROM ' . $xartable['surveys_user_responses'] . ' AS  qresponses'
                . ' INNER JOIN ' . $xartable['surveys_question_groups'] . ' AS qgroups'
                . ' ON qgroups.xar_question_id = qresponses.xar_question_id'
                . ' INNER JOIN ' . $xartable['surveys_user_groups'] . ' AS usergroups'
                . ' ON usergroups.xar_user_survey_id = qresponses.xar_user_survey_id'
                . ' AND usergroups.xar_group_id = qgroups.xar_group_id'
                . ' WHERE usergroups.xar_user_survey_id = ?'
                . ' AND qresponses.xar_status = \'NA\' AND usergroups.xar_status <> \'NA\'';

            $result = $dbconn->execute($query, array((int)$usersurvey['usid']));
            if (!$result) {return;}

            $rids = array();
            while (!$result->EOF) {
                list($rid) = $result->fields;
                $rids[] = (int)$rid;

                // Get next item.
                $result->MoveNext();
            }

            if (count($rids) > 0) {
                // Update the responses.
                $query = 'UPDATE ' . $xartable['surveys_user_responses']
                    . ' SET xar_status = \'INVALID\''
                    . ' WHERE xar_rid IN(?'.str_repeat(', ?', count($rids)-1).')';

                $result = $dbconn->execute($query, $rids);
                if (!$result) {return;}
            }
        }
    } // count($enabled) > 0

    return true;
}

?>
