<?php
/**
 * Surveys table definitions function
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
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
 * Transfer the a user survey to the remote site.
 * This is CUSTOM - not generic - NOT for general release.
 *  debug: print debug messages if true
 *  usid: user survey ID
 * Returns:
 *  true: successful
 *  string: error message
 */

function surveys_adminapi_transfersurvey($args) {
    extract($args);

    if (!isset($debug)) {
        $debug = false;
    }

    $result = true;

    // URL of remote machine (the graphing module)
    $url_base = 'http://x.x.x.x/';

    // Get all responses for the user survey.
    $responses = xarModAPIfunc('surveys', 'user', 'getquestionresponses', array('usid' => $usid));
    //var_dump($responses);

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    // Registration form
    $reg_form_fields = array(
        'V3Id' => '',
        'UserType' => '1',
        'Username' => '',
        'Password' => '',
        'CompletionDate' => '',
        'Salutation' => '',
        'FirstName' => '',
        'LastName' => '',
        'EmailAddress' => '',
        'JobTitle' => NULL,
        'CompanyName' => '',
        'CompanyStreet' => '',
        'CompanyStreet1' => '',
        'CompanyTownCity' => '',
        'CompanyCountry' => '',
        'CompanyPostcode' => NULL,
        'CompanyTelephoneNo' => NULL,
        'CompanyWebsite' => NULL,
        'CompanyFaxNo' => NULL,
        'Sector' => '',
        'NACECode' => '',
        'NACESector' => '',
        'OtherSector' => NULL,
        'NoOfStaff' => '',
        'NoOfCompanySites' => '',
        'LevelOfEMS' => '',
        'EMSTime' => '',
        'Comments' => NULL,
        'RegulatorEmail' => NULL,
        'RegulatorAddress' => NULL,
        'RegulatorOrganisation' => NULL,
        'RegulatorName' => NULL,
        'SiteLocation' => NULL,
        'ProductName' => NULL,
        'Volume' => NULL,
        'Wildlife' => NULL, // "wildlife"
        'Aquifer' => NULL, // "aquifer"
        'Flooding' => NULL, // "flooding"
        'AirWater' => NULL, // "airwater"
        'submit' => 'Save'
    );

    // EMA form
    $ema_form_fields = array(
        'V3Id' => '',
        'EMA1Q1' => '',     'EMA1Q1_comment' => '',
        'EMA1Q2' => '',     'EMA1Q2_comment' => '',
        'EMA1Q2_multi' => '', // "continual,prevention,comply,activities,goals"
        'EMA1Q3' => '',     'EMA1Q3_comment' => '',
        'EMA1Q4' => '',     'EMA1Q4_comment' => '',
        'EMA1Q4_multi' => '', // "informal,working,formal,14001minus,14001,14001plus,emas,other"
        'CertDate' => '',
        'AuditDate' => '',
        'EMSStatus' => '',
        'EMSContact' => '',
        'EMA1_evidence_1' => NULL, // 1
        'EMA1_evidence_2' => NULL, // 2
        'EMA1_evidence_3' => NULL, // 3
        'EMA1_evidence_4' => NULL, // 4
        'EMA2Q1' => '',     'EMA2Q1_comment' => '',
        'EMA2Q2' => '',     'EMA2Q2_comment' => '',
        'EMA2Q3' => '',     'EMA2Q3_comment' => '',
        'EMA2Q4' => '',     'EMA2Q4_comment' => '',
        'EMA2Q5' => '',     'EMA2Q5_comment' => '',
        'EMA2Q6' => '',     'EMA2Q6_comment' => '',

        'EMA2Q7' => '',
        'EMA2Q7_application' => '',
        'EMA2Q7_permit' => '',
        'EMA2Q7a' => '',    'EMA2Q7a_comment' => '',

        'EMA2Q8' => '',     'EMA2Q8_comment' => '',
        'EMA2Q9' => '',     'EMA2Q9_comment' => '',

        'EMA2_evidence_1' => NULL, // 1
        'EMA2_evidence_2' => NULL, // 2
        'EMA2_evidence_3' => NULL, // 3
        'EMA2_evidence_4' => NULL, // 4
        'EMA2_evidence_5' => NULL, // 5
        'EMA2_evidence_6' => NULL, // 6
        'EMA2_evidence_7' => NULL, // 7
        'EMA2_evidence_8' => NULL, // 8
        'EMA2_evidence_9' => NULL, // 9
        'EMA2_evidence_10' => NULL, // 10
        'EMA2_evidence_11' => NULL, // 11
        'EMA2_evidence_12' => NULL, // 12

        'EMA3Q1' => '',     'EMA3Q1_comment' => '',
        'EMA3Q2' => '',     'EMA3Q2_comment' => '',
        'EMA3Q3' => '',     'EMA3Q3_comment' => '',
        'EMA3Q4' => '',     'EMA3Q4_comment' => '',
        'EMA3Q5' => '',     'EMA3Q5_comment' => '',
        'EMA3Q5_multi' => '', // "suggest,liason,complaints,communication,committee,opendays,regulator"
        'EMA3Q6' => '',     'EMA3Q6_comment' => '',
        'EMA3Q7' => '',     'EMA3Q7_comment' => '',
        'EMA3Q8' => '',     'EMA3Q8_comment' => '',

        'EMA3Q9' => '',     'EMA3Q9_suggest' => '',
        'EMA3Q9a' => '',    'EMA3Q9a_comment' => '',

        'EMA3Q10' => '',    'EMA3Q10_comment' => '',
        'EMA3Q11' => '',    'EMA3Q11_comment' => '',
        'EMA3Q11_multi' => '', // "permit,instruction,opcontrol,maintenance,nearmiss,loss,risk,other"

        'EMA3Q12' => '',
        'EMA3Q12_nearmiss' => '', // 0
        'EMA3Q12a' => '',   'EMA3Q12a_comment' => '',

        'EMA3Q13' => '',    'EMA3Q13_comment' => '',
        'EMA3Q14' => '',    'EMA3Q14_comment' => '',
        'EMA3Q15' => '',    'EMA3Q15_comment' => '',
        'EMA3Q16' => '',    'EMA3Q16_comment' => '',
        'EMA3Q17' => '',    'EMA3Q17_comment' => '',
        'EMA3Q18' => '',    'EMA3Q18_comment' => '',

        'EMA3_evidence_1' => NULL, // 1
        'EMA3_evidence_2' => NULL, // 2
        'EMA3_evidence_3' => NULL, // etc.
        'EMA3_evidence_4' => NULL,
        'EMA3_evidence_5' => NULL,
        'EMA3_evidence_6' => NULL,
        'EMA3_evidence_7' => NULL,
        'EMA3_evidence_8' => NULL,
        'EMA3_evidence_9' => NULL,
        'EMA3_evidence_10' => NULL,
        'EMA3_evidence_11' => NULL,
        'EMA3_evidence_12' => NULL,
        'EMA3_evidence_13' => NULL,
        'EMA3_evidence_14' => NULL,
        'EMA3_evidence_15' => NULL,
        'EMA3_evidence_16' => NULL,
        'EMA3_evidence_17' => NULL,
        'EMA3_evidence_18' => NULL,
        'EMA3_evidence_19' => NULL,
        'EMA3_evidence_20' => NULL,
        'EMA3_evidence_21' => NULL,

        'EMA4Q1' => '',     'EMA4Q1_comment' => '',
        'EMA4Q2' => '',     'EMA4Q2_comment' => '',
        'EMA4Q3' => '',     'EMA4Q3_comment' => '',
        'EMA4Q4' => '',     'EMA4Q4_comment' => '',
        'EMA4Q5' => '',     'EMA4Q5_comment' => '',
        'EMA4Q6' => '',     'EMA4Q6_comment' => '',
        'EMA4Q7' => '',     'EMA4Q7_comment' => '',
        'EMA4Q8' => '',     'EMA4Q8_comment' => '',
        'EMA4Q9' => '',     'EMA4Q9_comment' => '',
        'EMA4Q10' => '',    'EMA4Q10_comment' => '',
        'EMA4Q11' => '',    'EMA4Q11_comment' => '',

        'EMA4Q12' => '',    'EMA4Q12_comment' => '',
        'EMA4Q12_multi' => '', // "internal,external,company,certified"

        'EMA4Q13' => '',    'EMA4Q13_comment' => '',
        'EMA4Q13_internal' => '0', // 0
        'EMA4Q13_external' => '0', // 0
        'EMA4Q13_company' => '0', // 0
        'EMA4Q13_certified' => '0', // 0

        'EMA4Q14' => '',    'EMA4Q14_comment' => '',

        'EMA4_evidence_1' => NULL, // 1
        'EMA4_evidence_2' => NULL, // 2
        'EMA4_evidence_3' => NULL, // etc.
        'EMA4_evidence_4' => NULL,
        'EMA4_evidence_5' => NULL,
        'EMA4_evidence_6' => NULL,
        'EMA4_evidence_7' => NULL,
        'EMA4_evidence_8' => NULL,
        'EMA4_evidence_9' => NULL,
        'EMA4_evidence_10' => NULL,
        'EMA4_evidence_11' => NULL,
        'EMA4_evidence_12' => NULL,
        'EMA4_evidence_13' => NULL,

        'EMA5Q1' => '',     'EMA5Q1_comment' => '',
        'EMA5Q2' => '',     'EMA5Q2_comment' => '',
        'EMA5Q3' => '',     'EMA5Q3_comment' => '',
        'EMA5Q4' => '',     'EMA5Q4_comment' => '',
        'EMA5Q5' => '',     'EMA5Q5_comment' => '',
        'EMA5_evidence_1' => NULL, // 1
        'EMA5_evidence_2' => NULL, // 2
        'EMA5_evidence_3' => NULL, // etc.
        'EMA5_evidence_4' => NULL,
        'EMA5_evidence_5' => NULL,

        'EMA6Q1' => '',     'EMA6Q1_comment' => '',
        'EMA6Q2' => '',     'EMA6Q2_comment' => '',
        'EMA6Q3' => '',     'EMA6Q3_comment' => '',
        'EMA6Q4' => '',     'EMA6Q4_comment' => '',
        'EMA6Q5' => '',     'EMA6Q5_comment' => '',
        'EMA6Q6' => '',     'EMA6Q6_comment' => '',
        'EMA6_evidence_1' => NULL, // 1
        'EMA6_evidence_2' => NULL, // 2
        'EMA6_evidence_3' => NULL, // etc.
        'EMA6_evidence_4' => NULL,
        'EMA6_evidence_5' => NULL,
        'submit' => 'Save'
    );

    // EP form
    $ep_form_fields = array(
        'V3Id' => '',
        'EP1a' => '',   'EP1a_description' => '',   'EP1a_comment' => '',
        'EP1b' => '',   'EP1b_description' => '',   'EP1b_comment' => '',
        'EP1c' => '',   'EP1c_description' => '',   'EP1c_comment' => '',
        'EP1d' => '',   'EP1d_description' => '',   'EP1d_comment' => '',
        'EP1e' => '',   'EP1e_description' => '',   'EP1e_comment' => '',

        'EP2a' => '',   'EP2a_description' => '',   'EP2a_comment' => '',
        'EP2b' => '',   'EP2b_description' => '',   'EP2b_comment' => '',
        // e.g. NoEP3="1" to "3" EP3_1="0" EP3_option_1="Not applicable" EP3_rawmaterial_1="Raw material"

        'NoEP3' => '0',
        'EP3_1' => '',  'EP3_option_1' => '',   'EP3_description_1' => '',  'EP3_comment_1' => '', 'EP3_rawmaterial_1' => '',
        'EP3_2' => '',  'EP3_option_2' => '',   'EP3_description_2' => '',  'EP3_comment_2' => '', 'EP3_rawmaterial_2' => '',
        'EP3_3' => '',  'EP3_option_3' => '',   'EP3_description_3' => '',  'EP3_comment_3' => '', 'EP3_rawmaterial_3' => '',

        'NoEP4' => '0',
        'EP4_1' => '',  'EP4_option_1' => '',   'EP4_description_1' => '',  'EP4_comment_1' => '',
        'EP4_2' => '',  'EP4_option_2' => '',   'EP4_description_2' => '',  'EP4_comment_2' => '',
        'EP4_3' => '',  'EP4_option_3' => '',   'EP4_description_3' => '',  'EP4_comment_3' => '',

        'NoEP5' => '0',
        'EP5_1' => '',  'EP5_option_1' => '',   'EP5_description_1' => '',  'EP5_comment_1' => '',
        'EP5_2' => '',  'EP5_option_2' => '',   'EP5_description_2' => '',  'EP5_comment_2' => '',
        'EP5_3' => '',  'EP5_option_3' => '',   'EP5_description_3' => '',  'EP5_comment_3' => '',

        'NoEP6' => '0',
        'EP6_1' => '',  'EP6_option_1' => '',   'EP6_description_1' => '',  'EP6_comment_1' => '',
        'EP6_2' => '',  'EP6_option_2' => '',   'EP6_description_2' => '',  'EP6_comment_2' => '',
        'EP6_3' => '',  'EP6_option_3' => '',   'EP6_description_3' => '',  'EP6_comment_3' => '',

        'NoEP7' => '0',
        'EP7_1' => '',  'EP7_option_1' => '',   'EP7_description_1' => '',  'EP7_comment_1' => '',
        'EP7_2' => '',  'EP7_option_2' => '',   'EP7_description_2' => '',  'EP7_comment_2' => '',
        'EP7_3' => '',  'EP7_option_3' => '',   'EP7_description_3' => '',  'EP7_comment_3' => '',

        'NoEP8' => '0',
        'EP8_1' => '',  'EP8_option_1' => '',   'EP8_description_1' => '',  'EP8_comment_1' => '',
        'EP8_2' => '',  'EP8_option_2' => '',   'EP8_description_2' => '',  'EP8_comment_2' => '',
        'EP8_3' => '',  'EP8_option_3' => '',   'EP8_description_3' => '',  'EP8_comment_3' => '',
        'submit' => 'Save'
    );

    // Get the survey details and set global settings.
    $survey = xarModAPIfunc('surveys', 'user', 'getusersurvey', array('usid' => $usid));
    $submit_date = $survey['submit_date'];
    // Default
    if (empty($submit_date)) {
        $submit_date = time();
    }
    $submit_date = strftime('%Y-%m-%d', $submit_date);
    $reg_form_fields['CompletionDate'] = $submit_date;

    $reg_form_fields['Username'] = xarUserGetVar('uname', $survey['uid']) . '-' . $survey['usid'];
    $reg_form_fields['Password'] = md5(xarUserGetVar('uname', $survey['uid']) . '-' . $survey['uid']);
    $reg_form_fields['V3Id'] = $survey['usid'];
    $ema_form_fields['V3Id'] = $survey['usid'];
    $ep_form_fields['V3Id'] = $survey['usid'];

    // Loop for each response and do something with the data.
    foreach ($responses as $response) {
        extract($response);
        // name value1 value2 value3 status usid rtid rid qid qtid

        // Full list:
        //EMA2-6-1 START1-1 START2-1 EP-START-2 EP-START-3 EP-START-4 EP-START-6 EP-START-7 EP-START-8 EP-START-9 EP5-1-1 EMA2-1-1 EMA2-2-1 EMA1-1-1 EMA1-6-1 EMA6-7-1 EMA1-3-1 EP7-1-1 EMA1-2-1 EMA3-8-1 EMA6-1-1 EMA6-2-1 EMA6-3-1 EMA6-4-1 EMA6-5-1 EMA6-6-1 EMA5-1-1 EMA5-2-1 EMA5-3-1 EMA5-4-1 EMA5-5-1 EMA5-6-1 EMA1-4-1 EMA1-5-1 EMA1-5-2 EMA1-5-3 EMA1-5-4 EMA2-7-1 SITE0-1 SITE0-2 SITE0-3 SITE0-4 SITE0-5 SITE0-6 SITE1-0a SITE1-1 SITE1-2 SITE1-3 SITE1-4 SITE1-5 SITE1-6 SITE1-7 SITE1-8 SITE1-9 SITE2-2 SITE2-3 SITE2-4 SITE2-5 SITE2-7

        // Skip invalid responses.
        if ($status <> 'COMPLETE') {
            continue;
        }
        
        switch ($name) {
            /* Registration Form */
            case 'SITE0-1':
                $reg_form_fields['Salutation'] = $value1; break;
            case 'SITE0-2':
                $reg_form_fields['FirstName'] = $value1; break;
            case 'SITE0-3':
                $reg_form_fields['LastName'] = $value1;  break;
            case 'SITE0-4':
                $reg_form_fields['EmailAddress'] = $value1; break;
            case 'SITE0-5':
                $reg_form_fields['JobTitle'] = $value1; break;
            case 'SITE1-0a':
                // Not used.
                break;
            case 'SITE1-1':
                $reg_form_fields['CompanyName'] = $value1; break;
            case 'SITE1-2':
                $reg_form_fields['CompanyStreet'] = $value1; break;
            case 'SITE1-3':
                $reg_form_fields['CompanyStreet1'] = $value1; break;
            case 'SITE1-4':
                $reg_form_fields['CompanyTownCity'] = $value1; break;
            case 'SITE1-5':
                $reg_form_fields['CompanyCountry'] = $value1; break;
            case 'SITE1-6':
                $reg_form_fields['CompanyPostcode'] = $value1; break;
            case 'SITE1-7':
                $reg_form_fields['CompanyTelephoneNo'] = $value1; break;
            case 'SITE1-9':
                $reg_form_fields['CompanyWebsite'] = $value1; break;
            case 'SITE1-8':
                $reg_form_fields['CompanyFaxNo'] = $value1; break;
            case 'SITE2-2':
                $reg_form_fields['NoOfStaff'] = $value1; break;
            case 'SITE2-3':
                $reg_form_fields['NoOfCompanySites'] = $value1; break;
            case 'SITE2-4':
                $reg_form_fields['LevelOfEMS'] = $value1; break;
            case 'SITE2-5':
                $reg_form_fields['EMSTime'] = $value1; break;
            case 'SITE2-7':
                $reg_form_fields['Comments'] = $value3; break;
            case 'EP-START-4':
                if (empty($value1)) {
                    $reg_form_fields['Sector'] = 0;
                    $reg_form_fields['NACESector'] = 0;
                } else {
                    // 15.12.2004: look up the sector name.
                    $query = 'SELECT name FROM remas_ep_subsectors WHERE nace = ?';
                    $qresult = $dbconn->execute($query, array($value1));
                    if ($qresult && !$qresult->EOF) {
                        list($sector_name) = $qresult->fields;
                        $reg_form_fields['Sector'] = $sector_name;
                        $reg_form_fields['NACESector'] = $value1 . ' ' . $sector_name;
                    } else {
                        $reg_form_fields['Sector'] = 'Unknown';
                        $reg_form_fields['NACESector'] = 'Unknown';
                    }
                }
                $reg_form_fields['NACECode'] = $value1;
                $reg_form_fields['OtherSector'] = $value3;
                break;
            case 'EP-START-1c':
                $reg_form_fields['RegulatorOrganisation'] = $value1; break;
            case 'EP-START-1d':
                $reg_form_fields['RegulatorName'] = $value1; break;
            case 'EP-START-2':
                $reg_form_fields['RegulatorEmail'] = $value1; break;
            case 'EP-START-3':
                $reg_form_fields['RegulatorAddress'] = $value3; break;
            case 'EP-START-6':
                $reg_form_fields['SiteLocation'] = $value1; break;
            case 'EP-START-8':
                $reg_form_fields['ProductName'] = $value1; break;
            case 'EP-START-9':
                $reg_form_fields['Volume'] = $value1; break;
            case 'EP-START-7':
                $arr = explode(',', $value1);
                foreach($arr as $value) {
                    // A functional mapping is not so easy due to the mixed case,
                    // so a switch is used.
                    switch ($value) {
                        case 'wildlife':
                            $reg_form_fields['Wildlife'] = $value;
                            break;
                        case 'aquifer':
			// JDJ Added 2005.05.23: Mixed case match required.
                        case 'Aquifer':
                            $reg_form_fields['Aquifer'] = $value;
                            break;
                        case 'flooding':
                            $reg_form_fields['Flooding'] = $value;
                            break;
                        case 'airwater':
                            $reg_form_fields['AirWater'] = $value;
                            break;
                        default:
                            // Should be an error, this.
                            break;
                    }
                }
                break;

            /* EMA Form */

            case 'EMA1-1-1':
            case 'EMA1-3-1':
            case 'EMA2-1-1':
            case 'EMA2-2-1':
            case 'EMA2-3-1':
            case 'EMA2-4-1':
            case 'EMA2-5-1':
            case 'EMA2-6-1':
            case 'EMA2-7a-1':
            case 'EMA2-8-1':
            case 'EMA2-9-1':
            case 'EMA3-1-1':
            case 'EMA3-2-1':
            case 'EMA3-3-1':
            case 'EMA3-4-1':
            case 'EMA3-5-1':
            case 'EMA3-6-1':
            case 'EMA3-7-1':
            case 'EMA3-8-1':
            case 'EMA3-9a-1':
            case 'EMA3-10-1':
            case 'EMA3-12a-1':
            case 'EMA3-13-1':
            case 'EMA3-14-1':
            case 'EMA3-15-1':
            case 'EMA3-16-1':
            case 'EMA3-17-1':
            case 'EMA3-18-1':
            case 'EMA4-1-1':
            case 'EMA4-2-1':
            case 'EMA4-3-1':
            case 'EMA4-4-1':
            case 'EMA4-5-1':
            case 'EMA4-6-1':
            case 'EMA4-7-1':
            case 'EMA4-8-1':
            case 'EMA4-9-1':
            case 'EMA4-10-1':
            case 'EMA4-11-1':
            case 'EMA4-14-1':
            case 'EMA5-1-1':
            case 'EMA5-2-1':
            case 'EMA5-3-1':
            case 'EMA5-4-1':
            case 'EMA5-5-1':
            case 'EMA6-1-1':
            case 'EMA6-2-1':
            case 'EMA6-3-1':
            case 'EMA6-4-1':
            case 'EMA6-5-1':
            case 'EMA6-6-1':
                $arr = preg_split('/EMA|-/', $name);
                $ems_sect = $arr[1];
                $ema_ques = $arr[2];
                $ema_form_fields['EMA'.$ems_sect.'Q'.$ema_ques] = $value1;
                $ema_form_fields['EMA'.$ems_sect.'Q'.$ema_ques.'_comment'] = $value3;
                break;

            case 'EMA1-2-1':
                if ($value1 == 'none' || $value1 == 'unable' || $value1 == 'notwish') {
                    $ema_form_fields['EMA1Q2'] = $value1;
                }
                $ema_form_fields['EMA1Q2_multi'] = $value1;
                $ema_form_fields['EMA1Q2_comment'] = $value3;
                break;

            case 'EMA1-4-1':
                $ema_form_fields['EMA1Q4_multi'] = $value1;
                $ema_form_fields['EMA1Q4_comment'] = $value3;
                break;

            case 'EMA1-5-1':
                $ema_form_fields['CertDate'] = $value1;
                break;

            case 'EMA1-5-2':
                $ema_form_fields['AuditDate'] = $value1;
                break;

            case 'EMA1-5-3':
                $ema_form_fields['EMSStatus'] = $value1;
                break;

            case 'EMA1-5a-1':
            case 'EMA1-5a-2':
            case 'EMA1-5a-3':
            case 'EMA1-5a-5':
                $ema_form_fields['EMSContact'] .=  (empty($ema_form_fields['EMSContact']) ? '' : "\n") . $value1;
                break;

            case 'EMA1-5-4':
                $ema_form_fields['EMSContact'] .=  (empty($ema_form_fields['EMSContact']) ? '' : "\n") . $value3;
                break;

            case 'EMA1-6-1':
                $arr = explode(',', $value1);
                foreach($arr as $value) {
                    $ema_form_fields['EMA1_evidence_'.$value] = $value;
                }
                break;

            case 'EMA2-7-1':
                // Y/N answer
                $ema_form_fields['EMA2Q7'] = $value1;
                break;

            case 'EMA2-7-2':
                $ema_form_fields['EMA2Q7_application'] = $value1;
                break;

            case 'EMA2-7-3':
                $ema_form_fields['EMA2Q7_permit'] = $value1;
                break;

            case 'EMA2-7a-1':
                $ema_form_fields['EMA2Q7a'] = $value1;
                $ema_form_fields['EMA2Q7a_comment'] = $value3;
                break;

            case 'EMA2-10-1':
                $arr = explode(',', $value1);
                foreach($arr as $value) {
                    $ema_form_fields['EMA2_evidence_'.$value] = $value;
                }
                break;

            case 'EMA3-9-1':
                $ema_form_fields['EMA3Q9'] = $value1;
                //$ema_form_fields['EMA3Q9_suggest'] = $value3; // ???
                break;

            case 'EMA3-11-1':
                $ema_form_fields['EMA3Q11_multi'] = $value1;
                $ema_form_fields['EMA3Q11_comment'] = $value3;
                break;

            case 'EMA3-12-1':
                $ema_form_fields['EMA3Q12'] = $value1; // ???
                $ema_form_fields['EMA3Q12_nearmiss'] = $value1;
                break;

            case 'EMA3-10-1':
                $arr = explode(',', $value1);
                foreach($arr as $value) {
                    $ema_form_fields['EMA3_evidence_'.$value] = $value;
                }
                break;

            case 'EMA4-12-1':
                if ($value1 == 'unable' || $value1 == 'notwish') {
                    $ema_form_fields['EMA4Q12'] = $value1;
                }
                $ema_form_fields['EMA4Q12_multi'] = $value1;
                $ema_form_fields['EMA4Q12_comment'] = $value3;
                break;

            case 'EMA4-13-1':
                $ema_form_fields['EMA4Q13_internal'] = $value1;
                break;

            case 'EMA4-13-2':
                $ema_form_fields['EMA4Q13_external'] = $value1;
                break;

            case 'EMA4-13-3':
                $ema_form_fields['EMA4Q13_company'] = $value1;
                break;

            case 'EMA4-13-4':
                $ema_form_fields['EMA4Q13_certified'] = $value1;
                break;

            case 'EMA4-13-5':
                $ema_form_fields['EMA4Q13'] = $value1;
                $ema_form_fields['EMA4Q13_comment'] = $value3;
                break;

            case 'EMA4-15-1':
                $arr = explode(',', $value1);
                foreach($arr as $value) {
                    $ema_form_fields['EMA4_evidence_'.$value] = $value;
                }
                break;

            case 'EMA5-6-1':
                $arr = explode(',', $value1);
                foreach($arr as $value) {
                    $ema_form_fields['EMA5_evidence_'.$value] = $value;
                }
                break;

            case 'EMA6-7-1':
                $arr = explode(',', $value1);
                foreach($arr as $value) {
                    $ema_form_fields['EMA6_evidence_'.$value] = $value;
                }
                break;

            /* EP Form */

            case 'EP1a':
            case 'EP1b':
            case 'EP1c':
            case 'EP1d':
            case 'EP1e':
            case 'EP2a':
            case 'EP2b':
                $ep_form_fields[$name] = $value1;
                break;

            case 'EP3-1-1':
            case 'EP3-2-1':
            case 'EP3-3-1':
            case 'EP4-1-1':
            case 'EP4-2-1':
            case 'EP4-3-1':
            case 'EP5-1-1':
            case 'EP5-2-1':
            case 'EP5-3-1':
            case 'EP6-1-1':
            case 'EP6-2-1':
            case 'EP6-3-1':
            case 'EP7-1-1':
            case 'EP7-2-1':
            case 'EP7-3-1':
            case 'EP8-1-1':
            case 'EP8-2-1':
            case 'EP8-3-1':
                // value1 = indicator value
                // value2 = indicator ID
                // value3 = comment or raw material
                if (isset($value1) && $value1 != '' && !empty($value2)) {
                    $arr = preg_split('/EP|-/', $name);
                    $ep_sect = $arr[1];
                    $ep_no = $arr[2];
                    $ep_form_fields['NoEP'.$ep_sect] += 1;
                    $ep_form_fields['EP'.$ep_sect.'_option_'.$ep_no] = $value2;
                    $ep_form_fields['EP'.$ep_sect.'_'.$ep_no] = $value1;
                    $ep_form_fields['EP'.$ep_sect.'_comment_'.$ep_no] = $value3;
                    if ($ep_sect == 3 ) {
                        // JJ 17.12.2004: set raw material to comments truncated
                        // Redo of changes lost from 29.11.2004
                        $ep_form_fields['EP'.$ep_sect.'_rawmaterial_'.$ep_no] = substr($value3, 0, 255);
                    }
                    // Fetch the description.
                    $query = 'SELECT name FROM remas_ep_indicators WHERE id = ?';
                    $qresult = $dbconn->execute($query, array($value2));
                    if ($qresult && !$qresult->EOF) {
                        list($desc) = $qresult->fields;
                        // JJ 17.12.2004: blank the description
                        // JJ 20.12.2004: restor the description
                        // Redo of changes lost from 11.11.2004 ???
                        $ep_form_fields['EP'.$ep_sect.'_description_'.$ep_no] = ''; //$desc;
                    }
                }
                break;

            default:
                // No error on default (but perhaps log it).
                break;
        }
    }

    // Load the curl API.
    $curl = xarModAPIfunc('base', 'user', 'newcurl');

    //
    // URL for the reg form.
    //
    if (!empty($debug)) {
        echo "<h2>Reg Form</h2>";
    }
    if ($result === true) {
        $result = surveys_adminapi_transfersurvey_xfer($url_base . '/v3_reg.asp', $reg_form_fields, $debug);
    }

    //
    // URL for the ema form.
    //
    if (!empty($debug)) {
        echo "<h2>EMA Form</h2>";
    }
    if ($result === true) {
        $result = surveys_adminapi_transfersurvey_xfer($url_base . '/v3_em.asp', $ema_form_fields, $debug);
    }

    //
    // URL for the ema form.
    //
    if (!empty($debug)) {
        echo "<h2>EP Form</h2>";
    }
    if ($result === true) {
        $result = surveys_adminapi_transfersurvey_xfer($url_base . '/v3_ep.asp', $ep_form_fields, $debug);
    }

    return $result;
}

function surveys_adminapi_transfersurvey_xfer($url, &$data, $debug = false) {
    $f_result = true;

    // Load the curl API.
    $curl = xarModAPIfunc('base', 'user', 'newcurl');

    $curl->seturl($url);

    // Prepare to transfer the form data.
    $curl->post($data);
    if (!empty($debug)) {
        echo "<pre>POST DATA:\n";
        //var_dump($data);
        foreach($data as $name => $value) {
            if (isset($value)) {
                echo "$name: '" . xarVarPrepForDisplay($value) . "'<br/>";
            }
        }
        echo "</pre>";
    }

    // Post the reg form data.
    $raw_result = $curl->exec();
    // Close the session.
    $curl->close();

    if ($curl->http_code == 200 && !empty($raw_result)) {
        // Analyse the post result.
        $result = xarModAPIfunc(
            'surveys', 'user', 'remas_submit_result',
            array('data' => $raw_result)
        );
        extract($result);

        if (!empty($debug)) {
            echo "Result=$status<br/>";
            echo "Message=$message<br/>";
            if (!empty($detail)) {
                echo "Detail:-<br/>";
                foreach($detail as $line) {
                    echo "* $line<br/>";
                }
            }
        }
    } else {
        if (!empty($debug)) {
            echo "Error: HTTP response " . $curl->http_code . "<br/>";
            echo "Detail: " . $curl->error . "<br/>";
        }

        $f_result = $curl->error;
    }

    if (!empty($debug)) {
        echo '<textarea rows="10" style="width: 100%;">';
        echo xarVarPrepForDisplay($raw_result);
        echo '</textarea>';
    }

    return $f_result;
}

?>
