<?php
/**
 * Surveys Workflow for user user survey status changes.
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
 * Workflow for user user survey status changes.
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
 * Workflow for user user survey status changes.
 * This is very simplified. Ideally it should provide:
 * - single states where an action has only one single resultant state
 * - mulitple states where a current state contains a set of optional next states
 * - different states depending upon the user (if invoked by a user)
 *   (the user could be identified in the action: USER-SUBMIT-USERSURVEY,
 *   TIME-TRANSFER-USERSURVEY, etc.)
 */

function surveys_userapi_workflow($args) {
    // Expand arguments.
    extract($args);

    // An action is needed.
    if (!isset($action)) {
        return;
    }

    $return = NULL;
    switch ($action) {
        case 'SUBMIT-USERSURVEY':
            // Submit a user survey.
            // Params: sid, [current] status
            switch ($sid) {
                case 2: // Standard survey.
                    $return = 'SUBMITTED';
                    break;
                case 1: // Test survey.
                case 3: // Site details survey.
                    $return = 'COMPLETE';
                    break;
                default:
                    break;
            }
        default:
            break;
    }

    return $return;
}

?>