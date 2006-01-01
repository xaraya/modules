<?php
/**
 * Initialise the authentication module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authentication module
 * @author Jan Schrage, John Cox, Gregor Rothfuss
 */

/**
 * Initialise the authentication module
 *
 * @access public
 * @param none $
 * @returns bool
 */
function authentication_init()
{
# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewAuthentication','All','vendors','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('EditAuthentication','All','vendors','All','All','ACCESS_EDIT');
    xarRegisterMask('AdminAuthentication','All','vendors','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('AdminAuthentication','All','vendors','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Define modvars
#
    xarModSetVar('authentication', 'allowregistration', 1);
    xarModSetVar('authentication', 'requirevalidation', 1);
    xarModSetVar('authentication', 'itemsperpage', 20);
    xarModSetVar('authentication', 'uniqueemail', 1);
    xarModSetVar('authentication', 'askwelcomeemail', 1);
    xarModSetVar('authentication', 'askvalidationemail', 1);
    xarModSetVar('authentication', 'askdeactivationemail', 1);
    xarModSetVar('authentication', 'askpendingemail', 1);
    xarModSetVar('authentication', 'askpasswordemail', 1);
    xarModSetVar('authentication', 'defaultgroup', 'Users');
	xarModSetVar('authentication', 'lockouttime', 15);
	xarModSetVar('authentication', 'lockouttries', 3);
    xarModSetVar('authentication', 'minage', 13);

/*---------------------------------------------------------------
* Set disallowed names
*/
    $names = 'Admin
Root
Linux';
    $disallowednames = serialize($names);
    xarModSetVar('authentication', 'disallowednames', $disallowednames);

    $emails = 'none@none.com
president@whitehouse.gov';
    $disallowedemails = serialize($emails);
    xarModSetVar('authentication', 'disallowedemails', $disallowedemails);

/*---------------------------------------------------------------
* Set disallowed IPs
*/
    $ips = '';
    $disallowedips = serialize($ips);
    xarModSetVar('authentication', 'disallowedips', $disallowedips);

    // Register blocks
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'authentication',
                'blockType' => 'login'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'authentication',
                'blockType' => 'online'))) return;

    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'authentication',
                'blockType' => 'user'))) return;

    return true;
}

function authentication_activate()
{
    return true;
}

/**
 * Upgrade the authentication module from an old version
 *
 * @access public
 * @param oldVersion $
 * @returns bool
 */
function authentication_upgrade($oldVersion)
{
    // Upgrade dependent on old version number
    switch ($oldVersion) {
        case 1.01:
            break;
        case 2.0:
            // Code to upgrade from version 2.0 goes here
            break;
    }
    // Update successful
    return true;
}

/**
 * Delete the authentication module
 *
 * @access public
 * @param none $
 * @returns bool
 */
function authentication_delete()
{
    /**
     * Remove modvars, instances and masks
     */
    xarModDelAllVars('authentication');
    xarRemoveMasks('authentication');
    xarRemoveInstances('authentication');

    // Deletion successful
    return true;
}

?>
