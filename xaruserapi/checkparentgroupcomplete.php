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
 * Bit of a mouthful, but here is what it does:
 * Given a group ID, check the status of that group's parent,
 * and all its descendants. If all are either COMPLETE or NA,
 * then indicate a success.
 * When applied to a group that has just had its status changed
 * to COMPLETE, this function will indicate whether it is the last
 * group in its parent's group to be completed successfuly.
 *
 * There still some outstanding issues that can be solved in other
 * places. For example, it may trigger premeturely because groups
 * do not always get populated in the database (i.e. a group that
 * has not been responded to, may have no record at all in the
 * user_groups table; a group that was previously hidden, and is
 * now shown, however, will have an entry in the user_groups table
 * because showing a previously-hidden group involved creating
 * a 'NORESPONSE' database entry for that group).
 *
 * Params: $usid and $gid, or $ugid
 */

function surveys_userapi_checkparentgroupcomplete($args) {
    extract($args);

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $query = 'SELECT parent_group.xar_name, parent_group.xar_desc,'
        . ' parent_group.xar_left, parent_group.xar_right,'
        . ' seed_user_group.xar_user_survey_id, parent_group.xar_gid'
        . ' FROM ' . $xartable['surveys_user_groups'] . ' AS seed_user_group'

        // Start group (not the parent)
        . ' INNER JOIN ' . $xartable['surveys_groups'] . ' AS seed_group'
        . ' ON seed_group.xar_gid = seed_user_group.xar_group_id'

        // Parent group - we are looking at descendants of this group.
        . ' INNER JOIN ' . $xartable['surveys_groups'] . ' AS parent_group'
        . ' ON parent_group.xar_gid = seed_group.xar_parent'

        . ' WHERE seed_group.xar_parent <> 0'
        ;

    $bind = array();

    if (isset($ugid)) {
        $query .= ' AND seed_user_group.xar_ugid = ?';
        $bind[] = (int)$ugid;
    } else {
        if (!isset($usid) || !isset($gid)) {return false;}

        $query .= ' AND seed_user_group.xar_user_survey_id = ?'
            . ' AND seed_user_group.xar_group_id = ?';
        $bind[] = (int)$usid;
        $bind[] = (int)$gid;
    }

    $result = $dbconn->execute($query, $bind);
    if (!$result || $result->EOF) {return false;}
    list($name, $desc, $left, $right, $usid, $pgid) = $result->fields;

    // Fetch the parent group details here too, for reporting.
    // Exclude groups that have no questions, as they may never leave the 'NORESPONSE' state.
    $query = 'SELECT count(question_groups.xar_group_id)'
        // Descendant groups
        . ' FROM ' . $xartable['surveys_groups'] . ' AS desc_groups'

        // Join to user survey groups that are not VALID
        . ' INNER JOIN ' . $xartable['surveys_user_groups'] . ' AS user_groups'
        . ' ON user_groups.xar_group_id = desc_groups.xar_gid AND user_groups.xar_user_survey_id = ?'
        . ' AND user_groups.xar_status IN (\'NORESPONSE\', \'INVALID\')'

        // Join to the question groups, to ensure there is at least one question.
        // We actually only want one link, so an EXISTS would be nice here...
        . ' INNER JOIN ' . $xartable['surveys_question_groups'] . ' AS question_groups'
        . ' ON question_groups.xar_group_id = desc_groups.xar_gid'

        . ' WHERE desc_groups.xar_left BETWEEN ? AND ?';

    $result = $dbconn->execute($query, array((int)$usid, (int)$left, (int)$right));
    if (!$result) {return;}
    list($count) = $result->fields;

    //echo " name=$name desc=$desc count=$count ";

    if (empty($count)) {
        // Return some details about the group that is complete...
        return array('gid' => (int)$pgid, 'name' => $name, 'desc' => $desc);
    } else {
        // ...or return false if it is not yet complete.
        return false;
    }
}

?>