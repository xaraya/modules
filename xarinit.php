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
    if (!$q->run($query)) return;
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
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_reminders_emails";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_reminders_emails (
        id                integer unsigned NOT NULL auto_increment,
        name              varchar(255) NOT NULL default '', 
        address           varchar(255) NOT NULL default '', 
        timecreated       integer unsigned NOT NULL default 0, 
        timemodified      integer unsigned NOT NULL default 0, 
        state             tinyint(3) NOT NULL default 3, 
        PRIMARY KEY  (id)
    )";
    if (!$q->run($query)) return;

    $query = "DROP TABLE IF EXISTS " . $prefix . "_reminders_history";
    if (!$q->run($query)) return;
    $query = "CREATE TABLE " . $prefix . "_reminders_history (
        id                integer unsigned NOT NULL auto_increment,
        entry_id          integer unsigned NOT NULL default 0, 
        message           varchar(255) NOT NULL default '', 
        address           varchar(255) NOT NULL default '', 
        timecreated       integer unsigned NOT NULL default 0, 
        PRIMARY KEY  (id)
    )";
    if (!$q->run($query)) return;

# --------------------------------------------------------
#
# Set up masks
#
    xarRegisterMask('ViewReminders',    'All','reminders','All','All','ACCESS_OVERVIEW');
    xarRegisterMask('ReadReminders',    'All','reminders','All','All','ACCESS_READ');
    xarRegisterMask('CommentReminders', 'All','reminders','All','All','ACCESS_COMMENT');
    xarRegisterMask('ModerateReminders','All','reminders','All','All','ACCESS_MODERATE');
    xarRegisterMask('EditReminders',    'All','reminders','All','All','ACCESS_EDIT');
    xarRegisterMask('AddReminders',     'All','reminders','All','All','ACCESS_ADD');
    xarRegisterMask('ManageReminders',  'All','reminders','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminReminders',   'All','reminders','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Set up privileges
#
    xarRegisterPrivilege('ViewReminders',    'All','reminders','All','All','ACCESS_OVERVIEW');
    xarRegisterPrivilege('ReadReminders',    'All','reminders','All','All','ACCESS_READ');
    xarRegisterPrivilege('CommentReminders', 'All','reminders','All','All','ACCESS_COMMENT');
    xarRegisterPrivilege('ModerateReminders','All','reminders','All','All','ACCESS_MODERATE');
    xarRegisterPrivilege('EditReminders',    'All','reminders','All','All','ACCESS_EDIT');
    xarRegisterPrivilege('AddReminders',     'All','reminders','All','All','ACCESS_ADD');
    xarRegisterPrivilege('ManageReminders',  'All','reminders','All','All','ACCESS_DELETE');
    xarRegisterPrivilege('AdminReminders',   'All','reminders','All','All','ACCESS_ADMIN');

# --------------------------------------------------------
#
# Create DD objects
#
    $module = 'reminders';
    $objects = array(
                     'reminders_emails',
                     'reminders_entries',
                     'reminders_history',
                     );

    if(!xarMod::apiFunc('modules','admin','standardinstall',array('module' => $module, 'objects' => $objects))) return;

# --------------------------------------------------------
#
# Set up modvars
#
    $module_settings = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'reminders'));
    $module_settings->initialize();

    // Add variables like this next one when creating utility modules
    // This variable is referenced in the xaradmin/modifyconfig-utility.php file
    // This variable is referenced in the xartemplates/includes/defaults.xd file
    xarModVars::set('reminders', 'defaultmastertable','reminders_history');
    xarModVars::set('reminders', 'debugmode', false);
    xarModVars::set('reminders', 'save_history', false);

# --------------------------------------------------------
#
# Default data for other modules
#
    // Add basic mailer templates
    if (xarMod::isAvailable('mailer')) {
        $dat_file = sys::code() . 'modules/' . $module . '/xardata/'.'mailer_templates-dat.xml';
        if(file_exists($dat_file)) {
            $data['file'] = $dat_file;
            $objectid = xarMod::apiFunc('dynamicdata','util','import', $data);
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

    // Remove entries of this module in the mailer mails table
    xarMod::load('mailer');
    $tables = xarDB::getTables();
    $q = new Query('DELETE', $tables['mailer_mails']);
    $q->eq('module_id', xarMod::getRegid($this_module));
    $q->run();

    // Remove everything else concerning the module
    return xarMod::apiFunc('modules','admin','standarddeinstall',array('module' => $this_module));
}

?>