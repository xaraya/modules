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
 * Import legacy data from V2.
 */

require_once "modules/surveys/convert/ConvertCharset.class.php";

function surveys_admin_importlegacy() {
    // Get a character conversion object.
    $encoding_object = new ConvertCharset;
    //$NewFileOutput = $encoding_object->Convert($FileText, $FromCharset, $ToCharset, $Entities);
    $FromCharset = 'windows-1252'; //'iso-8859-1'; // 'windows-1251';
    $ToCharset = 'utf-8';

    if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
        // No privilege for editing survey structures.
        return false;
    }

    // Extend the execution time to allow for extended execution.
    set_time_limit(60*20);

    // Get the user ('%' for all users)
    xarVarFetch('user', 'pre:lower:trim', $p_user, NULL, XARVAR_NOT_REQUIRED);

    if (empty($p_user)) {
        echo "Must supply 'user' parameter";
        return;
    }

    // Database stuff.
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Select all matching users.
    $query = 'SELECT email, username, password, displayname, startdate, completiondate'
        . ' FROM v3_users WHERE username LIKE ? AND imported = \'N\' ORDER BY username';
    $resultx = $dbconn->execute($query, array($p_user));
    if (!$resultx) {return;}

    while (!$resultx->EOF) {
        list($email, $dbusername, $password, $displayname, $startdate, $completiondate) = $resultx->fields;
        $username = $encoding_object->Convert($dbusername, $FromCharset, $ToCharset, 0);
        $displayname = $encoding_object->Convert($displayname, $FromCharset, $ToCharset, 0);
        //var_dump($startdate); echo " unix=".strtotime($startdate);

        // Convert dates to Unix dates.
        $startdate = (!empty($startdate) ? strtotime($startdate) : NULL);
        $completiondate = (!empty($completiondate) ? strtotime($completiondate) : NULL);

        // We have a user.
        echo "<h2>username=$username</h2>email=$email password=$password displayname=$displayname<br/>";

        // Create the user it they do not already exist.
        // Get the existing user, if possible.
        $current_user = xarModAPIfunc('roles', 'user', 'get', array('uname' => $username));

        if (empty($current_user)) {
            echo "New user. Creating...";

            $new_uid = xarModAPIfunc(
                'roles', 'admin', 'create',
                array(
                    'uname' => $username,
                    'realname' => $displayname,
                    'email' => $email,
                    'pass' => $password,
                    'date' => time(),
                    'valcode' => 'imported',
                    'state' => 3 // active
                )
            );
            if (empty($new_uid)) {
                echo "**FAILED TO CREATE USER**";
                return;
            }
            echo "created<br/>";
            $current_user = xarModAPIfunc('roles', 'user', 'get', array('uid' => $new_uid));
        } else {
            echo "User exists<br/>";
            //var_dump($current_user);
        }
        echo "Checking user group...";
        $addmember = xarModAPIfunc(
            'roles', 'user', 'addmember',
            array('uid' => $current_user['uid'], 'gid' => 5) // 5='Users' group
        );
        echo "done<br/>";
        // Now we have the user details in the database.

        // Create a survey for the user.
        // Check that a survey has not already been created.
        // Get the existing survey for this user.
        $current_survey = xarModAPIfunc(
            'surveys', 'user', 'getusersurvey',
            array('uid' => $current_user['uid'], 'sid' => 2)
        );

        if (empty($current_survey)) {
            echo "Survey does not exist. Creating...";
            $new_usid = xarModAPIfunc(
                'surveys', 'admin', 'createusersurvey',
                array(
                    'uid' => $current_user['uid'],
                    'sid' => 2,
                    'start_date' => (!empty($startdate) ? $startdate : time())
                )
            );
            if (empty($new_usid)) {
                echo "** cannot create user survey **";
                return;
            }
            // Get the survey details now it has been created.
            $current_survey = xarModAPIfunc(
                'surveys', 'user', 'getusersurvey',
                array('usid' => $new_usid)
            );
            echo "created<br/>";
        } else {
            echo "User survey exists<br/>";
        }
        //echo " ==".$current_survey['usid']; var_dump($current_survey);


        // **************************************
        // Start with the 'START' type questions.
        // **************************************
        $query = 'SELECT SITE0_1, SITE0_2, SITE0_3, SITE0_4, SITE0_5, SITE0_6,'
            . ' SITE1_0a, SITE1_1, SITE1_2, SITE1_3, SITE1_4, SITE1_5, SITE1_6, SITE1_7, SITE1_8, SITE1_9,'
            . ' START2_1, EP_START_4, SITE2_2, SITE2_3, SITE2_4, SITE2_5, SITE2_7,'
            . ' EP_START_1c, EP_START_1d,'
            . ' EP_START_2, EP_START_3, EP_START_6, EP_START_7, EP_START_8, EP_START_9, EP_START_10'
            . ' FROM v3_start_answers'
            . ' WHERE username = ?'
            . ' ORDER BY username';
        $result = $dbconn->execute($query, array($username));
        if (!$result) {return;}
        $ep_start_4 = array();

        while (!$result->EOF) {
            $responses = $result->GetRowAssoc(0);
            //var_dump($responses);
            foreach($responses as $question_name => $response) {
                $question_name = str_replace('_', '-', strtoupper($question_name));
                echo "EP: $question_name = '".xarVarPrepForDisplay($response)."'<br/>";

                $response = trim($response, ', ');

                // Charset conversions.
                if (!empty($response) && !is_numeric($response)) {
                    $response = $encoding_object->Convert($response, $FromCharset, $ToCharset, 0);
                }

                // Where the result will end up in most circumstances.
                $responses = array('value1' => $response);

                if ($question_name == 'SITE2-7' || $question_name == 'EP-START-3') {
                    $responses = array('value3' => $response);
                }

                // TODO: store this one and repeat it at the end.
                if ($question_name == 'EP-START-4') {
                    $query2 = 'SELECT id, sector_id'
                        . ' FROM remas_ep_subsectors'
                        . ' WHERE nace = ?';
                    $result2 = $dbconn->execute($query2, array($response));
                    if (!$result2) {return;}

                    if (!$result2->EOF) {
                        list($subsector_id, $sector_id) = $result2->fields;
                        $responses['value1'] = $response; //$subsector_id;
                        $responses['value2'] = $sector_id;

                        if (!empty($response) || !empty($sector_id)) {
                            $ep_start_4 = array('value1' => $response, 'value2' => $sector_id, 'value3' => '');
                        }
                    } else {
                        echo "** response '$response' not a valid NACE code<br/>";
                    }

                    $responses['value3'] = '';
                }

                $x = xarModAPIfunc(
                    'surveys', 'admin', 'importresponse',
                    array(
                        'usid' => $current_survey['usid'],
                        'name' => $question_name,
                        'response' => $responses
                    )
                );
            }

            // Next START question to import.
            $result->MoveNext();
        }


        // **************************************
        // Next the 'EMA' type questions.
        // **************************************

        $query = 'SELECT v3questionno, emaset, questionno, value1, value3'
            . ' FROM v3_ema_answers'
            . ' WHERE username = ?'
            . ' AND questionno NOT LIKE \'evidence%\''
            . ' ORDER BY v3questionno';
        $result = $dbconn->execute($query, array($username));
        if (!$result) {return;}

        while (!$result->EOF) {
            list($question_name, $emaset, $questionno, $value1, $value3) = $result->fields;

            // Charset conversions.
            if (!empty($value1) && !is_numeric($value1)) {
                $value1 = $encoding_object->Convert($value1, $FromCharset, $ToCharset, 0);
            }
            if (!empty($value3)) {
                $value3 = $encoding_object->Convert($value3, $FromCharset, $ToCharset, 0);
            }

            // Translate 'none' to '0' for some questions.
            // Some items also need value3 moving to value1.
            // Some of these will be strings, even though they should be numbers
            // They will just have to sit invalid until manually corrected.
            if (preg_match('/EMA4-13-[1234]/i', $question_name)) {
                $value1 = $value3;
                if (strtolower($value1) == 'none' || $value1 === '') {
                    $value1 = 0;
                }
            }

            // A comment is mandatory if the answer is 'unable'.
            // Default it to get it to load.
            if ($value1 == 'unable' && $value3 == '') {
                $value3 = "No reason given";
            }

            // This question has lots of instances of 'none' for '0'
            if (preg_match('/EMA3-12-1/i', $question_name) || preg_match('/EMA3-9-1/i', $question_name)) {
                if (strtolower($value1) == 'none' || $value1 === '') {
                    $value1 = 0;
                }
            }

            // A few responses (even multiple-choice) are stored in value3.
            // No idea why - they just are.
            if (preg_match('/EMA1-5-[123]/i', $question_name)) {
                $value1 = $value3;
            }

            // Split the comment of question EMA2-7-1 into the two date fields.
            if (preg_match('/EMA2-7-1/i', $question_name)) {
                // Translate 1/2 into no/yes
                if ($value1 == 2) {
                    $value1 = 'yes';
                } else {
                    $value1 = 'no';
                }

                if ($value1 == 'yes') {
                    // If there are two CSV values in the comment, then split them off.
                    $dates = explode(',', $value3);
                    if (count($dates) == 2) {
                        // There were two values. Store them.
                        // Many of the dates use an invalid format, but that can be picked up later.
                        echo "EMA: EMA2-7-2 = ".xarVarPrepForDisplay(trim($dates[0]))."<br/>";
                        echo "EMA: EMA2-7-3 = ".xarVarPrepForDisplay(trim($dates[1]))."<br/>";
                        $x = xarModAPIfunc(
                            'surveys', 'admin', 'importresponse',
                            array(
                                'usid' => $current_survey['usid'],
                                'name' => 'EMA2-7-2',
                                'response' => array('value1' => trim($dates[0]))
                            )
                        );
                        $x = xarModAPIfunc(
                            'surveys', 'admin', 'importresponse',
                            array(
                                'usid' => $current_survey['usid'],
                                'name' => 'EMA2-7-3',
                                'response' => array('value1' => trim($dates[1]))
                            )
                        );
                    }
                }
            }

            // Trim any leading or trailing commas from multi-choice responses.
            $value1 = trim($value1, ',');

            echo "EMA: $question_name = ".xarVarPrepForDisplay($value1)."<br/>";

            $x = xarModAPIfunc(
                'surveys', 'admin', 'importresponse',
                array(
                    'usid' => $current_survey['usid'],
                    'name' => $question_name,
                    'response' => array('value1' => $value1, 'value3' => $value3)
                )
            );

            // Next EMA question to import.
            $result->MoveNext();
        }


        // **************************************
        // Next the 'EMA Evidence' type questions.
        // **************************************

        $query = 'SELECT v3questionno, emaset, questionno, value1'
            . ' FROM v3_ema_answers'
            . ' WHERE username = ?'
            . ' AND questionno LIKE \'evidence%\''
            . ' ORDER BY v3questionno, value1';
        $result = $dbconn->execute($query, array($username));
        if (!$result) {return;}

        $evidence = array(
            'EMA1-6-1' => array(),
            'EMA2-10-1' => array(),
            'EMA3-19-1' => array(),
            'EMA4-15-1' => array(),
            'EMA5-6-1' => array(),
            'EMA6-7-1' => array()
        );
        while (!$result->EOF) {
            list($question_name, $emaset, $questionno, $value1) = $result->fields;

            if (isset($evidence[$question_name]) && !empty($value1)) {
                $evidence[$question_name][] = trim($value1);
            }

            //echo "EVIDENCE: $question_name = ".xarVarPrepForDisplay($value1)."<br/>";

            // Next EMA evidence question to import.
            $result->MoveNext();
        }
        foreach($evidence as $evident_name => $evident_value) {
            $evident_value = implode(',', $evident_value);
            echo "EVIDENCE: $evident_name = '$evident_value'<br/>";
            $x = xarModAPIfunc(
                'surveys', 'admin', 'importresponse',
                array(
                    'usid' => $current_survey['usid'],
                    'name' => $evident_name,
                    'response' => array('value1' => $evident_value)
                )
            );
        }


        // **************************************
        // Next the 'EP' type questions (EP1 and EP2).
        // **************************************

        $query = 'SELECT v3questionno, value1, value3'
            . ' FROM v3_ep_1and2'
            . ' WHERE username = ?'
            . ' ORDER BY v3questionno';
        $result = $dbconn->execute($query, array($username));
        if (!$result) {return;}

        while (!$result->EOF) {
            list($question_name, $value1, $value3) = $result->fields;

            echo "EMA: $question_name = ".xarVarPrepForDisplay($value1)."<br/>";

            // The main value.
            $x = xarModAPIfunc(
                'surveys', 'admin', 'importresponse',
                array(
                    'usid' => $current_survey['usid'],
                    'name' => $question_name,
                    'response' => array('value1' => $value1)
                )
            );

            // The associated comment.
            $x = xarModAPIfunc(
                'surveys', 'admin', 'importresponse',
                array(
                    'usid' => $current_survey['usid'],
                    // The comment question has a postfix.
                    'name' => $question_name . '-1',
                    'response' => array('value3' => $value3)
                )
            );

            // Next EP1/2 question to import.
            $result->MoveNext();
        }


        // **************************************
        // Next the 'EP' type questions (EP3 to EP8).
        // **************************************
        // TODO: fill in the unvisited gaps? Do by outer join at Access end?

        $query = 'SELECT v3questionno, value1, value2, value3, indicator_name'
            . ' FROM v3_ep_indicators'
            . ' WHERE username = ?'
            . ' ORDER BY v3questionno';
        $result = $dbconn->execute($query, array($username));
        if (!$result) {return;}

        $all_eps = array('EP3'=>'EP3','EP4'=>'EP4','EP5'=>'EP5','EP6'=>'EP6','EP7'=>'EP7','EP8'=>'EP8');
        if (!$result->EOF) {
            $last_ep = '';
            $ind_no = 0;
            while (!$result->EOF) {
                list($question_name, $value1, $value2, $value3, $indicator_name) = $result->fields;

                // Increment the indicator number for each value within an EP.
                // TODO: blank entry required if EP stops before 3 or does not have any.
                if ($last_ep <> $question_name) {
                    if ($ind_no < 3 && $ind_no > 0) {
                        // Not completely filled, so create a blank entry.
                        $blank_name = $last_ep . '-' . ($ind_no+1) . '-1';
                        echo "EP: $blank_name = blank<br/>";
                        $x = xarModAPIfunc(
                            'surveys', 'admin', 'importresponse',
                            array(
                                'usid' => $current_survey['usid'],
                                'name' => $blank_name,
                                'response' => array('value1' => '', 'value2' => '', 'value3' => '')
                            )
                        );
                    }

                    $last_ep = $question_name;
                    $ind_no = 1;
                    unset($all_eps[$question_name]);
                } else {
                    $ind_no += 1;
                }
                $question_name = $question_name . '-' . $ind_no . '-1';

                echo "EP: $question_name = $value1/$value2/$value3<br/>";

                $x = xarModAPIfunc(
                    'surveys', 'admin', 'importresponse',
                    array(
                        'usid' => $current_survey['usid'],
                        'name' => $question_name,
                        'response' => array('value1' => $value1, 'value2' => $value2, 'value3' => $value3)
                    )
                );

                // Next EP question to import.
                $result->MoveNext();
            }
            // Add a blank entry after the last EP, if required.
            if ($ind_no < 3 && $ind_no > 0) {
                // Not completely filled, so create a blank entry.
                $blank_name = $last_ep . '-' . ($ind_no+1) . '-1';
                echo "EP: $blank_name = blank<br/>";
                $x = xarModAPIfunc(
                    'surveys', 'admin', 'importresponse',
                    array(
                        'usid' => $current_survey['usid'],
                        'name' => $blank_name,
                        'response' => array('value1' => '', 'value2' => '', 'value3' => '')
                    )
                );
            }
        }

        foreach($all_eps as $extra_blank) {
            $blank_name = $extra_blank . '-1-1';
            echo "EP: $blank_name = blank<br/>";
            $x = xarModAPIfunc(
                'surveys', 'admin', 'importresponse',
                array(
                    'usid' => $current_survey['usid'],
                    'name' => $blank_name,
                    'response' => array('value1' => '', 'value2' => '', 'value3' => '')
                )
            );
        }

        // Default the EP-START-4 (sub-sector) if it has not been set.
        if (empty($ep_start_4)) {
            echo "EP: EP-START-4 = default<br/>";
            $ep_start_4 = array('value1' => '', 'value2' => '', 'value3' => 'not known');

            $x = xarModAPIfunc(
                'surveys', 'admin', 'importresponse',
                array(
                    'usid' => $current_survey['usid'],
                    'name' => 'EP-START-4',
                    'response' => $ep_start_4
                )
            );
        }

        // **************************************
        // Do the boiler-plate groups to show they
        // have been read.
        // **************************************

        $boilerplate = array('SURVEY1', 'EMA', 'EP');
        foreach($boilername as $boilerplate) {
            $x = xarModAPIfunc(
                'surveys', 'admin', 'importresponse',
                array(
                    'usid' => $current_survey['usid'],
                    'name' => $boilername,
                    'response' => array('value1' => '', 'value2' => '', 'value3' => '')
                )
            );
        }

        // Apply the response rules for this user.
        xarModAPIfunc('surveys', 'admin', 'applyresponserules', $current_survey);

        // Update the last-update time on the user survey
        xarModAPIfunc('surveys', 'admin', 'update', array('usid' => $current_survey['usid'], 'last_updated' => time()));

        $query = 'UPDATE v3_users SET imported = \'Y\''
            . ' WHERE username LIKE ? AND imported = \'N\'';
        $resulty = $dbconn->execute($query, array($dbusername));
        if (!$resulty) {return;}

        // Next user to import.
        $resultx->MoveNext();
    }


    return "** no text **";
}

?>