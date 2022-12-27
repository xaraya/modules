<?php
/**
 * Reminders Module
 *
 * @package modules
 * @subpackage reminders
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 *
 * Initialise or remove the reminders module
 *
 */

sys::import('xaraya.structures.query');

function reminders_init()
{
    # --------------------------------------------------------
#
    # Set tables
#
    $q = new Query();
    $prefix = xarDB::getPrefix();

    $query = "DROP TABLE IF EXISTS " . $prefix . "_reminders_entries";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_reminders_entries (
        id                  integer unsigned NOT NULL auto_increment,
        name                varchar(255) NOT NULL default '', 
        message             text, 
        email_id_1          integer unsigned NOT NULL default 0, 
        email_id_2          integer unsigned NOT NULL default 0, 
        template_id         integer unsigned NOT NULL default 0, 
        due_date            integer unsigned NOT NULL default 0, 
        recurring           tinyint unsigned NOT NULL default 0, 
        recur_period        tinyint unsigned NOT NULL default 0, 
        reminder_warning_1  integer unsigned NOT NULL default 0, 
        reminder_done_1     tinyint unsigned NOT NULL default 0, 
        reminder_warning_2  integer unsigned NOT NULL default 0, 
        reminder_done_2     tinyint unsigned NOT NULL default 0, 
        reminder_warning_3  integer unsigned NOT NULL default 0, 
        reminder_done_3     tinyint unsigned NOT NULL default 0, 
        reminder_warning_4  integer unsigned NOT NULL default 0, 
        reminder_done_4     tinyint unsigned NOT NULL default 0, 
        reminder_warning_5  integer unsigned NOT NULL default 0, 
        reminder_done_5     tinyint unsigned NOT NULL default 0, 
        reminder_warning_6  integer unsigned NOT NULL default 0, 
        reminder_done_6     tinyint unsigned NOT NULL default 0, 
        reminder_warning_7  integer unsigned NOT NULL default 0, 
        reminder_done_7     tinyint unsigned NOT NULL default 0, 
        reminder_warning_8  integer unsigned NOT NULL default 0, 
        reminder_done_8     tinyint unsigned NOT NULL default 0, 
        reminder_warning_9  integer unsigned NOT NULL default 0, 
        reminder_done_9     tinyint unsigned NOT NULL default 0, 
        reminder_warning_10 integer unsigned NOT NULL default 0, 
        reminder_done_10    tinyint unsigned NOT NULL default 0, 
        timecreated         integer unsigned NOT NULL default 0, 
        timemodified        integer unsigned NOT NULL default 0, 
        state               tinyint(3) NOT NULL default 3, 
        PRIMARY KEY  (id) 
    )";
    if (!$q->run($query)) {
        return;
    }

    $query = "DROP TABLE IF EXISTS " . $prefix . "_reminders_emails";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_reminders_emails (
        id                integer unsigned NOT NULL auto_increment,
        name              varchar(255) NOT NULL default '', 
        address           varchar(255) NOT NULL default '', 
        subject           varchar(255) NOT NULL default '', 
        message           varchar(255) NOT NULL default '', 
        do_lookups        tinyint unsigned NOT NULL default 0, 
        lookup_interval   integer unsigned NOT NULL default 0, 
        lookup_template   integer unsigned NOT NULL default 0, 
        timecreated       integer unsigned NOT NULL default 0, 
        timemodified      integer unsigned NOT NULL default 0, 
        state             tinyint(3) NOT NULL default 3, 
        PRIMARY KEY  (id)
    )";
    if (!$q->run($query)) {
        return;
    }

    $query = "DROP TABLE IF EXISTS " . $prefix . "_reminders_history";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_reminders_history (
        id                integer unsigned NOT NULL auto_increment,
        entry_id          integer unsigned NOT NULL default 0, 
        message           varchar(255) NOT NULL default '', 
        address           varchar(255) NOT NULL default '', 
        due_date          integer unsigned NOT NULL default 0, 
        timecreated       integer unsigned NOT NULL default 0, 
        PRIMARY KEY  (id)
    )";
    if (!$q->run($query)) {
        return;
    }

    $query = "DROP TABLE IF EXISTS " . $prefix . "_reminders_lookups";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_reminders_lookups (
        id                integer unsigned NOT NULL auto_increment,
        owner             integer unsigned NOT NULL default 0, 
        name              varchar(255) NOT NULL default '',  
        message           varchar(255) NOT NULL default '', 
        email_id_1        integer unsigned NOT NULL default 0, 
        email_id_2        integer unsigned NOT NULL default 0, 
        template_id       integer unsigned NOT NULL default 0, 
        phone             varchar(255) NOT NULL default '', 
        email             varchar(255) NOT NULL default '', 
        last_lookup       integer unsigned NOT NULL default 0, 
        timecreated       integer unsigned NOT NULL default 0, 
        timemodified      integer unsigned NOT NULL default 0, 
        PRIMARY KEY  (id)
    )";
    if (!$q->run($query)) {
        return;
    }

    $query = "DROP TABLE IF EXISTS " . $prefix . "_reminders_lookup_history";
    if (!$q->run($query)) {
        return;
    }
    $query = "CREATE TABLE " . $prefix . "_reminders_lookup_history (
        id                integer unsigned NOT NULL auto_increment,
        lookup            integer unsigned NOT NULL default 0, 
        owner             integer unsigned NOT NULL default 0, 
        date              integer unsigned NOT NULL default 0, 
        subject           varchar(255) NOT NULL default '',  
        message           text, 
        promised          tinyint(1) unsigned NOT NULL default 0, 
        timecreated       integer unsigned NOT NULL default 0, 
        PRIMARY KEY  (id)
    )";
    if (!$q->run($query)) {
        return;
    }

    # --------------------------------------------------------
#
    # Set up masks
#
    xarMasks::register('ViewReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_OVERVIEW');
    xarMasks::register('ReadReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_READ');
    xarMasks::register('CommentReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_COMMENT');
    xarMasks::register('ModerateReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_MODERATE');
    xarMasks::register('EditReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_EDIT');
    xarMasks::register('AddReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_ADD');
    xarMasks::register('ManageReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_DELETE');
    xarMasks::register('AdminReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_ADMIN');

    # --------------------------------------------------------
#
    # Set up privileges
#
    xarPrivileges::register('ViewReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_OVERVIEW');
    xarPrivileges::register('ReadReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_READ');
    xarPrivileges::register('CommentReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_COMMENT');
    xarPrivileges::register('ModerateReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_MODERATE');
    xarPrivileges::register('EditReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_EDIT');
    xarPrivileges::register('AddReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_ADD');
    xarPrivileges::register('ManageReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_DELETE');
    xarPrivileges::register('AdminReminders', 'All', 'reminders', 'All', 'All', 'ACCESS_ADMIN');

    # --------------------------------------------------------
#
    # Create DD objects
#
    $module = 'reminders';
    $objects = [
                     'reminders_emails',
                     'reminders_entries',
                     'reminders_history',
                     'reminders_lookups',
                     'reminders_lookup_history',
                     ];

    if (!xarMod::apiFunc('modules', 'admin', 'standardinstall', ['module' => $module, 'objects' => $objects])) {
        return;
    }

    # --------------------------------------------------------
#
    # Set up modvars
#
    $module_settings = xarMod::apiFunc('base', 'admin', 'getmodulesettings', ['module' => 'reminders']);
    $module_settings->initialize();

    // Add variables like this next one when creating utility modules
    // This variable is referenced in the xaradmin/modifyconfig-utility.php file
    // This variable is referenced in the xartemplates/includes/defaults.xd file
    xarModVars::set('reminders', 'defaultmastertable', 'reminders_history');
    xarModVars::set('reminders', 'debugmode', false);
    xarModVars::set('reminders', 'save_history', false);
    xarModVars::set('reminders', 'subject', "Touching base");
    xarModVars::set('reminders', 'message', 's:110:"<p>Hello #$first_name#,<br />
<br />
I wanted to get in touch.</p>

<p>Best,<br />
#$my_first_name#</p>
";');
    xarModVars::set('reminders', 'lookup_template', 20); // TODO: get rid of this hard coding

    # --------------------------------------------------------
#
    # Default data from other modules
#
    // Add basic mailer templates
    if (xarMod::isAvailable('mailer')) {
        $dat_file = sys::code() . 'modules/' . $module . '/xardata/'.'mailer_templates-dat.xml';
        if (file_exists($dat_file)) {
            $data['file'] = $dat_file;
            $objectid = xarMod::apiFunc('dynamicdata', 'util', 'import', $data);
        }
    }
    # --------------------------------------------------------
#
    # Set up hooks
#

    return true;
}

function reminders_upgrade()
{
    return true;
}

function reminders_delete()
{
    $this_module = 'reminders';

    # --------------------------------------------------------
#
    # Remove data from other modules
#
    // Remove mailer templates
    if (xarMod::isAvailable('mailer')) {
        xarMod::load('mailer');
        $tables = xarDB::getTables();
        $q = new Query('DELETE', $tables['mailer_mails']);
        $q->eq('module_id', xarMod::getRegid($this_module));
        $q->run();
    }

    // Remove everything else concerning the module
    return xarMod::apiFunc('modules', 'admin', 'standarddeinstall', ['module' => $this_module]);
}
