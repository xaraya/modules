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
function surveys_user_test() {

// Components:
// Survey Assessment

return 'x';

// Set up database tables
$dbconn =& xarDBGetConn();
$xartable =& xarDBGetTables();

// defineInstance($module,$type (AKA component),$instances,$propagate=0,$table2='',$childID='',$parentID='',$description='')
$instances = array (
    array (
        'header' => 'Survey ID',
        'query' => 'SELECT xar_sid FROM ' . $xartable['surveys_surveys'],
        'limit' => 20
    )
);

xarDefineInstance(
    'surveys', 'Survey', $instances, 0, 'All', 'All', 'All',
    xarML('Select for a specific survey')
);

$instances = array (
    array (
        'header' => 'external', //'Survey ID',
        'query' => xarModURL('surveys', 'admin', 'privileges'), //'SELECT xar_sid FROM ' . $xartable['surveys_surveys'],
        'limit' => 20
    ),
    array (
        'header' => 'System status',
        'query' => 'SELECT DISTINCT xar_system_status FROM ' . $xartable['surveys_status'] . ' WHERE xar_type = \'SURVEY\'',
        'limit' => 20
    ),
    array (
        'header' => 'Status',
        'query' => 'SELECT xar_status FROM ' . $xartable['surveys_status'] . ' WHERE xar_type = \'SURVEY\'',
        'limit' => 20
    ),
    array (
        'header' => 'User ID',
        'query' => 'SELECT DISTINCT xar_uid FROM ' . $xartable['roles'] . ' WHERE xar_type = 0',
        'limit' => 20
    )
);

xarDefineInstance(
    'surveys', 'Assessment', $instances, 0, 'All:All:All:All' /*'All'*/, 'All', 'All',
    xarML('Select for a specific user survey')
);


// register($name,$realm,$module,$component,$instance,$level,$description='')
xarRegisterMask('OverviewSurvey', 'All', 'surveys', 'Survey', 'All', 'ACCESS_OVERVIEW', 'View description of a survey');
xarRegisterMask('ReadSurvey', 'All', 'surveys', 'Survey', 'All', 'ACCESS_READ', 'View questions to a survey');
xarRegisterMask('CommentSurvey', 'All', 'surveys', 'Survey', 'All', 'ACCESS_COMMENT', 'Complete answers to a survey; carry out a survey');
xarRegisterMask('EditSurvey', 'All', 'surveys', 'Survey', 'All', 'ACCESS_EDIT', 'Change the structure of a survey');
xarRegisterMask('AddSurvey', 'All', 'surveys', 'Survey', 'All', 'ACCESS_ADD', 'Add a new survey');
xarRegisterMask('DeleteSurvey', 'All', 'surveys', 'Survey', 'All', 'ACCESS_DELETE', 'Remove a survey');
xarRegisterMask('AdminSurvey', 'All', 'surveys', 'Survey', 'All', 'ACCESS_ADMIN', 'Do anything to a survey');

xarRegisterMask('OverviewAssessment', 'All', 'surveys', 'Assessment', 'All', 'ACCESS_OVERVIEW', 'View the summary to a user survey');
xarRegisterMask('ReadAssessment', 'All', 'surveys', 'Assessment', 'All', 'ACCESS_READ', 'View responses to a user survey');
xarRegisterMask('CommentAssessment', 'All', 'surveys', 'Assessment', 'All', 'ACCESS_COMMENT', 'Change responses to a user survey');
xarRegisterMask('ModerateAssessment', 'All', 'surveys', 'Assessment', 'All', 'ACCESS_MODERATE', 'Change the status of a user survey');


return 'xxx';

                $system_statuses = xarModAPIfunc(
                    'surveys', 'user', 'lookupstatus',
                    array('type' => 'SURVEY', 'status' => 'INSPECTOR', 'return' => 'system_status')
                );

var_dump($system_statuses);

$var = '00:00:00';
//echo to_number($var);
echo (int)$var;

return 'x';
}

?>