<?php
/**
 * Surveys refresh (custom)
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
 * Refresh surveys (custom command).
 *
 * Only in use for REMAS
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
 * @deprecated Nov 2005
 */

function surveys_admin_refreshsurveys() {
    if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
        // No privilege for editing survey structures.
        return false;
    }

    // Database stuff.
    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $query = 'SELECT xar_usid FROM remas_surveys_user_surveys';
    $result1 = $dbconn->execute($query);
    if (!$result1) {return;}

    while (!$result1->EOF) {
        list($usid) = $result1->fields;

        echo " $usid<br/> ";

        // 172 = REGISTER3
        /*
        xarModAPIfunc(
            'surveys', 'admin', 'updateusergroupstatus',
            array('gid' => 172, 'usid' => $usid)
        );
        */

        // 132 = EP-START
        /*
        xarModAPIfunc(
            'surveys', 'admin', 'updateusergroupstatus',
            array('gid' => 132, 'usid' => $usid)
        );
        */

        // Update the groups according to the rules.

        // Get the next row.
        $result1->MoveNext();
    }
}

?>