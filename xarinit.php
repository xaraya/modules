<?php
/**
 * Otp Module
 *
 * @package modules
 * @subpackage otp
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2017 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 *
 * Initialise or remove the otp module
 *
 */

    sys::import('xaraya.structures.query');

    function otp_init()
    {

    # --------------------------------------------------------
        #
        # Set tables
        #
        $q = new Query();
        $prefix = xarDB::getPrefix();
        
        $query = "DROP TABLE IF EXISTS " . $prefix . "_otp_entries";
        if (!$q->run($query)) {
            return;
        }
        $query = "CREATE TABLE " . $prefix . "_otp_entries (
        id                integer unsigned NOT NULL auto_increment,
        module_id         integer unsigned NOT NULL default 0, 
        type              varchar(64) NOT NULL default '', 
        func              varchar(64) NOT NULL default '', 
        access_key        varchar(64) NOT NULL default '', 
        access            text, 
        owner_id          integer unsigned NOT NULL default 0, 
        timecreated       integer unsigned NOT NULL default 0, 
        timeexpires       integer unsigned NOT NULL default 0, 
        lastseen          integer unsigned NOT NULL default 0, 
        state             tinyint(3) NOT NULL default 3, 
        PRIMARY KEY  (id), 
        KEY i_key (access_key)
    )";
        if (!$q->run($query)) {
            return;
        }
  
        $query = "DROP TABLE IF EXISTS " . $prefix . "_otp_otps";
        if (!$q->run($query)) {
            return;
        }
        $query = "CREATE TABLE " . $prefix . "_otp_otps (
        id                integer unsigned NOT NULL auto_increment,
        user_ident        varchar(64) NOT NULL default '', 
        passphrase        varchar(64) NOT NULL default '', 
        seed              varchar(64) NOT NULL default '', 
        sequence          integer unsigned NOT NULL default 0, 
        algorithm         varchar(10) NOT NULL default '', 
        time_created      integer unsigned NOT NULL default 0, 
        time_starts       integer unsigned NOT NULL default 0, 
        time_expires      integer unsigned NOT NULL default 0, 
        module_id         integer unsigned NOT NULL default 0, 
        reference         integer unsigned NOT NULL default 0,
        PRIMARY KEY  (id), 
        KEY i_tag_module (module_id),
        KEY i_tag_reference (reference)
    )";
        if (!$q->run($query)) {
            return;
        }
    
        $query = "DROP TABLE IF EXISTS " . $prefix . "_otp_used_seeds";
        if (!$q->run($query)) {
            return;
        }
        $query = "CREATE TABLE " . $prefix . "_otp_used_seeds (
        seed              varchar(64) NOT NULL default '',
        PRIMARY KEY  (seed) 
    )";
        if (!$q->run($query)) {
            return;
        }
  
        # --------------------------------------------------------
        #
        # Set up masks
        #
        xarRegisterMask('ViewOtp', 'All', 'otp', 'All', 'All', 'ACCESS_OVERVIEW');
        xarRegisterMask('ReadOtp', 'All', 'otp', 'All', 'All', 'ACCESS_READ');
        xarRegisterMask('CommentOtp', 'All', 'otp', 'All', 'All', 'ACCESS_COMMENT');
        xarRegisterMask('ModerateOtp', 'All', 'otp', 'All', 'All', 'ACCESS_MODERATE');
        xarRegisterMask('EditOtp', 'All', 'otp', 'All', 'All', 'ACCESS_EDIT');
        xarRegisterMask('AddOtp', 'All', 'otp', 'All', 'All', 'ACCESS_ADD');
        xarRegisterMask('ManageOtp', 'All', 'otp', 'All', 'All', 'ACCESS_DELETE');
        xarRegisterMask('AdminOtp', 'All', 'otp', 'All', 'All', 'ACCESS_ADMIN');

        # --------------------------------------------------------
        #
        # Set up privileges
        #
        xarRegisterPrivilege('ViewOtp', 'All', 'otp', 'All', 'All', 'ACCESS_OVERVIEW');
        xarRegisterPrivilege('ReadOtp', 'All', 'otp', 'All', 'All', 'ACCESS_READ');
        xarRegisterPrivilege('CommentOtp', 'All', 'otp', 'All', 'All', 'ACCESS_COMMENT');
        xarRegisterPrivilege('ModerateOtp', 'All', 'otp', 'All', 'All', 'ACCESS_MODERATE');
        xarRegisterPrivilege('EditOtp', 'All', 'otp', 'All', 'All', 'ACCESS_EDIT');
        xarRegisterPrivilege('AddOtp', 'All', 'otp', 'All', 'All', 'ACCESS_ADD');
        xarRegisterPrivilege('ManageOtp', 'All', 'otp', 'All', 'All', 'ACCESS_DELETE');
        xarRegisterPrivilege('AdminOtp', 'All', 'otp', 'All', 'All', 'ACCESS_ADMIN');

        # --------------------------------------------------------
        #
        # Create DD objects
        #
        $module = 'otp';
        $objects = array(
                        'otp_entries',
                         );

        if (!xarModAPIFunc('modules', 'admin', 'standardinstall', array('module' => $module, 'objects' => $objects))) {
            return;
        }

        # --------------------------------------------------------
        #
        # Set up modvars
        #
        $module_settings = xarMod::apiFunc('base', 'admin', 'getmodulesettings', array('module' => 'otp'));
        $module_settings->initialize();

        // Add variables like this next one when creating utility modules
        // This variable is referenced in the xaradmin/modifyconfig-utility.php file
        // This variable is referenced in the xartemplates/includes/defaults.xd file
        xarModVars::set('otp', 'defaultmastertable', 'otp_otp');
        xarModVars::set('otp', 'sequence', 100);
        xarModVars::set('otp', 'algorithm', 'md5');
        xarModVars::set('otp', 'expires', 3600 * 24 * 3);

        # --------------------------------------------------------
        #
        # Set up hooks
        #

        return true;
    }

    function otp_upgrade()
    {
        return true;
    }

    function otp_delete()
    {
        $this_module = 'otp';
        return xarModAPIFunc('modules', 'admin', 'standarddeinstall', array('module' => $this_module));
    }
