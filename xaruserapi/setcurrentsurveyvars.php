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
 * Set the current survey vars for a user.
 * Sets the details in the module user var or the session, as appropriate.
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