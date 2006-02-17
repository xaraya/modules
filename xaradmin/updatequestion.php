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
 * Update a survey question
 *
 * Long Description [OPTIONAL one or more lines]
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param 'qid' the id of the question to be modified
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
 */
function surveys_admin_updatequestion($args)
{
    extract($args);

    // Get parameters from whatever input we need
    if (!xarVarFetch('qid', 'id', $qid,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('name', 'str:1:100', $name,  NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('desc', 'str', $desc,  '', XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('mandatory', 'enum:Y:N:y:n', $mandatory,  'N', XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('default', 'str:0:200', $default,  NULL, XARVAR_DONT_SET)) {return;}

    // Fetch existing question details.
    //$question = xarModAPIFunc(
    //    'surveys', 'user', 'get',
    //    array('qid' => $qid)
    //);
    //if (!$question) {return;}

    // Security Check
    if (!xarSecurityCheck('EditSurvey', 1, 'Survey', 'All')) {
        // No privilege for editing survey structures.
        return false;
    }

    $result = xarModAPIfunc(
        'surveys', 'admin', 'update',
        array (
            'qid' => $qid,
            'name' => $name,
            'desc' => $desc,
            'mandatory' => $mandatory,
            'default' => $default
        )
    );

    // Throw back if update failed.
    if (!$result) {return;}

    // Redirect if successful.
    xarResponseRedirect(xarModURL('surveys', 'admin', 'modifyquestion', array('qid'=>$qid)));

    return true;
}

?>
