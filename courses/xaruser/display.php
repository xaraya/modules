<?php
 /**
 * File: $Id: 
 * 
 * Display an item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author XarayaGeek 
 */

/**
 * display an item
 * This is a standard function to provide detailed informtion on a single item
 * available from the module.
 * 
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['exid'] the item id used for this example module
 */
function courses_user_display($args)
{
    // User functions of this type can be called by other modules.  If this
    // happens then the calling module will be able to pass in arguments to
    // this function through the $args parameter.  Hence we extract these
    // arguments *before* we have obtained any form-based input through
    // xarVarFetch(), so that parameters passed by the modules can also be
    // checked by a certain validation.
    extract($args);
    // function should be obtained from xarVarFetch(), xarVarCleanFromInput()
    // is a degraded function.  xarVarFetch allows the checking of the input
    // variables as well as setting default values if needed.  Getting vars
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    if (!xarVarFetch('courseid', 'int:1:', $courseid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enrolled', 'str:1:', $enrolled, '', XARVAR_NOT_REQUIRED)) return;

    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.  This could have been passed in by a hook or
    // through some other function calling this as part of a larger module, but
    // if it exists it overrides $exid

    // Note that this module could just use $objectid everywhere to avoid all
    // of this munging of variables, but then the resultant code is less
    // descriptive, especially where multiple objects are being used.  The
    // decision of which of these ways to go is up to the module developer
    if (!empty($objectid)) {
        $courseid = $objectid;
    }
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('courses', 'user', 'menu');
    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    // The API function is called.  The arguments to the function are passed in
    // as their own arguments array.
    // Security check 1 - the get() function will fail if the user does not
    // have at least READ access to this item (also see below).
    $item = xarModAPIFunc('courses',
        'user',
        'get',
        array('courseid' => $courseid));
    if (!isset($item) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back

    // If your module deals with different types of items, you should specify the item type
    // here, before calling any hooks
    // $item['itemtype'] = 0;
    // Security check 2 - if your API function does *not* check for the
    // appropriate access rights, or if for some reason you require higher
    // access than READ for this function, you *must* check this here !
    // if (!xarSecurityCheck('CommentExample',0,'Item',"$item[name]:All:$item[exid]")) {
    // // Fill in the status variable with the status to be shown
    // $data['status'] = _EXAMPLENOAUTH;
    // // Return the template variables defined in this function
    // return $data;
    // }
    // Let any transformation hooks know that we want to transform some text.
    // You'll need to specify the item id, and an array containing the names of all
    // the pieces of text that you want to transform (e.g. for autolinks, wiki,
    // smilies, bbcode, ...).
    $item['transform'] = array('name');
    $item = xarModCallHooks('item',
        'transform',
        $courseid,
        $item);
    // Fill in the details of the item.  Note that a module variable is used here to determine
    // whether or not parts of the item information should be displayed in
    // bold type or not
    $data['name_label'] = xarVarPrepForDisplay(xarML('Course Name:'));
    $data['number_label'] = xarVarPrepForDisplay(xarML('Course Number:'));
	$data['hours_label'] = xarVarPrepForDisplay(xarML('Course Hours:'));
	$data['ceu_label'] = xarVarPrepForDisplay(xarML('Course Credit Hours:'));
	$data['startdate_label'] = xarVarPrepForDisplay(xarML('Course Start Date:'));
	$data['enddate_label'] = xarVarPrepForDisplay(xarML('Course End Date:'));
	$data['shortdesc_label'] = xarVarPrepForDisplay(xarML('Short Course Description:'));
	$data['longdesc_label'] = xarVarPrepForDisplay(xarML('Course Description:'));
    $data['name_value'] = $item['name'];
    $data['number_value'] = $item['number'];
	$data['hours_value'] = $item['hours'];
	$data['ceu_value'] = $item['ceu'];
	$data['startdate_value'] = $item['startdate'];
	$data['enddate_value'] = $item['enddate'];
	$data['shortdesc_value'] = xarVarPrepHTMLDisplay($item['shortdesc']);
	$data['longdesc_value'] = xarVarPrepHTMLDisplay($item['longdesc']);
    //$data['enrolled'] = xarVarPrepForDisplay(xarML('You are currently enrolled in this course'));
    $data['courseid'] = $courseid;

    $data['is_bold'] = xarModGetVar('courses', 'bold');

     // Get the username so we can pass it to the enrollment function
    $uid = xarUserGetVar('uid');
    //Check to see if this user is already enrolled in this course
   $courses = xarModAPIFunc('courses',
                          'user',
                          'check_enrolled',
                          array('uid' => $uid,
                                'courseid' => $courseid));

    if (!isset($courses) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
//       echo "<br /><pre>items => "; print_r($courses); echo "</pre>";
    if (isset($courses[$courseid])) {

        $data['enrolled'] = xarVarPrepForDisplay(xarML('You are currently enrolled in '. $courses[$courseid] ));
    }


    // Note : module variables can also be specified directly in the
    // blocklayout template by using &xar-mod-<modname>-<varname>;
    // Note that you could also pass on the $item variable, and specify
    // the labels directly in the blocklayout template. But make sure you
    // use the <xar:ml>, <xar:mlstring> or <xar:mlkey> tags then, so that
    // labels can be translated for other languages...
    // Save the currently displayed item ID in a temporary variable cache
    // for any blocks that might be interested (e.g. the Others block)
    // You should use this -instead of globals- if you want to make
    // information available elsewhere in the processing of this page request
    xarVarSetCached('Blocks.courses', 'courseid', $courseid);
    // Let any hooks know that we are displaying an item.  As this is a display
    // hook we're passing a return URL in the item info, which is the URL that any
    // hooks will show after they have finished their own work.  It is normal
    // for that URL to bring the user back to this function
    $item['returnurl'] = xarModURL('courses',
        'user',
        'display',
        array('courseid' => $courseid));
    $hooks = xarModCallHooks('item',
        'display',
        $courseid,
        $item);
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } else {
        // You can use the output from individual hooks in your template too, e.g. with
        // $hookoutput['comments'], $hookoutput['hitcount'], $hookoutput['ratings'] etc.
        $data['hookoutput'] = $hooks;
    }
    // Once again, we are changing the name of the title for better
    // Search engine capability.
    xarTplSetPageTitle(xarVarPrepForDisplay($item['name']));
    // Return the template variables defined in this function
    return $data;
    // Note : instead of using the $data variable, you could also specify
    // the different template variables directly in your return statement :

    // return array('menu' => ...,
    // 'item' => ...,
    // 'hookoutput' => ...,
    // ... => ...);
}

?>
