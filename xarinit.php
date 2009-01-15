<?php
/**
 *
 * Initialise or remove the mailer module
 *
 */

    sys::import('modules.query.class.query');

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
            sendername        varchar(254) default '' NOT NULL,
            senderaddress     varchar(254) default '' NOT NULL,
            subject           text,
            body              text,
            footer_id         integer unsigned NOT NULL,
            locale            varchar(64) default '' NOT NULL,
            timecreated       int(11) unsigned NOT NULL default '0', 
            timemodified      int(11) unsigned NOT NULL default '0', 
            role_id           int(11) unsigned NOT NULL default '0', 
            redirect          tinyint NOT NULL default '0', 
            redirectaddress   varchar(254) default '' NOT NULL,
            alias             tinyint NOT NULL default '1', 
            type              tinyint NOT NULL default '3', 
            state             tinyint NOT NULL default '3', 
            PRIMARY KEY  (id) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_mailer_footers";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_mailer_footers (
            id                integer unsigned NOT NULL auto_increment,
            name              varchar(64) default '' NOT NULL,
            body              text,
            timecreated       int(11) unsigned NOT NULL default '0', 
            PRIMARY KEY  (id) 
        ) TYPE=MyISAM";
        if (!$q->run($query)) return;

        $query = "DROP TABLE IF EXISTS " . $prefix . "_mailer_history";
        if (!$q->run($query)) return;
        $query = "CREATE TABLE " . $prefix . "_mailer_history (
            id                integer unsigned NOT NULL auto_increment,
            mail_id           integer unsigned NOT NULL,
            sendername        varchar(254) default '' NOT NULL,
            senderaddress     varchar(254) default '' NOT NULL,
            recipientname     varchar(254) default '' NOT NULL,
            recipientaddress  varchar(254) default '' NOT NULL,
            subject           text,
            body              text,
            timecreated       int(11) unsigned NOT NULL default '0', 
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
        xarModVars::set('mailer', 'itemsperpage', 20);
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

    # --------------------------------------------------------
    #
    # Create DD objects
    #
        $module = 'mailer';
        $objects = array(
                         'mailer_mails',
                         'mailer_footers',
                         'mailer_history',
                         );

        if(!xarModAPIFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

    # --------------------------------------------------------
    #
    # Set up hooks
    #
        // This is a GUI hook for the roles module that enhances the roles profile page
        if (!xarModRegisterHook('item', 'usermenu', 'GUI',
                'mailer', 'user', 'usermenu')) {
            return false;
        }

        xarModAPIFunc('modules', 'admin', 'enablehooks',
            array('callerModName' => 'mailer', 'hookModName' => 'mailer'));

        return true;
    }

    function mailer_upgrade()
    {
        return true;
    }

    function mailer_delete()
    {
        $this_module = 'mailer';
        return xarModAPIFunc('modules','admin','standarddeinstall',array('module' => $this_module));
    }

?>
