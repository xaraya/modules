<?php
/**
 * Initialise the registration module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Initialise the registration module
 *
 * @author jojodee <jojodee@xaraya.com>
 * @access public
 * @param none $
 * @returns bool
 */
function registration_init()
{
/** --------------------------------------------------------
 * Set up masks
 */
    xarRegisterMask('ViewRegistration','All','registration','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ViewRegistrationLogin','All','registration','Block','rlogin:Login:All','ACCESS_OVERVIEW');
    xarRegisterMask('EditRegistration','All','registration','All','All','ACCESS_EDIT');
    xarRegisterMask('AdminRegistration','All','registration','All','All','ACCESS_ADMIN');

/** --------------------------------------------------------
 * Set up privileges
 */
    xarRegisterPrivilege('AdminRegistration','All','registration','All','All','ACCESS_ADMIN','Admin the Registration module');
    xarRegisterPrivilege('ViewRegistrationLogin','All','registration','Block','rlogin:Login:All','ACCESS_OVERVIEW','View the User Access block');
    xarRegisterPrivilege('ViewRegistration','All','registration','All','All','ACCESS_OVERVIEW','View the User Access block');

/** --------------------------------------------------------
 * Define modvars
 */
    xarModSetVar('registration', 'allowregistration', true);
    xarModSetVar('registration', 'requirevalidation', true);
    xarModSetVar('registration', 'uniqueemail', true);
    xarModSetVar('registration', 'askwelcomeemail', true); // not in reg atm, leave in roles?
    xarModSetVar('registration', 'askvalidationemail', true); // not in reg atm, leave in roles?
    xarModSetVar('registration', 'askdeactivationemail', true);// not in reg atm, leave in roles?
    xarModSetVar('registration', 'askpendingemail', true); // not in reg atm, leave in roles?
    xarModSetVar('registration', 'askpasswordemail', true);// not in reg atm, leave in roles?
    //xarModSetVar('registration', 'defaultgroup', 'Users'); //Use the Roles modvar
    xarModSetVar('registration', 'minage', 13);

    //we need these too
    xarModSetVar('registration', 'SupportShortURLs', false);
    xarModSetVar('registration', 'showterms', true);
    xarModSetVar('registration', 'showprivacy', true);
    xarModSetVar('registration', 'chooseownpassword', false);
    xarModSetVar('registration', 'notifyemail', xarModGetVar('mail', 'adminmail'));
    xarModSetVar('registration', 'sendnotice', false);
    xarModSetVar('registration', 'explicitapproval', false);
    xarModSetVar('registration', 'showdynamic', false);
    xarModSetVar('registration', 'sendwelcomeemail', false);
    xarModSetVar('registration', 'minpasslength', 5);
    $defaultregmodule= xarModGetVar('roles','defaultregmodule');
    if (!isset($defaultregmodule)) {
        xarModSetVar('roles','defaultregmodule',xarModGetIDFromName('registration'));
    }

/** ---------------------------------------------------------------
 * Set disallowed names
 */
    $names = 'Admin
Root
Linux';
    $disallowednames = serialize($names);
    xarModSetVar('registration', 'disallowednames', $disallowednames);

    $emails = 'none@none.com
president@whitehouse.gov';
    $disallowedemails = serialize($emails);
    xarModSetVar('registration', 'disallowedemails', $disallowedemails);

/** ---------------------------------------------------------------
 * Set disallowed IPs
 */
    $ips = '';
    $disallowedips = serialize($ips);
    xarModSetVar('registration', 'disallowedips', $disallowedips);
   // Register blocks - same as authsystem but has a registration link
    $tid = xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'registration',
                'blockType' => 'rlogin'));
    if (!$tid) return;

    /* This init function brings our module to version 1.2.0, run the upgrades for the rest of the initialisation */
    return registration_upgrade('1.2.0');
}

function registration_activate()
{
    return true;
}

/**
 * Upgrade the registration module from an old version
 *
 * @access public
 * @param oldVersion $
 * @returns bool
 */
function registration_upgrade($oldVersion)
{
    // Upgrade dependent on old version number
    switch ($oldVersion) {
        case '1.0.0':
        //set new vars
        xarModSetVar('registration', 'notifyemail', xarModGetVar('mail', 'adminmail'));

        //delete old vars
        xarModDelVar('registration', 'lockouttime'); // to authsystem
        xarModDelVar('registration', 'lockouttries'); // to authsystem
        xarModDelVar('registration', 'uselockout'); // to authsystem
        $defaultregmodule= xarModGetVar('roles','defaultregmodule');
        if (!isset($defaultregmodule)) {
            xarModSetVar('roles','defaultregmodule',xarModGetIDFromName('registration'));
        }
            break;
        case '1.2.0':
            // Code to upgrade from version 1.2.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * Delete the registration module
 *
 * @access public
 * @param none $
 * @returns bool
 */
function registration_delete()
{
   // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'registration',
                             'blockType'=> 'rlogin'))) return;

    //check if the roles default registration module is set
    //If so - we have to remove the registration value if it's registration module
    $regid=xarModGetIDFromName('registration');
    $defaultregvalue =  xarModGetVar('roles','defaultregmodule');
    if (isset($defaultregmodule) && $defaultregmodule==$regid) {
        xarModSetVar('roles','defaultregmodule',NULL);
    }

    /**
     * Remove modvars, instances, masks and privs
     */
    xarModDelAllVars('registration');
    xarRemoveMasks('registration');
    xarRemoveInstances('registration');
    xarRemovePrivileges('registration');
    // Deletion successful
    return true;
}

?>