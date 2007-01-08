<?php
/**
 * Surveys initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys module
 * @link http://xaraya.com/index.php/release/45.html
 * @author Jason Judge
 */
/**
 * Initialise the surveys module.
 * @author Surveys module development team
 * @author MichelV <michelv@xarayahosting.nl>
 * @param none
 * @return bool true on success
 */
function surveys_init()
{
    /*
     * Create tables
     */
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $group_rulestable = $xartable['surveys_group_rules'];

    // Get a data dictionary object with item create methods.
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
/*
 CREATE TABLE `xar_surveys_group_rules` (
  `xar_rid` int(11) NOT NULL auto_increment,
  `xar_survey_id` int(11) NOT NULL default '0',
  `xar_group_id` int(11) NOT NULL default '0',
  `xar_logic` varchar(10) NOT NULL default 'OR',
  `xar_condition` varchar(200) default NULL,
  PRIMARY KEY  (`xar_rid`),
  KEY `xar_survey_id` (`xar_survey_id`,`xar_group_id`),
  KEY `xar_group_id` (`xar_group_id`)
) TYPE=MyISAM AUTO_INCREMENT=62 ;
*/

    $fields = " xar_rid         I       AUTO    PRIMARY KEY,
                xar_survey_id   I       UNSIGNED NOTNULL default 0,
                xar_group_id    I       NOTNULL default 0,
                xar_logic       C(10)   NOTNULL default OR,
                xar_condition   C(200)  default NULL
                ";

    // Create or alter the table as necessary.
    $result = $datadict->changeTable($group_rulestable, $fields);
    if (!$result) {return;}


// function CreateIndexSQL($idxname, $tabname, $flds, $idxoptions = false)

    // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_surveys_survey_id',
        $group_rulestable,
        array('xar_survey_id','xar_group_id')
    );
    if (!$result) {return;}

     // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_surveys_group_id',
        $group_rulestable,
        'xar_group_id'
    );
    if (!$result) {return;}


    $groupstable = $xartable['surveys_groups'];
    /*
     * This table describes the groups that will contain the questions
     */

/*
CREATE TABLE `xar_surveys_groups` (
  `xar_gid` int(11) NOT NULL auto_increment,
  `xar_parent` int(11) default NULL,
  `xar_left` int(11) default NULL,
  `xar_right` int(11) default NULL,
  `xar_name` varchar(100) default NULL,
  `xar_desc` text,
  PRIMARY KEY  (`xar_gid`),
  KEY `xar_left` (`xar_left`),
  KEY `xar_right` (`xar_right`)
) TYPE=MyISAM AUTO_INCREMENT=176 ;
*/

    $fields = " xar_gid     I NOTNULL auto KEY,
                xar_parent  I default NULL,
                xar_left    I default NULL,
                xar_right   I default NULL,
                xar_name    C(100) default NULL,
                xar_desc    text";
    // Create or alter the table as necessary.
    $result = $datadict->changeTable($groupstable, $fields);
    if (!$result) {return;}

/*
  KEY `xar_left` (`xar_left`),
  KEY `xar_right` (`xar_right`)
  */
     // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_surveys_xar_left',
        $groupstable,
        'xar_left'
    );
    if (!$result) {return;}
     // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_surveys_xar_right',
        $groupstable,
        'xar_right'
    );
    if (!$result) {return;}


    $question_groupstable = $xartable['surveys_question_groups'];
    /*
     * This table couples the questions to groups.
     * Groups are described in $groupstable
     */
/*
CREATE TABLE `xar_surveys_question_groups` (
  `xar_qgid` int(11) NOT NULL auto_increment,
  `xar_question_id` int(11) NOT NULL default '0',
  `xar_group_id` int(11) NOT NULL default '0',
  `xar_order` varchar(20) default NULL,
  `xar_template` varchar(30) default NULL,
  `xar_readonly` char(1) NOT NULL default 'N',
  PRIMARY KEY  (`xar_qgid`),
  KEY `xar_group_id` (`xar_qgid`,`xar_question_id`)
) TYPE=MyISAM AUTO_INCREMENT=190 ;
*/

    $fields = "
              xar_qgid          I auto primary,
              xar_question_id   I NOTNULL default 0,
              xar_group_id      I NOTNULL default 0,
              xar_order         C(20) default NULL,
              xar_template      C(30) default NULL,
              xar_readonly      C(1) NOTNULL default N
              ";
    // Create or alter the table as necessary.
    $result = $datadict->changeTable($question_groupstable, $fields);
    if (!$result) {return;}

    // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_surveys_groups_id',
        $question_groupstable,
        array('xar_qgid','xar_question_id')
    );
    if (!$result) {return;}


    $questionstable = $xartable['surveys_questions'];
/*
CREATE TABLE `xar_surveys_questions` (
  `xar_qid` int(11) NOT NULL auto_increment,
  `xar_type_id` int(11) NOT NULL default '0',
  `xar_name` varchar(100) default NULL,
  `xar_desc` text,
  `xar_mandatory` char(1) NOT NULL default 'N',
  `xar_default` varchar(200) default NULL,
  PRIMARY KEY  (`xar_qid`),
  KEY `xar_type_id` (`xar_type_id`)
) TYPE=MyISAM AUTO_INCREMENT=162 ;
*/
// MichelV: Don't like the C(1) here for a bool

    $fields = " xar_qid         I AUTO PRIMARY,
                xar_type_id     I NOTNULL default 0,
                xar_name        C(100) default NULL,
                xar_desc        text,
                xar_mandatory   C(1) NOTNULL default N,
                xar_default     C(200) default NULL
            ";
    // Create or alter the table as necessary.
    $result = $datadict->changeTable($questionstable, $fields);
    if (!$result) {return;}

     // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_surveys_xar_type_id',
        $questionstable,
        'xar_type_id'
    );


    $statustable = $xartable['surveys_status'];
/*
CREATE TABLE `xar_surveys_status` (
  `xar_ssid` int(11) NOT NULL auto_increment,
  `xar_type` varchar(30) NOT NULL default '',
  `xar_status` varchar(30) NOT NULL default '',
  `xar_system_status` varchar(30) default NULL,
  `xar_short_name` varchar(100) default NULL,
  `xar_desc` text,
  PRIMARY KEY  (`xar_ssid`),
  KEY `xar_type` (`xar_type`)
) TYPE=MyISAM AUTO_INCREMENT=16 ;
*/
    $fields = "
              xar_ssid I NOTNULL auto PRIMARY,
              xar_type C(30) NOTNULL default '',
              xar_status C(30) NOTNULL default '',
              xar_system_status C(30) default NULL,
              xar_short_name C(100) default NULL,
              xar_desc text
              ";

    // Create or alter the table as necessary.
    $result = $datadict->changeTable($statustable, $fields);
    if (!$result) {return;}

     // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_surveys_xar_type',
        $statustable,
        'xar_type'
    );


    $surveystable = $xartable['surveys_surveys'];
/*
CREATE TABLE `xar_surveys_surveys` (
  `xar_sid` int(11) NOT NULL auto_increment,
  `xar_name` varchar(100) NOT NULL default '',
  `xar_desc` text,
  `xar_group_id` int(11) default NULL,
  `xar_summary_template` text,
  `xar_max_instances` int(11) NOT NULL default '1',
  `xar_max_in_progress` int(11) NOT NULL default '1',
  `xar_anonymous` char(1) NOT NULL default 'Y',
  PRIMARY KEY  (`xar_sid`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;
*/
    $fields = "
              xar_sid               I NOTNULL auto PRIMARY,
              xar_name              C(100) NOTNULL default '',
              xar_desc              text,
              xar_group_id          I default NULL,
              xar_summary_template  text,
              xar_max_instances     I NOTNULL default 1,
              xar_max_in_progress   I NOTNULL default 1,
              xar_anonymous         C(1) NOTNULL default Y
              ";
    // Create or alter the table as necessary.
    $result = $datadict->changeTable($surveystable, $fields);
    if (!$result) {return;}

    $surveys_typestable = $xartable['surveys_types'];
/*
CREATE TABLE `xar_surveys_types` (
  `xar_tid` int(11) NOT NULL auto_increment,
  `xar_type` char(1) NOT NULL default 'S',
  `xar_name` varchar(100) default NULL,
  `xar_response_type_id` int(11) default NULL,
  `xar_object_name` varchar(100) default NULL,
  PRIMARY KEY  (`xar_tid`)
) TYPE=MyISAM AUTO_INCREMENT=26 ;
*/
    $fields = "
              xar_tid I NOTNULL auto PRIMARY,
              xar_type C(1) NOTNULL default S,
              xar_name C(100) default NULL,
              xar_response_type_id I default NULL,
              xar_object_name C(100) default NULL
              ";
    // Create or alter the table as necessary.
    $result = $datadict->changeTable($surveys_typestable, $fields);
    if (!$result) {return;}

    $user_groupstable = $xartable['surveys_user_groups'];

/*
CREATE TABLE `xar_surveys_user_groups` (
  `xar_ugid` int(11) NOT NULL auto_increment,
  `xar_user_survey_id` int(11) NOT NULL default '0',
  `xar_group_id` int(11) NOT NULL default '0',
  `xar_status` varchar(20) NOT NULL default 'NORESPONSE',
  PRIMARY KEY  (`xar_ugid`),
  UNIQUE KEY `xar_user_survey_id` (`xar_user_survey_id`,`xar_group_id`)
) TYPE=MyISAM AUTO_INCREMENT=69448 ;
*/

    $fields = "
              xar_ugid I NOT NULL auto PRIMARY,
              xar_user_survey_id I NOTNULL default 0,
              xar_group_id I NOTNULL default 0,
              xar_status C(20) NOTNULL default NORESPONSE
              ";
    // Create or alter the table as necessary.
    $result = $datadict->changeTable($user_groupstable, $fields);
    if (!$result) {return;}

    // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_xar_user_survey_id',
        $user_groupstable,
        array('xar_user_survey_id','xar_group_id'),
        'unique' // This doesn't work properly
    );
    if (!$result) {return;}



    $user_responsestable = $xartable['surveys_user_responses'];
    /*
     * This table holds all user responses
     * It gets filled with dummy answers when a group of questions is loaded by a user
     */

/*
CREATE TABLE `xar_surveys_user_responses` (
  `xar_rid` int(11) NOT NULL auto_increment,
  `xar_user_survey_id` int(11) NOT NULL default '0',
  `xar_question_id` int(11) NOT NULL default '0',
  `xar_status` varchar(20) NOT NULL default 'NA',
  `xar_value1` varchar(200) default NULL,
  `xar_value2` varchar(200) default NULL,
  `xar_value3` text,
  PRIMARY KEY  (`xar_rid`),
  KEY `xar_user_survey_id` (`xar_user_survey_id`),
  KEY `xar_question_id` (`xar_question_id`),
  KEY `xar_user_survey_id_2` (`xar_user_survey_id`,`xar_question_id`)
) TYPE=MyISAM AUTO_INCREMENT=60562 ;
*/
    $fields = "
              xar_rid I auto PRIMARY,
              xar_user_survey_id I NOTNULL default 0,
              xar_question_id I NOTNULL default 0,
              xar_status C(20) NOTNULL default NA,
              xar_value1 C(200) default NULL,
              xar_value2 C(200) default NULL,
              xar_value3 text
              ";
    // Create or alter the table as necessary.
    $result = $datadict->changeTable($user_responsestable, $fields);
    if (!$result) {return;}

     // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_surveys_xar_user_survey_id',
        $user_responsestable,
        'xar_user_survey_id'
    );
     // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_surveys_xar_question_id',
        $user_responsestable,
        'xar_question_id'
    );
     // Create indexes.
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_surveys_xar_question_id_2',
        $user_responsestable,
        array('xar_user_survey_id','xar_question_id')
    );

    $user_surveystable = $xartable['surveys_user_surveys'];

/*
CREATE TABLE `xar_surveys_user_surveys` (
  `xar_usid` int(11) NOT NULL auto_increment,
  `xar_user_id` varchar(60) NOT NULL default '0',
  `xar_survey_id` int(11) NOT NULL default '0',
  `xar_status` varchar(20) NOT NULL default 'PROGRESS',
  `xar_start_date` int(10) NOT NULL default '0',
  `xar_submit_date` int(10) NOT NULL default '0',
  `xar_closed_date` int(10) NOT NULL default '0',
  `xar_last_updated` int(10) NOT NULL default '0',
  PRIMARY KEY  (`xar_usid`),
  KEY `xar_user_id` (`xar_survey_id`,`xar_usid`)
) TYPE=MyISAM AUTO_INCREMENT=675 ;

*/
    $fields = "
              xar_usid          I       auto    PRIMARY,
              xar_user_id       C(60)   NOTNULL default 0,
              xar_survey_id     I       NOTNULL default 0,
              xar_status        C(20)   NOTNULL default PROGRESS,
              xar_start_date    I       NOTNULL default 0,
              xar_submit_date   I       NOTNULL default 0,
              xar_closed_date   I       NOTNULL default 0,
              xar_last_updated  I       NOTNULL default 0
              ";
    // Create or alter the table as necessary.
    $result = $datadict->changeTable($user_surveystable, $fields);
    if (!$result) {return;}
    // Create indexes.
    // Bug 5222. Double entries in index table Lets turn off this index for a moment
    /*
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_surveys_xar_user_id',
        $user_surveystable,
        array('xar_survey_id','xar_usid')
    );
    */
    /*
     * Variables
     * and Others
     */

    // Create a module variable for storing the current
    // user survey flags against.
    $name = 'surveys.current_survey';
    xarModSetVar('surveys', $name, serialize(array()));

    xarModSetVar('surveys', 'SendEventMails', 1);
    xarModSetVar('surveys', 'SupportShortURLs', 0);
    /* Alias
     * TODO: everything else on short urls and aliases
     */
    xarModSetVar('surveys', 'useModuleAlias',false);
    xarModSetVar('surveys','aliasname','');

    /*
     * Instances
     */

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

    return true;
}
/**
 * upgrade the surveys module from an old version
 * This function can be called multiple times
 */
function surveys_upgrade($oldversion)
{
    /* Upgrade dependent on old version number */
    switch ($oldversion) {
        case '0.1.0':
            return surveys_upgrade('0.1.1');
        case '0.1.1':
            break;
    }
    /* Update successful */
    return true;
}
/**
 * delete the surveys module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function surveys_delete()
{
    /* Get database setup */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    // Initialise table array
    $basename = 'surveys';

    foreach(array('groups', 'question_groups', 'questions', 'surveys', 'types', 'status', 'user_groups', 'user_responses', 'user_surveys', 'group_rules') as $table) {

    /* Drop the tables */
     $result = $datadict->dropTable($xartable[$basename . '_' . $table]);
    }

    /* Remove any module aliases before deleting module vars */
    /* Assumes one module alias in this case */
    $aliasname =xarModGetVar('surveys','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='surveys')){
        xarModDelAlias($aliasname,'surveys');
    }

    /* Delete any module variables
     */

    xarModDelAllVars('surveys');


    /* Unregister each of the hooks that have been created
     * Will create the user GUI hook later
     *
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'surveys', 'user', 'usermenu')) {
        return false;
    }
    */
    /* Remove Masks and Instances
     * these functions remove all the registered masks and instances of a module
     * from the database. This is not strictly necessary, but it's good housekeeping.
     */
    xarRemoveMasks('surveys');
    xarRemoveInstances('surveys');

    /* Deletion successful*/
    return true;
}


?>