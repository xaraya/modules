<?php
/**
 * Add new question
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys Module
 */

/**
 * Add new question
 *
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 *
 * @author Surveys module development team
 * @return array
 */
function surveys_admin_newquestion($args)
{
    extract($args);
    if (!xarVarFetch('type_id',     'int:1:', $type_id,  $type_id,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name',        'str:1:', $name,     $name,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('desc',        'str:1:', $desc,     $desc,     XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('mandatory',   'enum:Y:N', $mandatory,$mandatory,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('default',     'str:1:', $default,  $default,  XARVAR_NOT_REQUIRED)) return;

    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = xarModAPIFunc('surveys', 'admin', 'menu');
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddSurvey')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;

    $itemtypes = xarModAPIfunc(
        'surveys', 'user', 'gettypes',
        array('type' => 'Q')
    );
    $data['itemtypes']= $itemtypes;
    $item = array();
    $item['module'] = 'surveys';
    $item['itemid'] = 0;
    $item['itemtype'] = 'Q';// TODO: ???$type_id;
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        /* You can use the output from individual hooks in your template too, e.g. with
         * $hookoutput['categories'], $hookoutput['dynamicdata'], $hookoutput['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }

    /* Type ID goes here
     */
    if (empty($name)) {
        $data['name'] = '';
    } else {
        $data['name'] = $name;
    }

    if (empty($desc)) {
        $data['desc'] = '';
    } else {
        $data['desc'] = $desc;
    }

    if (empty($default)) {
        $data['default'] = '';
    } else {
        $data['default'] = $default;
    }
    if (empty($mandatory)) {
        $data['mandatory'] = '';
    } else {
        $data['mandatory'] = $mandatory;
    }
    /* Return the template variables defined in this function */
    return $data;
}
?>