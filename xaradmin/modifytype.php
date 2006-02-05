<?php
/**
 * Modify an item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * Modify a course type
 *
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @author Courses Module Development Team
 * @param  $ 'tid' the id of the item to be modified
 */
function courses_admin_modifytype($args)
{
    extract($args);

    if (!xarVarFetch('tid',     'id',     $tid)) return;
    if (!xarVarFetch('objectid', 'id',     $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',  'array', $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype',   'str::',    $coursetype, $coursetype,XARVAR_NOT_REQUIRED)) return;
   // if (!xarVarFetch('name',     'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $tid = $objectid;
    }
    $item = xarModAPIFunc('courses',
                          'user',
                          'gettype',
                          array('tid' => $tid));

    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('AdminCourses')) {
        return;
    }
    /* Get menu variables - it helps if all of the module pages have a standard
     * menu at their head to aid in navigation
     * $menu = xarModAPIFunc('courses','admin','menu','modify');
     */
    // call modifyconfig hooks with module + itemtype
    $hooks = xarModCallHooks('module', 'modifyconfig', 'courses',
                             array('module'   => 'courses',
                                   'itemtype' => $tid));

    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks.'));
    } else {
        $data['hooks'] = $hooks;
    }
/*
    // get root categories for this publication type
    if (!empty($tid)) {
        $catlinks = xarModAPIFunc('courses',
                                 'user',
                                 'getrootcats',
                                 array('tid' => $tid));
    // Note: if you want to use a *combination* of categories here, you'll
    //       need to use something like 'c15+32'
        foreach ($catlinks as $catlink) {
            $viewoptions[] = array('value' => 'c' . $catlink['catid'],
                                   'label' => xarML('Browse in') . ' ' .
                                              $catlink['cattitle']);
        }
    }
    $data['viewoptions'] = $viewoptions;
*/
    /* Return the template variables defined in this function */
    $data['authid']   = xarSecGenAuthKey();
     $data['coursetype']   = $coursetype;
     $data['invalid']      = $invalid;
     $data['item']         = $item;

     return $data;
}
?>