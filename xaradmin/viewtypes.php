<?php
/**
 * View the course types
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
 * Standard function to coursetypes
 *
 * @author Courses module development team
 * @param int startnum
 * @return array
 */
function courses_admin_viewtypes()
{
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    /* Initialise the $data variable that will hold the data */
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    /* Initialise the variable that will hold the items, so that the template
     * doesn't need to be adapted in case of errors
     */
    $data['items'] = array();

    if (!xarSecurityCheck('AdminCourses')) return;
    /* The user API function is called. */
    $items = xarModAPIFunc('courses',
                           'user',
                           'getall_coursetypes',
                            array('startnum' => $startnum,
                                  'numitems' => xarModGetVar('courses','itemsperpage')));
    /* Check for exceptions */
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Check individual permissions for Edit / Delete */
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];
        $items[$i]['editurl'] = xarModURL('courses',
            'admin',
            'modifytype',
            array('tid' => $item['tid']));

        $items[$i]['deleteurl'] = xarModURL('courses',
            'admin',
            'deletetype',
            array('tid' => $item['tid']));
    }
    /* Add the array of items to the template variables */
    $data['items'] = $items;

    /* Return the template variables defined in this function */
    return $data;

}
?>