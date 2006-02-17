<?php
/**
 * Create a new question
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys Module
 */

/**
 * Create a new question
 *
 * Standard function to create a new item
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('example','admin','new') to create a new item
 *
 * @author Jason Judge, MichelV
 * @param  $ 'name' the name of the item to be created
 * @param  $ 'desc' the
 */
function surveys_admin_createquestion($args)
{
    extract($args);
/*
xar_qid         I AUTO PRIMARY,
                xar_type_id     I NOTNULL default 0,
                xar_name        C(100) default NULL,
                xar_desc        text,
                xar_mandatory   C(1) NOTNULL default N,
                xar_default     C(200) default NULL
*/
    if (!xarVarFetch('objectid',    'int:1:',       $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',     'str:1:',       $invalid,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('type_id',     'int::',        $type_id,  0, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('name',        'str:1:100',    $name,  NULL, XARVAR_DONT_SET)) return;
    if (!xarVarFetch('desc',        'str',          $desc,  '', XARVAR_DONT_SET)) return;
    if (!xarVarFetch('mandatory',   'enum:Y:N:y:n', $mandatory,  'N', XARVAR_DONT_SET)) return;
    if (!xarVarFetch('default',     'str:0:200',    $default,  NULL, XARVAR_DONT_SET)) return;
    /* Argument check
     * TODO

    $item = xarModAPIFunc('example',
                          'user',
                          'validateitem',
                          array('name' => $name));
*/
    // Argument check
    $invalid = array();
    if (empty($mandatory) || !is_string($mandatory)) {
        $invalid['mandatory'] = 1;
        $number = '';
    }
    if (empty($name) || !is_string($name)) {
        $invalid['name'] = 1;
        $name = '';
    }
/*
    if (!empty($name) && $item['name'] == $name) {
        $invalid['duplicatename'] = 1;
        $duplicatename = '';
    }
*/
    // check if we have any errors
    if (count($invalid) > 0) {
        /* If we get here, we have encountered errors.
         * Send the user back to the admin_new form
         * call the admin_new function and return the template vars
         */
        return xarModFunc('surveys', 'admin', 'newquestion',
                          array('type_id' => $type_id,
                                'name' => $name,
                                'desc' => $desc,
                                'mandatory' => $mandatory,
                                'default' => $default,
                                'invalid' => $invalid));
    }

    if (!xarSecConfirmAuthKey()) return;

    $qid = xarModAPIFunc('surveys',
                          'admin',
                          'createquestion',
                          array('type_id' => $type_id,
                                'name' => $name,
                                'desc' => $desc,
                                'mandatory' => $mandatory,
                                'default' => $default));
    /* The return value of the function is checked here
     */
    if (!isset($qid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('survey', 'admin', 'view'));// TODO: link
    /* Return true, in this case */
    return true;
}
?>