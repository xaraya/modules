<?php
/**
 * Initialise the registration module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @author Jan Schrage, John Cox, Gregor Rothfuss
 */

/**
 * Initialise the registration module
 *
 * @access public
 * @param none $
 * @returns bool
 */
function registration_init()
{
# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewRegistration','All','registration','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ViewRegistrationLogin','All','registration','Block','rlogin:Login:All','ACCESS_OVERVIEW');
    xarRegisterMask('EditRegistration','All','registration','All','All','ACCESS_EDIT');
    xarRegisterMask('AdminRegistration','All','registration','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('AdminRegistration','All','registration','All','All','ACCESS_ADMIN');
    xarRegisterPrivilege('ViewRegistrationLogin','All','registration','Block','rlogin:Login:All','ACCESS_OVERVIEW','View the User Access block');
    xarRegisterPrivilege('ViewRegistration','All','registration','All','All','ACCESS_OVERVIEW','View the User Access block');

# --------------------------------------------------------
#
# Define modvars
#
    xarModSetVar('registration', 'allowregistration', 1);
    xarModSetVar('registration', 'requirevalidation', 1);
    xarModSetVar('registration', 'uniqueemail', 1);
    xarModSetVar('registration', 'askwelcomeemail', 1);
    xarModSetVar('registration', 'askvalidationemail', 1);
    xarModSetVar('registration', 'askdeactivationemail', 1);
    xarModSetVar('registration', 'askpendingemail', 1);
    xarModSetVar('registration', 'askpasswordemail', 1);
    //xarModSetVar('registration', 'defaultgroup', 'Users'); //Use the Roles modvar
	//xarModSetVar('registration', 'lockouttime', 15); // to authsystem
	//xarModSetVar('registration', 'lockouttries', 3); // to authsystem
    xarModSetVar('registration', 'minage', 13);

/*---------------------------------------------------------------
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

/*---------------------------------------------------------------
* Set disallowed IPs
*/
    $ips = '';
    $disallowedips = serialize($ips);
    xarModSetVar('registration', 'disallowedips', $disallowedips);
   //Let's check for an authsystem login block
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();

    $sitePrefix = xarDBGetSiteTablePrefix();
    $blocktypeTable = $sitePrefix .'_block_types';
    $blockinstanceTable = $sitePrefix .'_block_instances';
        $query = "SELECT xar_id,
                         xar_type,
                         xar_module
                         FROM $blocktypeTable
                 WHERE xar_type='login' and xar_module='authsystem'";
        $result =& $dbconn->Execute($query);
        if (!$result) return;
        list($blocktypeid,$blocktype,$module)= $result->fields;
        $blocktype = array('id' => $blocktypeid,
                           'blocktype' => $blocktype,
                           'module'=> $module);

       if (is_array($blocktype) && $blocktype['module']=='authsystem') {
       $blocktypeid=$blocktype['id'];
       //Find the block instance
       $query = "SELECT xar_id
                         FROM $blockinstanceTable
                 WHERE xar_type_id=?";
        $result =& $dbconn->Execute($query,array($blocktypeid));
        list($blockid)= $result->fields;
        if (isset($blockid)) {
           // remove this login block type and block from authsystem
    	    $result = xarModAPIfunc('blocks', 'admin', 'delete_instance', array('bid'=>$blockid));
        }
       }

    // Register blocks
    $tid = xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'registration',
                'blockType' => 'rlogin'));
    if (!$tid) return;


    if (!xarModAPIFunc('blocks', 'user', 'get', array('name'  => 'rlogin'))) {
        $rightgroup = xarModAPIFunc('blocks', 'user', 'getgroup', array('name'=> 'right'));
        if (!xarModAPIFunc('blocks', 'admin', 'create_instance',
                           array('title'    => 'User Access',
                                 'name'     => 'rlogin',
                                 'type'     => $tid,
                                 'groups'    => array($rightgroup),
                                 'template' => '',
                                 'state'    => 2))) {
            return;
        }
    }

    return true;
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
            break;
        case '1.2.0':
            // Code to upgrade from version 2.0 goes here
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
# --------------------------------------------------------
#
# Delete block details for this module (for now)
#
    $blocktypes = xarModAPIfunc(
        'blocks', 'user', 'getallblocktypes',
        array('module' => 'registration')
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
    xarModDelAllVars('registration');
    xarRemoveMasks('registration');
    xarRemoveInstances('registration');

    // Deletion successful
    return true;
}

?>
