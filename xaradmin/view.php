<?php
/**
 * Standard function to view courses and their planning
 *
 * @package modules
 * @copyright (C) 2005-2006 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */

/**
 * view course parameters
 * Parameters are derived from Dynamic Data
 *
 * @author MichelV <michelv@xaraya.com>
 * @param itemtype
 * @param startnum
 */
function courses_admin_view()
{
    // Get Vars
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, 1003, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('courses', 'admin', 'menu');
    $data['items'] = array();
    $data['itemsperpage'] = xarModGetVar('courses','itemsperpage');
    $data['itemtype'] = $itemtype;
    $data['startnum'] = $startnum;
    // The Generic Menu
    $data['menu']      = xarModFunc('courses','admin','menu');
    $data['menutitle'] = xarVarPrepForDisplay(xarML('View the hooked dynamic data options'));

    if (empty($data['itemtype'])){
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'view', 'courses');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }
    if (!xarSecurityCheck('EditCourses')) return;

    // Return the template variables defined in this function
    return $data;
}

?>
