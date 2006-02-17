<?php
/**
 * Set the current survey vars for a user.
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
 * Set the current survey vars for a user.
 *
 * Sets the details in the module user var or the session, as appropriate.
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

function surveys_userapi_setcurrentsurveyvars($args) {
    // Variable name.
    $name = 'surveys.current_survey';

    // TODO: do some checks - if we are or are not logged in, then does the
    // user ID correctly match the one we are storing against?

    // If logged in, store the details in the user vars, else the session vars.
    if (xarUserIsLoggedIn()) {
        xarModSetUserVar('surveys', $name, serialize($args));
    } else {
        xarSessionSetVar($name, $args);
    }

    return true;
}
?>