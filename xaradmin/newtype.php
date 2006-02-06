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
 * Add new type of course
 *
 * A coursetype is a general type of courses that is used to determine the general lay-out and functioning of this coursetype
 * You can add courses in this type later
 *
 * @author MichelV <michelv@xaraya.com>
 * @return array
 */
function courses_admin_newtype($args)
{
    extract($args);


    if (!xarVarFetch('coursetype', 'str:1:',    $coursetype, $coursetype, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('descr',      'str:1:255', $descr,      $descr,      XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('settings',   'str:1:255', $settings,   $settings,   XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',    'array',     $invalid,    $invalid,    XARVAR_NOT_REQUIRED)) return;

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
    if (empty($descr)) {
        $data['descr'] = '';
    } else {
        $data['descr'] = $descr;
    }
    if (empty($settings)) {
        $data['settings'] = '';
    } else {
        $data['settings'] = $settings;
    }
    /* Return the template variables defined in this function */
    return $data;
}
?>