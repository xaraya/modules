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
 * @author XarayaGeek , Michel V.
 */

/**
 * display a course
 * This is the function to provide detailed information on a single course
 * and show the details of all planned occurences
 * 
 * @param  $args an array of arguments (if called by other modules)
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['courseid'] the ID of the course
 */
function courses_user_display($args)
{
    extract($args);
    if (!xarVarFetch('courseid', 'int:1:', $courseid)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enrolled', 'str:1:', $enrolled, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $courseid = $objectid;
    }
    // Initialise the $data variable
    $data = xarModAPIFunc('courses', 'user', 'menu');
    // Prepare the variable that will hold some status message if necessary
    $data['status'] = '';
    // The API function is called.  The arguments to the function are passed in
    // as their own arguments array.
    $item = xarModAPIFunc('courses',
        'user',
        'get',
        array('courseid' => $courseid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

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
    $data['namelabel'] = xarVarPrepForDisplay(xarML('Course Name'));
    $data['numberlabel'] = xarVarPrepForDisplay(xarML('Course Number'));
    $data['coursetypelabel'] = xarVarPrepForDisplay(xarML('Course Type (Category)'));
    $data['levellabel'] = xarVarPrepForDisplay(xarML('Course Level'));
    $data['creditslabel'] = xarVarPrepForDisplay(xarML('Course Credits'));
    $data['startdatelabel'] = xarVarPrepForDisplay(xarML('Start date'));
    $data['enddatelabel'] = xarVarPrepForDisplay(xarML('End date'));
    $data['costslabel'] = xarVarPrepForDisplay(xarML('Course Fee'));
    $data['materiallabel'] = xarVarPrepForDisplay(xarML('Course materials'));
    $data['creditsminlabel'] = xarVarPrepForDisplay(xarML('Course Minimum Credits'));
    $data['creditsmaxlabel'] = xarVarPrepForDisplay(xarML('Course Maximum Credits'));
    $data['prereqlabel'] = xarVarPrepForDisplay(xarML('Course Prerequisites'));
    $data['aimlabel'] = xarVarPrepForDisplay(xarML('Course Aim'));
    $data['coordinatorslabel'] = xarVarPrepForDisplay(xarML('Course coordinators'));
    $data['committeelabel'] = xarVarPrepForDisplay(xarML('Course committee'));
    $data['lecturerslabel'] = xarVarPrepForDisplay(xarML('Course lecturers'));
    $data['locationlabel'] = xarVarPrepForDisplay(xarML('Course location'));
    $data['programlabel'] = xarVarPrepForDisplay(xarML('Course Programme'));
    $data['shortdesclabel'] = xarVarPrepForDisplay(xarML('Course Description'));
    $data['methodlabel'] = xarVarPrepForDisplay(xarML('Course Method'));
    $data['languagelabel'] = xarVarPrepForDisplay(xarML('Course Language'));
    $data['freqlabel'] = xarVarPrepForDisplay(xarML('Course Frequency'));
    $data['contactlabel'] = xarVarPrepForDisplay(xarML('Course Contact details'));
    $data['hideplanninglabel'] = xarVarPrepForDisplay(xarML('Hide this occurence'));
    $data['infolabel'] = xarVarPrepForDisplay(xarML('Other Course info'));
    $data['enrollbutton'] = xarVarPrepForDisplay(xarML('Enroll'));
    $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));
    //$data['enrolled'] = xarVarPrepForDisplay(xarML('You are currently enrolled in this course'));
    $data['courseid'] = $courseid;
    $data['item'] = $item;
    $data['is_bold'] = xarModGetVar('courses', 'bold');

     // Get the username so we can pass it to the enrollment function
    $uid = xarUserGetVar('uid');
    //Check to see if this user is already enrolled in this course
/*   $courses = xarModAPIFunc('courses',
                          'user',
                          'check_enrolled',
                          array('uid' => $uid,
                                'courseid' => $courseid));

    if (!isset($courses) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
//       echo "<br /><pre>items => "; print_r($courses); echo "</pre>";
    if (isset($courses[$courseid])) {

        $data['enrolled'] = xarVarPrepForDisplay(xarML('You are currently enrolled in '. $courses[$courseid] ));
    }

*/
    $data['levelname'] = xarModAPIFunc('courses', 'user', 'getlevel',
                                      array('level' => $item['level']));
    $items = xarModAPIFunc('courses',
        'user',
        'getplandates',
        array('courseid' => $courseid));
    //TODO: howto check for correctness here?
    //if (!isset($plandates) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Check individual permissions for Enroll/Edit/Viewstatus
    // Note : we could use a foreach ($items as $item) here as well, as
    // shown in xaruser.php, but as an example, we'll adapt the $items array
    // 'in place', and *then* pass the complete items array to $data

    for ($i = 0; $i < count($items); $i++) {
        $planitem = $items[$i];
        if (xarSecurityCheck('EditPlanning', 0, 'Item', "All:All:$courseid")) {
            $items[$i]['participantsurl'] = xarModURL('courses',
                'admin',
                'participants',
                array('planningid' => $planitem['planningid']));
        } else {
            $items[$i]['participantsurl'] = '';
        }
        $items[$i]['participantstitle'] = xarML('Participants');
        
        if (xarSecurityCheck('ReadCourses', 0, 'Item', "All:All:$courseid")) {
            $items[$i]['enrollurl'] = xarModURL('courses',
                'user',
                'enroll',
                array('planningid' => $planitem['planningid']));
        } else {
            $items[$i]['enrollurl'] = '';
        }
        $items[$i]['enrolltitle'] = xarML('Enroll');
        
        if (xarSecurityCheck('ReadPlanning', 0, 'Item', "$planitem[planningid]:All:$courseid")) {
            $items[$i]['detailsurl'] = xarModURL('courses',
                'user',
                'displayplanned',
                array('planningid' => $planitem['planningid'], 'courseid'=> $courseid));
        } else {
            $items[$i]['detailsurl'] = '';
        }
        $items[$i]['detailstitle'] = xarML('Details');
        
        if (xarSecurityCheck('DeleteCourses', 0, 'Item', "$planitem[planningid]:All:$courseid")) {
            $items[$i]['statusurl'] = xarModURL('courses',
                'user',
                'status',
                array('planningid' => $planitem['planningid']));
        } else {
            $items[$i]['statusurl'] = '';
        }
        $items[$i]['statustitle'] = xarML('Status');
    }
    
    // Add the array of items to the template variables
    $data['items'] = $items;    
    
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
    $data['authid'] = xarSecGenAuthKey();
    // Once again, we are changing the name of the title for better
    // Search engine capability.
    xarTplSetPageTitle(xarVarPrepForDisplay($item['name']));
    // Return the template variables defined in this function
    return $data;
}

?>
