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
    xarRegisterMask('ViewAuthentication','All','authentication','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('EditAuthentication','All','authentication','All','All','ACCESS_EDIT');
    xarRegisterMask('AdminAuthentication','All','authentication','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('AdminAuthentication','All','authentication','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Define modvars
#
    xarModSetVar('authentication', 'allowregistration', 1);
    xarModSetVar('authentication', 'requirevalidation', 1);
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
    $tid = xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'authentication',
                'blockType' => 'login'));
    if (!$tid) return;


    if (!xarModAPIFunc('blocks', 'user', 'get', array('name'  => 'login'))) {
        $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
        if (!xarModAPIFunc('blocks', 'admin', 'create_instance',
                           array('title'    => 'Login',
                                 'name'     => 'login',
                                 'type'     => $tid,
                                 'groups'    => array($rightgroup),
                                 'template' => '',
                                 'state'    => 2))) {
            return;
        }
    }

    // Make this the default authentication module
    xarModSetVar('roles', 'defaultauthmodule', xarModGetIDFromName('authentication'));

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
# --------------------------------------------------------
#
# Delete block details for this module (for now)
#
    $blocktypes = xarModAPIfunc(
        'blocks', 'user', 'getallblocktypes',
        array('module' => 'authentication')
    );

    // Delete block types.
    if (is_array($blocktypes) && !empty($blocktypes)) {
        foreach($blocktypes as $blocktype) {
            $result = xarModAPIfunc(
                'blocks', 'admin', 'delete_type', $blocktype
            );
        }
    }

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
