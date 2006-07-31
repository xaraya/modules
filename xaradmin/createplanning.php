<?php
/**
 * Create a new planning item
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('courses','admin','plancourse') to create a new planning
 *
 * @param  $name the name of the item to be created
 * @param  $number the number of the item to be created
 * @return bool true on success of creation
 * @todo MichelV Create a function to validate this creation
 */
function courses_admin_createplanning($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('courseid',        'id', $courseid, $courseid)) return;
    if (!xarVarFetch('year',            'int:1:', $year)) return;
    if (!xarVarFetch('credits',         'float:1:', $credits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmin',      'float::', $creditsmin, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmax',      'float::', $creditsmax, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startdate',       'str::', $startdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enddate',         'str::', $enddate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('prerequisites',   'str:1:', $prerequisites, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aim',             'str:1:', $aim, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('method',          'str:1:', $method, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('longdesc',        'str:1:', $longdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('costs',           'str:1:', $costs, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('committee',       'str:1:', $committee, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coordinators',    'str:1:', $coordinators, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lecturers',       'str:1:', $lecturers, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('location',        'str:1:', $location, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('material',        'str:1:', $material, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('info',            'str:1:', $info, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('extreg',          'checkbox', $extreg, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('regurl',          'str:1:255', $regurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program',         'str:1:', $program, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hideplanning',    'checkbox', $hideplanning, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('minparticipants', 'int:1:', $minparticipants, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxparticipants', 'int:1:', $maxparticipants, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('closedate',       'str::', $closedate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('expected',        'str::', $expected, '', XARVAR_NOT_REQUIRED)) return;

    /*
    $item = array();
    $item = xarModAPIFunc('courses',
                          'admin',
                          'validatecourse',
                          array('name' => $name,
                                'number' => $number));


    if (!empty($name) && strcmp($item['name'], $name)) {
        $invalid[] = 'duplicatename';
    }
    if (!empty($number) && strcmp($item['number'], $number)) {
        $invalid[] = 'duplicatenumber';
    }
    */

    // Argument check
    $invalid = array();
    // Number of participants
    if ($minparticipants > $maxparticipants) {
      $invalid['minparticipants'] = $minparticipants;
      }
    if (!isset($enddate) || !is_string($enddate)) {
        $invalid[] = 'enddate';
    }
    if (!isset($startdate) || !is_string($startdate)) {
        $invalid[] = 'startdate';
    }
    if (!empty($startdate) && (!empty($enddate))) {
        if (strtotime($enddate) < strtotime($startdate)) {
            $invalid[] = 'enddate';
        }
    }

    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_newcourse function and return the template vars
        return xarModFunc('courses', 'admin', 'plancourse',
                          array('courseid'      => $courseid,
                                'year'          => $year,
                                'credits'       => $credits,
                                'creditsmin'    => $creditsmin,
                                'creditsmax'    => $creditsmax,
                                'startdate'     => $startdate,
                                'enddate'       => $enddate,
                                'prerequisites' => $prerequisites,
                                'aim'           => $aim,
                                'method'        => $method,
                                'longdesc'      => $longdesc,
                                'costs'         => $costs,
                                'committee'     => $committee,
                                'coordinators'  => $coordinators,
                                'lecturers'     => $lecturers,
                                'location'      => $location,
                                'material'      => $material,
                                'info'          => $info,
                                'program'       => $program,
                                'extreg'        => $extreg,
                                'regurl'        => $regurl,
                                'hideplanning'  => $hideplanning,
                                'minparticipants'=> $minparticipants,
                                'maxparticipants'=> $maxparticipants,
                                'closedate'     => $closedate,
                                'expected'      => $expected,
                                'invalid'       => $invalid));
    }
    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Create planning and get planningid
    $planningid = xarModAPIFunc('courses',
                          'admin',
                          'createplanning',
                          array('courseid'          => $courseid,
                                'year'              => $year,
                                'credits'           => $credits,
                                'creditsmin'        => $creditsmin,
                                'creditsmax'        => $creditsmax,
                                'startdate'         => $startdate,
                                'enddate'           => $enddate,
                                'expected'          => $expected,
                                'prerequisites'     => $prerequisites,
                                'aim'               => $aim,
                                'method'            => $method,
                                'longdesc'          => $longdesc,
                                'costs'             => $costs,
                                'committee'         => $committee,
                                'coordinators'      => $coordinators,
                                'lecturers'         => $lecturers,
                                'location'          => $location,
                                'material'          => $material,
                                'info'              => $info,
                                'program'           => $program,
                                'extreg'            => $extreg,
                                'regurl'            => $regurl,
                                'hideplanning'      => $hideplanning,
                                'minparticipants'   => $minparticipants,
                                'maxparticipants'   => $maxparticipants,
                                'closedate'         => $closedate));
    //Check returnvalue
    if (!isset($planningid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarSessionSetVar('statusmsg', xarML('Successfully Created Planning!'));
    xarResponseRedirect(xarModURL('courses', 'admin', 'viewallplanned'));
    // Return
    return true;
}

?>
