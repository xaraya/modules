<?php
/**
 * File: $Id:
 * 
 * Standard function to create a new module item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team 
 */
/**
 * add new dynamic data item for courses
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 */
function courses_admin_new($args)
{
    // Admin functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *before* we have obtained any form-based input through
    // xarVarFetch().
    extract($args);

    // Get parameters for the dyn data objects.
    if (!xarVarFetch('itemtype', 'int:1:', $itemtype, 3, XARVAR_GET_OR_POST)) return;
    if (!xarVarFetch('preview', 'str', $preview, '', XARVAR_NOT_REQUIRED)) return;
    if (empty($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'item type', 'admin', 'new', 'courses');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return $msg;
    }
    
    
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('courses', 'admin', 'menu');
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminCourses')) return;
    
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    $data['object'] =& xarModAPIFunc('dynamicdata','user','getobject',
                                     array('module'   => 'courses',
                                           'itemtype' => $itemtype )
                                    );
    if (!isset($data['object'])) return;  // throw back

    $item = array();
    $item['module'] = 'courses';
    $item['itemtype'] = $itemtype;
    $hooks = xarModCallHooks('item','new','',$item);
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
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
