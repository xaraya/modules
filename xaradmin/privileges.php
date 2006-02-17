<?php
/**
 * Surveys table definitions function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys
 * @author Surveys module development team
 */
/*
 * Privileges wizard for surveys.
 *
 * Use a wizard to define privileges for assessments
 *
 * @author     Jason Judge <jason.judge@academe.co.uk>
 * @author     Another Author <another@example.com>          [REQURIED]
 * @param string $arg1  the string used                      [OPTIONAL A REQURIED]
 * @param int    $arg2  an integer and use description
 *                      Identing long comments               [OPTIONAL A REQURIED]
 * @param  $sid survey id
 * @param  $system_status
 * @param  $status
 * @param  $uid User ID, take all users from roles
 *
 * @return array  the data for the template with dropdownlists.
 * @access      private
 * @static                                                   [OPTIONAL]
 * @link       link to a reference                           [OPTIONAL]
 * @see        anothersample(), someotherlinke [reference to other function, class] [OPTIONAL]
 *
 * @TODO MichelV: impose restriction on user group?
 */

function surveys_admin_privileges($args) {
    extract($args);

    // Parameters passed by the privileges module.
    if (!xarVarFetch('extpid',      'isset', $extpid,       NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extname',     'isset', $extname,      NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extrealm',    'isset', $extrealm,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extmodule',   'isset', $extmodule,    NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extcomponent','isset', $extcomponent, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extlevel',    'isset', $extlevel,     NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('extinstance', 'isset', $extinstance,  NULL, XARVAR_DONT_SET)) {return;}

    // The user has asked for the details to be applied.
    if (!xarVarFetch('apply', 'isset', $apply, NULL, XARVAR_DONT_SET)) {return;}

    // Submitted values from the wizard form.
    if (!xarVarFetch('sid',             'isset', $sid,           NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('system_status',   'isset', $system_status, NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('status',          'isset', $status,        NULL, XARVAR_DONT_SET)) {return;}
    if (!xarVarFetch('uid',             'isset', $uid,           NULL, XARVAR_DONT_SET)) {return;}

    // Get the current instance details (split up).
    // This would only get invoked on first being called up from the privileges
    // module.
    if (!empty($extinstance) && $extcomponent == 'Assessment') {
        $parts = explode(':', $extinstance);
        if (count($parts) > 0 && !empty($parts[0])) {$sid = $parts[0];}
        if (count($parts) > 1 && !empty($parts[1])) {$system_status = $parts[1];}
        if (count($parts) > 2 && !empty($parts[2])) {$status = $parts[2];}
        if (count($parts) > 3 && !empty($parts[3])) {$uid = $parts[3];}
    }

    $newinstance = array();

    if ($extcomponent == 'Assessment') {
        $newinstance[] = empty($sid) ? 'All' : $sid;
        $newinstance[] = empty($system_status) ? 'All' : $system_status;
        $newinstance[] = empty($status) ? 'All' : $status;
        $newinstance[] = empty($uid) ? 'All' : $uid;
    }

    if (!empty($apply)) {
        // Create or update the privilege
        $pid = xarReturnPrivilege($extpid, $extname, $extrealm, $extmodule, $extcomponent, $newinstance, $extlevel);
        if (empty($pid)) {
            // Throw back.
            return;
        }

        // Redirect to the privilege maintenance screen.
        xarResponseRedirect(
            xarModURL('privileges', 'admin', 'modifyprivilege',
            array('pid' => $pid))
        );
        return true;
    }

    // Get selection lists for the form.

    // List of surveys.
    $surveys = xarModAPIfunc('surveys', 'user', 'getsurveys');
    array_unshift($surveys, array('sid'=>'All', 'name'=>'All', 'desc'=>''));

    // System status.
    $system_statuses = xarModAPIfunc(
        'surveys', 'user', 'lookupstatuses',
        array('type' => 'SURVEY', 'return' => 'system_status')
    );
    array_unshift($system_statuses, 'All');

    // User status.
    $statuses = xarModAPIfunc(
        'surveys', 'user', 'lookupstatuses',
        array(
            'type' => 'SURVEY',
            'return' => 'status',
            'system_status' => ($system_status != 'All' ? $system_status : NULL)
        )
    );
    array_unshift($statuses, 'All');

    // List of users.
    $users = xarModAPIfunc('roles', 'user', 'getall');
    foreach($users as $key => $user) {
        if ($user['uname'] == 'myself') {
            $users[$key]['uid'] = 'Myself';
            break;
        }
    }
    array_unshift($users, array('uid'=>'All', 'name'=>'All', 'uname'=>'all'));

    $data = array(
        // Standard wizard variables:
        'extpid'        => $extpid,
        'extname'       => $extname,
        'extrealm'      => $extrealm,
        'extmodule'     => $extmodule,
        'extcomponent'  => $extcomponent,
        'extlevel'      => $extlevel,
        'extinstance'   => join(':', $newinstance),
        // Wizard-specific:
        'sid'           => $sid,
        'system_status' => $system_status,
        'status'        => $status,
        'uid'           => $uid,
        // Selection lists:
        'surveys'       => $surveys,
        'system_statuses' => $system_statuses,
        'statuses'      => $statuses,
        'users'         => $users
    );

    return $data;
}

?>