<?php
/**
 * Add new coursetype
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Add new item
 *
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 *
 * @author Example module development team
 * @return array
 */
function courses_admin_newtype($args)
{
    extract($args);

 //   if (!xarVarFetch('number',  'int:1:', $number,  $number,  XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype',    'str:1:', $coursetype,    $coursetype,    XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array',  $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('courses', 'admin', 'menu');
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddCourses')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    $data['invalid'] = $invalid;

    $item = array();
    $item['module'] = 'courses';
    $hooks = xarModCallHooks('item', 'new', '', $item);

    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        $data['hookoutput'] = $hooks;
    }
    $data['hooks'] = '';
    /* For E_ALL purposes, we need to check to make sure the vars are set.
     * If they are not set, then we need to set them empty to surpress errors
     */
    if (empty($coursetype)) {
        $data['coursetype'] = '';
    } else {
        $data['coursetype'] = $coursetype;
    }

    /* Return the template variables defined in this function */
    return $data;
}
?>