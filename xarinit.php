<?php
/**
 * Mailer Module
 *
 * @package modules
 * @subpackage mailer module
 * @copyright (C) 2010 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 *
 * Initialise or remove the mailer module
 *
 */

    sys::import('xaraya.structures.query');

    function mailer_init()
    {

    # --------------------------------------------------------
    #
    # Set tables
    #
        $q = new Query();
        $prefix = xarDB::getPrefix();
        
        $query = "DROP TABLE IF EXISTS " . $prefix . "_mailer_mails";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_mailer_mails (
            id                integer unsigned NOT NULL auto_increment,
            name              varchar(64) default '' NOT NULL,
            description       text,
            sendername        varchar(254) default '' NOT NULL,
            senderaddress     varchar(254) default '' NOT NULL,
            subject           text,
            header_id         integer unsigned NOT NULL,
            body              text,
            footer_id         integer unsigned NOT NULL,
            locale            varchar(64) default '' NOT NULL,
            timecreated       integer unsigned NOT NULL default '0', 
            timemodified      integer unsigned NOT NULL default '0', 
            role_id           integer unsigned NOT NULL default '0', 
            redirect          tinyint NOT NULL default '0', 
            redirectaddress   varchar(254) default '' NOT NULL,
            alias             tinyint NOT NULL default '1', 
            type              tinyint NOT NULL default '3', 
            module_id         integer unsigned NOT NULL default '0', 
            state             tinyint NOT NULL default '3', 
            header_x_mailer   varchar(254) default '' NOT NULL, 
            realm_id          integer(64) NOT NULL default '0', 
            PRIMARY KEY  (id) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_mailer_headers";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_mailer_headers (
            id                integer unsigned NOT NULL auto_increment,
            name              varchar(64) default '' NOT NULL,
            body              text,
            timecreated       integer unsigned NOT NULL default '0', 
            PRIMARY KEY  (id) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_mailer_footers";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_mailer_footers (
            id                integer unsigned NOT NULL auto_increment,
            name              varchar(64) default '' NOT NULL,
            body              text,
            timecreated       integer unsigned NOT NULL default '0', 
            PRIMARY KEY  (id) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_mailer_history";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_mailer_history (
            id                integer unsigned NOT NULL auto_increment,
            mail_id           integer unsigned NOT NULL,
            module_id         integer unsigned NOT NULL,
            sendername        varchar(254) default '' NOT NULL,
            senderaddress     varchar(254) default '' NOT NULL,
            recipientname     varchar(254) default '' NOT NULL,
            recipientaddress  varchar(254) default '' NOT NULL,
            subject           text,
            body              text,
            timecreated       integer unsigned NOT NULL default '0', 
            PRIMARY KEY  (id) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

    # --------------------------------------------------------
    #
    # Set up masks
    #
        xarRegisterMask('ViewMailer','All','mailer','All','All','ACCESS_OVERVIEW');
        xarRegisterMask('ReadMailer','All','mailer','All','All','ACCESS_READ');
        xarRegisterMask('CommentMailer','All','mailer','All','All','ACCESS_COMMENT');
        xarRegisterMask('ModerateMailer','All','mailer','All','All','ACCESS_MODERATE');
        xarRegisterMask('EditMailer','All','mailer','All','All','ACCESS_EDIT');
        xarRegisterMask('AddMailer','All','mailer','All','All','ACCESS_ADD');
        xarRegisterMask('ManageMailer','All','mailer','All','All','ACCESS_DELETE');
        xarRegisterMask('AdminMailer','All','mailer','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up privileges
    #
        xarRegisterPrivilege('ViewMailer','All','mailer','All','All','ACCESS_OVERVIEW');
        xarRegisterPrivilege('ReadMailer','All','mailer','All','All','ACCESS_READ');
        xarRegisterPrivilege('CommentMailer','All','mailer','All','All','ACCESS_COMMENT');
        xarRegisterPrivilege('ModerateMailer','All','mailer','All','All','ACCESS_MODERATE');
        xarRegisterPrivilege('EditMailer','All','mailer','All','All','ACCESS_EDIT');
        xarRegisterPrivilege('AddMailer','All','mailer','All','All','ACCESS_ADD');
        xarRegisterPrivilege('ManageMailer','All','mailer','All','All','ACCESS_DELETE');
        xarRegisterPrivilege('AdminMailer','All','mailer','All','All','ACCESS_ADMIN');

    # --------------------------------------------------------
    #
    # Set up modvars
    #
        xarModVars::set('mailer', 'items_per_page', 20);
        xarModVars::set('mailer', 'useModuleAlias',0);
        xarModVars::set('mailer', 'aliasname','Mailer');
        xarModVars::set('mailer', 'defaultmastertable','mailer_mails');

        xarModVars::set('mailer', 'defaultrecipientname', xarML('Occupant'));
        xarModVars::set('mailer', 'defaultsendername', xarModVars::get('mail','adminname'));
        xarModVars::set('mailer', 'defaultsenderaddress', xarModVars::get('mail','adminmail'));
        xarModVars::set('mailer', 'defaultuserobject', 'roles_users');
        xarModVars::set('mailer', 'defaultmailobject', 'mailer_mails');
        xarModVars::set('mailer', 'defaultlocale', 'en_US.utf-8');
        xarModVars::set('mailer', 'defaultredirect', 0);
        xarModVars::set('mailer', 'defaultredirectaddress', xarModVars::get('mail','adminmail'));
        xarModVars::set('mailer', 'defaultheader_x_mailer', "NetspanMailer [version 2.00]");
    # --------------------------------------------------------
    #
    # Create DD objects
    #
        $module = 'mailer';
        $objects = array(
                         'mailer_mails',
                         'mailer_headers',
                         'mailer_footers',
                         'mailer_history',
                         );

        if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    # --------------------------------------------------------
    #
    # Set up hooks
    #

        return true;
    }

    function mailer_upgrade($oldVersion)
    {
       // Upgrade dependent on old version number
        switch($oldVersion) {
            case '1.0.0': 
                    
                    # --------------------------------------------------------
                    #
                    # Alter table xar_mailer_mails to add column header_x_mailer
                    #
                    $q = new Query();
                    $prefix = xarDB::getPrefix();
                    
                    $query = "ALTER TABLE ". $prefix."_mailer_mails ADD header_x_mailer VARCHAR(254) NULL";
                    if (!$q->run($query)) return;
                    
                    # --------------------------------------------------------
                    #
                    # Set up modvars
                    #
                    xarModVars::set('mailer', 'defaultheader_x_mailer', "NetspanMailer [version 2.00]");
                    
                    # --------------------------------------------------------
                    #
                    # Add the missing DD property header_x_mailer in mailer_mails
                    #
                    sys::import('modules.dynamicdata.class.objects.master');
                    
                    $modid = xarMod::getId('mailer');
            
                    $objectinfo = DataObjectMaster::getObjectInfo(array('moduleid' => $modid, 'name' => 'mailer_mails'));
                    if (!isset($objectinfo) || empty($objectinfo['objectid'])) return;
            
                    $objectid = $objectinfo['objectid'];
                    
                    $propertyid = xarMod::apiFunc('dynamicdata','admin','createproperty', array('name'         => 'header_x_mailer',
                                                                                                'label'        => 'Header X_Mailer',
                                                                                                'objectid'     => $objectid,
                                                                                                'type'         => 2,
                                                                                                'defaultvalue' => '',
                                                                                                'source'       => 'xar_mailer_mails.header_x_mailer',
                                                                                                'status'       => 33,
                                                                                                'seq'          => 20));
                    if (empty($propertyid)) return;
                    // success
                    // fall through to next upgrade

            case '1.0.1':
                    
                    # --------------------------------------------------------
                    #
                    # Alter table xar_mailer_mails to add column realm_id
                    #
                    $q = new Query();
                    $prefix = xarDB::getPrefix();
                    $query = "ALTER TABLE ". $prefix."_mailer_mails ADD realm_id INT( 11 ) NOT NULL DEFAULT 0";
                    if (!$q->run($query)) return;
                    
                    # --------------------------------------------------------
                    # Add the missing DD property realm_id in mailer_mails
                    #
                    sys::import('modules.dynamicdata.class.objects.master');
                    
                    $modid = xarMod::getId('mailer');
            
                    $objectinfo = DataObjectMaster::getObjectInfo(array('moduleid' => $modid, 'name' => 'mailer_mails'));
                    if (!isset($objectinfo) || empty($objectinfo['objectid'])) return;
            
                    $objectid = $objectinfo['objectid'];
                    
                    $propertyid = xarMod::apiFunc('dynamicdata','admin','createproperty', array('name'         => 'realm_id',
                                                                                                'label'        => 'Realm_ID',
                                                                                                'objectid'     => $objectid,
                                                                                                'type'         => 30096,
                                                                                                'defaultvalue' => 0,
                                                                                                'source'       => 'xar_mailer_mails.realm_id',
                                                                                                'status'       => 67,
                                                                                                'seq'          => 21));
                    if (empty($propertyid)) return;
                    // success
                    // fall through to next upgrade

            case '1.0.2':
                $q = new Query();
                $prefix = xarDB::getPrefix();
                $module='"mailer"';
                $configuration = 'a:6:{s:12:"display_rows";s:1:"0";s:14:"display_layout";s:7:"default";s:24:"initialization_refobject";s:7:"modules";s:25:"initialization_store_prop";s:5:"regid";s:27:"initialization_display_prop";s:4:"name";s:22:"initialization_options";s:15:"0,Choose Module";}';
                $query = "UPDATE `".$prefix."_dynamic_properties` SET `defaultvalue` = 'xarMod::getRegId($module)', `configuration` = '$configuration' WHERE `source` = '".$prefix."_mailer_mails.module_id'";
                if (!$q->run($query)) return;

                $query = "UPDATE `".$prefix."_mailer_mails` SET `module_id` = (SELECT `regid` from `xar_modules` WHERE `".$prefix."_mailer_mails`.`module_id` = `".$prefix."_modules`.`id`);";
                if (!$q->run($query)) return;

            case '1.0.3':
                // fall through to next upgrade

            default:
                break;
        }
        return true;
    }

    function mailer_delete()
    {
        $this_module = 'mailer';
        return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => $this_module));
    }

?>