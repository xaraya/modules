<?php
/**
 * Standard function to create a new module item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Add new dynamic data item for the courses configuration
 *
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 *
 * @param int itemtype The itemtype to create an item for
 * @param string preview Create a preview, or not?
 * @return array
 */
function courses_admin_new($args)
{
    extract($args);

    // Get parameters for the dyn data objects.
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, 1003, XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('preview',  'str', $preview, '', XARVAR_NOT_REQUIRED)) return;
    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'new', 'courses');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }

    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminCourses')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['object'] = xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module'   => 'courses',
                                           'itemtype' => $itemtype )
                                    );
    if (!isset($data['object'])) return;  // throw back

    $item = array();
    $item['module'] = 'courses';
    $item['itemtype'] = $itemtype;

    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = array();
    }else {
        $data['hooks'] = $hooks;
    }

    // The Generic Menu
    $data['menu']      = xarModFunc('courses','admin','menu');
    $data['menutitle'] = xarVarPrepForDisplay(xarML('Make a new hooked dynamic data object'));
    $data['itemtype'] = $itemtype;
    $data['preview'] = $preview;
    // Return the template variables defined in this function
    return $data;
}

?>
