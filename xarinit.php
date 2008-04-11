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
    xarRegisterMask('ReadRegistration','All','registration','All','All','ACCESS_READ');
    xarRegisterMask('ViewRegistrationLogin','All','registration','BlockItem','All','ACCESS_OVERVIEW');
    xarRegisterMask('EditRegistration','All','registration','All','All','ACCESS_EDIT');
    xarRegisterMask('AdminRegistration','All','registration','All','All','ACCESS_ADMIN');

/** --------------------------------------------------------
 * Set up privileges
 */
    xarRegisterPrivilege('AdminRegistration','All','registration','All','All','ACCESS_ADMIN','Admin the Registration module');
    xarRegisterPrivilege('ViewRegistrationLogin','All','registration','BlockItem','All','ACCESS_OVERVIEW','View the User Access block');
    xarRegisterPrivilege('ViewRegistration','All','registration','All','All','ACCESS_OVERVIEW','View access to the registration module');
    xarRegisterPrivilege('ReadRegistration','All','registration','All','All','ACCESS_READ','Read access to the registration module');

# --------------------------------------------------------
#
# Create DD objects
#
    $module = 'registration';
    $objects = array(
                   'registration_users',
                     );

    if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

/** --------------------------------------------------------
 * Define modvars
 */
    xarModVars::set('registration', 'allowregistration', true);
    xarModVars::set('registration', 'requirevalidation', false);
    xarModVars::set('registration', 'uniqueemail', true); //move back to roles - better there
    xarModVars::set('registration', 'askwelcomeemail', true);
    xarModVars::set('registration', 'askvalidationemail', true); // not in reg atm, leave in roles?
    xarModVars::set('registration', 'askdeactivationemail', true);// not in reg atm, leave in roles?
    xarModVars::set('registration', 'askpendingemail', true); // not in reg atm, leave in roles?
    xarModVars::set('registration', 'askpasswordemail', true);// not in reg atm, leave in roles?
    xarModVars::set('registration', 'minage', 13);

    //we need these too
    xarModVars::set('registration', 'SupportShortURLs', false);
    xarModVars::set('registration', 'showterms', true);
    xarModVars::set('registration', 'showprivacy', true);
    xarModVars::set('registration', 'chooseownpassword', false);
    xarModVars::set('registration', 'notifyemail', xarModVars::get('mail', 'adminmail'));
    xarModVars::set('registration', 'sendnotice', false);
    xarModVars::set('registration', 'explicitapproval', true);
    xarModVars::set('registration', 'showdynamic', false);
    xarModVars::set('registration', 'sendwelcomeemail', false);

    // Make the default group of this module that of Roles for starters
    $defaultgroup = xarModVars::get('roles','defaultgroup');
    xarModVars::set('registration','defaultgroup',$defaultgroup);

    // If Roles has no default registrtion module, make this it
    $defaultregmodule = xarModVars::get('roles','defaultregmodule');
    if (empty($defaultregmodule)) {
        xarModVars::set('roles','defaultregmodule','registration');
    }

    xarModVars::set('registration','defaultuserstate',xarRoles::ROLES_STATE_ACTIVE);

    $regobject = DataObjectMaster::getObjectInfo(array('name' => 'registration_users'));
    xarModVars::set('registration', 'registrationobject', $regobject['objectid']);

/** ---------------------------------------------------------------
 * Set disallowed names
 */
    $names = 'Admin
Root
Linux';
    $disallowednames = serialize($names);
    xarModVars::set('registration', 'disallowednames', $disallowednames);

/* This really has to be in roles as a user can modify their email after registration
    $emails = 'none@none.com
president@whitehouse.gov';
    $disallowedemails = serialize($emails);
    xarModVars::set('registration', 'disallowedemails', $disallowedemails);
*/
/** ---------------------------------------------------------------
 * Set disallowed IPs
 */
    $ips = '';
    $disallowedips = serialize($ips);
    xarModVars::set('registration', 'disallowedips', $disallowedips);
   // Register blocks - same as authsystem but has a registration link
    $tid = xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'registration',
                'blockType' => 'rlogin'));
    if (!$tid) return;

    /* This init function brings our module to version 1.0.0, run the upgrades for the rest of the initialisation */
    return registration_upgrade('1.0.1');
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
        case '1.0.2': //current version

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
    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type',
                       array('modName'  => 'registration',
                             'blockType'=> 'rlogin'))) return;

    //check if the roles default registration module is set
    //If so - we have to remove the registration value if it's registration module
    $defaultregvalue = xarModVars::get('roles','defaultregmodule');
    if ($defaultregvalue == 'registration') {
        xarModVars::set('roles','defaultregmodule','');
    }

    $module = 'registration';
    return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => $module));
}

?>