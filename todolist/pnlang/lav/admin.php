<?php // $Id: admin.php,v 1.7 2002/03/12 10:32:34 voll Exp $
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Language defines for pnadmin.php
// ----------------------------------------------------------------------
//
define('_TODOLIST_ADMIN', 'Todolist administration');
define('_TODOLIST_VIEWUSERS', 'Users');
define('_TODOLIST_VIEWGROUPS', 'Groups');
define('_TODOLIST_VIEWPROJECTS', 'Projects');
define('_TODOLIST_EDITCONFIG', 'Edit Configuration');
define('_TODOLIST_PROJECT_DELETED', 'Project has been deleted');
define('_TODOLIST_PROJECT_CREATED', 'Project has been created');
define('_TODOLIST_PROJECTID', 'Id');
define('_TODOLIST_PROJECTNAME', 'Project name');
define('_TODOLIST_PROJECTDESC', 'Project description');
define('_TODOLIST_PROJECTLEADER', 'Project leader');
define('_TODOLIST_ACTION', 'Action');

define('_TODOLIST_REFRESH_MAIN','Refresh-time for the main page (Default = 600)');
define('_TODOLIST_SEND_MAILS', 'Should mails be send via local mailserver?');
define('_TODOLIST_MOST_IMPORTANT_COLOR', 'MOST_IMPORTANT_COLOR (Default = #FFFF99)');
define('_TODOLIST_VERY_IMPORTANT_COLOR', 'VERY_IMPORTANT_COLOR (Default = #FF3366)');
define('_TODOLIST_HIGH_COLOR', 'HIGH_COLOR (Default = #ffff00)');
define('_TODOLIST_MED_COLOR', 'MED_COLOR (Default = #FFcc66)');
define('_TODOLIST_LOW_COLOR', 'LOW_COLOR (Default = #66ccff)');
define('_TODOLIST_DONE_COLOR', 'DONE_COLOR (Default = #CCFFFF)');
define('_TODOLIST_BACKGROUND_COLOR', 'BACKGROUND_COLOR (Default = #99CCFF)');
define('_TODOLIST_ACCESS_RESTRICTED', 'Access restricted');
define('_TODOLIST_TODO_HEADING', "Custom title. For example the Company's-Name");
define('_TODOLIST_VERY_IMPORTANT_DAYS', 'Days in the future that should be higligted with VERY_IMPORTANT_COLOR (Disable = 0)');
define('_TODOLIST_MOST_IMPORTANT_DAYS', 'Days in the past that should be higligted with VERY_IMPORTANT_COLOR and MOST_IMPORTANT_COLOR foreground-color (Disable = 0)');
define('_TODOLIST_MAX_DONE', 'Maximum number of done-entries shown on the main page.');
define('_TODOLIST_SHOW_LINE_NUMBERS', 'Show the line-Numbers? [true/false] (Default = true)');
define('_TODOLIST_SHOW_PRIORITY_IN_TABLE', 'Show priority as text in the tables ? [true/false] (Default = true)');
define('_TODOLIST_SHOW_PERCENTAGE_IN_TABLE', 'Show percentage-completed in the tables? [true/false] (Default = true)');
define('_TODOLIST_SHOW_EXTRA_ASTERISK', 'If there is a note attached to the todo the number of notes attached is shown in the details column. To have another notification you can also show an asterisk in one of the left columns. Possible options are: 0 = disable extra asterisk, 1 = show it in #-column, 2 = show it in priority-column, 3 = show it in percentage completed-column, 4 = show it in text-column)');
define('_TODOLIST_DATEFORMAT_NUMBER', 'Dateformat: 1 = YYYY-MM-DD / 2 = DD.MM.JJJJJ / 3 = MM/DD/YYYY (Default - 2)');


define('_TODOLIST_USEREMAILNOTIFY', 'Email notify');
define('_TODOLIST_USERPRIMARYPROJECT', 'Primary project');
define('_TODOLIST_USERMYTASKS', 'My tasks');
define('_TODOLIST_USERSHOWICONS', 'Show icons');

define('_TODOLIST_USER_EMAIL_NOTIFY', 'Email notify');
define('_TODOLIST_USER_PRIMARY_PROJECT', 'Primary project');
define('_TODOLIST_USER_MY_TASKS', 'My tasks');
define('_TODOLIST_USER_SHOW_ICONS', 'Show icons');

define('_TODOLIST_ITEM', 'Item');
define('_TODOLIST_PROJECT', 'Project');
define('_TODOLIST_USER', 'User');
define('_TODOLIST_GROUP', 'Group');

define('_TODOLIST_NAME', 'Name');
define('_TODOLIST_ADD', 'Add');
define('_TODOLIST_MAINT', 'Maintenance');

define('_TODOLIST_EDITPROJECT', 'Edit Project');
define('_TODOLIST_PROJECTMAINT', 'Project Maintenance');
define('_TODOLIST_ADDPROJECT', 'Add New Project');

define('_TODOLIST_USERNAME', 'User Name');
define('_TODOLIST_EDITUSER', 'Edit User');
define('_TODOLIST_USERMAINT', 'User Maintenance');
define('_TODOLIST_ADDUSER', 'Add User');

define('_TODOLIST_GROUPNAME', 'Group Name');
define('_TODOLIST_EDITGROUP', 'Edit Group');
define('_TODOLIST_GROUPMAINT', 'Group Maintenance');
define('_TODOLIST_GROUPADD', 'Add Group');

define('_ADDTODO', 'Add example item');
define('_CANCELTODODELETE', 'Cancel deletion');
define('_CONFIRMTODODELETE', 'Confirm deletion of example item');
define('_CREATEFAILED', 'Creation attempt failed');
define('_DELETEFAILED', 'Deletion attempt failed');
define('_DELETETODO', 'Delete example item');
define('_EDITTODO', 'Edit example item');
define('_EDITTODOCONFIG', 'Edit example intems configuration');
define('_LOADFAILED', 'Load of module failed');
define('_NEWTODO', 'New example item');
define('_TODOLIST', 'Template (example)');
define('_TODOADD', 'Add example item');
define('_TODOCREATED', 'Example item created');
define('_TODODELETED', 'Example item deleted');
define('_TODODISPLAYBOLD', 'Display item names in bold');
define('_TODOMODIFYCONFIG', 'Modify example items configuration');
define('_TODONAME', 'Example item name');
define('_TODONOSUCHITEM', 'No such item');
define('_TODONUMBER', 'Example item number');
define('_TODOOPTIONS', 'Options');
define('_TODOUPDATE', 'Update example item');
define('_TODOUPDATED', 'Example item updated');
define('_VIEWTODO', 'View example items');
define('_TODOITEMSPERPAGE', 'Items per page');
if (!defined('_CONFIRM')) {
	define('_CONFIRM', 'Confirm');
}
if (!defined('_TODONOAUTH')) {
	define('_TODONOAUTH','Not authorised to access Template module');
}
?>