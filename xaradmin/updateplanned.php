<?php
/**
 * Update a planned course
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
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('courses','admin','modifycourse') to update a current item
 *
 * @param  id $ 'planningid' the id of the course to be updated
 * @param  string $ 'name' the name of the course to be updated
 * @param  string $ 'number' the number of the course to be updated
 * @return bool true on success
 * @todo MichelV: check those standard settings for $data array
 */
function courses_admin_updateplanned($args)
{
    extract($args);

    // Get parameters from whatever input we need.
    if (!xarVarFetch('planningid', 'id', $planningid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, '', XARVAR_NOT_REQUIRED )) return;
    if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('number', 'str:1:', $number, '',XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coursetype', 'str:1:', $coursetype, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('level', 'int:1:', $level, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('year', 'int:1:', $year, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('credits', 'float::', $credits, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmin', 'float::', $creditsmin, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('creditsmax', 'float::', $creditsmax, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('longdesc', 'str:1:', $longdesc, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('prerequisites', 'str:1:', $prerequisites, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program', 'str:1:', $program, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('committee', 'str:1:', $committee, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('coordinators', 'str:1:', $coordinators, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('lecturers', 'str:1:', $lecturers, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aim', 'str:1:', $aim, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('method', 'str:1:', $method, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('language', 'str:1:', $language, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('location',        'str:1:', $location, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('costs',           'str:1:', $costs, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('material',        'str:1:', $material, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startdate',       'str::', $startdate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('enddate',         'str::', $enddate, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('info',            'str:1:', $info, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('program',         'str:1:', $progra, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('extreg',          'checkbox', $extreg, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('regurl',          'str:5:255', $regurl, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid',         'array::', $invalid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('minparticipants', 'int::', $minparticipants, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('maxparticipants', 'int::', $maxparticipants, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('closedate', 'str::', $closedate, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('hideplanning', 'checkbox', $hideplanning, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('last_modified', 'int', $last_modified, time(), XARVAR_NOT_REQUIRED)) return;
    // At this stage we check to see if we have been passed $objectid, the
    // generic item identifier.
    if (!empty($objectid)) {
        $planningid = $objectid;
    }

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // We don't make an invalid here... so why need it?
    $invalid = array();
    // Check requirements
    if (isset($minparticipants) || isset($maxparticipants)) {
        if ($minparticipants > $maxparticipants) {
          $invalid['minparticipants'] = $minparticipants;
          }
    }

    if (empty($name)) {
        $data['name'] = '';
    } else {
        $data['name'] = $name;
    }

    if (empty($number)) {
        $data['number'] = '';
    } else {
        $data['number'] = $number;
    }

     if (empty($coursetype)) {
        $data['coursetype'] = '';
    } else {
        $data['coursetype'] = $coursetype;
    }

     if (empty($level)) {
        $data['level'] = '';
    } else {
        $data['level'] = $level;
    }

     if (empty($year)) {
        $data['year'] = '';
    } else {
        $data['year'] = $year;
    }

    if (empty($credits)) {
        $data['credits'] = '';
    } else {
        $data['credits'] = $credits;
    }
    if (empty($creditsmin)) {
        $data['creditsmin'] = '';
    } else {
        $data['creditsmin'] = $creditsmin;
    }
    if (empty($contact)) {
        $data['contact'] = '';
    } else {
        $data['contact'] = $contact;
    }
    $data['hideplanning'] = $hideplanning;

    // check if we have any errors
    if (count($invalid) > 0) {
        // call the admin_modifycourse function and return the template vars
        // (you need to copy admin-new.xd to admin-create.xd here)
        return xarModFunc('courses', 'admin', 'modifyplanned',
                          array('planningid' => $planningid,
                                'name' => $name,
                                'number' => $number,
                                'year' => $year,
                                'credits' => $credits,
                                'creditsmin' => $creditsmin,
                                'creditsmax' => $creditsmax,
                                'startdate' => $startdate,
                                'enddate' => $enddate,
                                'prerequisites' => $prerequisites,
                                'aim' => $aim,
                                'method' => $method,
                                'longdesc' => $longdesc,
                                'costs' => $costs,
                                'committee' => $committee,
                                'coordinators' => $coordinators,
                                'lecturers' => $lecturers,
                                'location' => $location,
                                'material' => $material,
                                'info' => $info,
                                'program'       => $program,
                                'extreg'        => $extreg,
                                'regurl'            => $regurl,
                                'minparticipants' => $minparticipants,
                                'maxparticipants' => $maxparticipants,
                                'closedate' => $closedate,
                                'hideplanning' => $hideplanning,
                                'last_modified' => $last_modified,
                                'invalid' => $invalid));
    }

    // The API function is called.
    if (!xarModAPIFunc('courses',
                       'admin',
                       'updateplanned',
                       array(   'planningid'        => $planningid,
                                'name'              => $name,
                                'number'            => $number,
                                'year'              => $year,
                                'credits'           => $credits,
                                'creditsmin'        => $creditsmin,
                                'creditsmax'        => $creditsmax,
                                'startdate'         => $startdate,
                                'enddate'           => $enddate,
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
                                'minparticipants' => $minparticipants,
                                'maxparticipants' => $maxparticipants,
                                'closedate' => $closedate,
                                'hideplanning' => $hideplanning))) {
        return; // throw back
    }
    xarSessionSetVar('statusmsg', xarML('Planned Course Was Successfully Updated!'));
    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('courses', 'admin', 'viewallplanned'));
    // Return
    return true;
}

?>
