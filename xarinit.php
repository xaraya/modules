<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
// Helpdesk Is Based On:
/********************************************************/
/* Dimensionquest Help Desk                             */
/*  Development by:                                     */
/*     Burke Azbill - burke@dimensionquest.net          */
/*                                                      */
/* This program is opensource so you can do whatever    */
/* you want with it.                                    */
/*                                                      */
/* http://www.dimensionquest.net            */
/********************************************************/

/**
* initialise the helpdesk module
*/
function helpdesk_init()
{
    // Get database information
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Create tables
    $helpdesktable  = $xartable['helpdesk_tickets'];
    $helpdeskcolumn = &$xartable['helpdesk_tickets_column'];

    $fields = array(
        'xar_id'          => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_date'        => array('type'=>'datetime', 'null'=>FALSE),
        'xar_updated'     => array('type'=>'datetime', 'null'=>FALSE),
        'xar_statusid'    => array('type'=>'integer', 'size'=>11,'null'=>FALSE, 'default'=>'0'),
        'xar_priorityid'  => array('type'=>'integer', 'size'=>11,'null'=>FALSE, 'default'=>'0'),
        'xar_sourceid'    => array('type'=>'integer', 'size'=>11,'null'=>FALSE, 'default'=>'0'),
        'xar_openedby'    => array('type'=>'integer', 'size'=>11,'null'=>FALSE, 'default'=>'0'),
        'xar_assignedto'  => array('type'=>'integer', 'size'=>11,'null'=>FALSE, 'default'=>'0'),
        'xar_closedby'    => array('type'=>'integer', 'size'=>11,'null'=>FALSE, 'default'=>'0'),
        'xar_domain'      => array('type'=>'varchar', 'size'=>255,'null'=>FALSE, 'default'=>''),
        'xar_subject'     => array('type'=>'varchar', 'size'=>255,'null'=>FALSE, 'default'=>''),
        'xar_name'        => array('type'=>'varchar', 'size'=>50,'null'=>FALSE, 'default'=>''),
        'xar_phone'       => array('type'=>'varchar', 'size'=>50,'null'=>FALSE, 'default'=>''),
        'xar_email'       => array('type'=>'varchar', 'size'=>50,'null'=>FALSE, 'default'=>'')
    );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($helpdesktable,$fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!isset($result)) return;

    /**
        Creates the Priority table
    */
    $table  = $xartable['helpdesk_priority'];

    $fields = array(
        'xar_pid'          => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_priority'        => array('type'=>'varchar', 'size'=>20,'null'=>FALSE, 'default'=>''),
        'xar_color'       => array('type'=>'varchar', 'size'=>10,'null'=>FALSE, 'default'=>'')
    );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($table,$fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!isset($result)) return;

    /**
        Creates the Status table
    */
    $table  = $xartable['helpdesk_status'];

    $fields = array(
        'xar_sid'          => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_status'        => array('type'=>'varchar', 'size'=>20,'null'=>FALSE, 'default'=>'')
    );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($table,$fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!isset($result)) return;

    /**
        Creates the Source table
    */
    $table  = $xartable['helpdesk_source'];

    $fields = array(
        'xar_sid'          => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_source'       => array('type'=>'varchar', 'size'=>20,'null'=>FALSE, 'default'=>'')
    );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($table,$fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!isset($result)) return;

    // Set up module variables
    xarModSetVar('helpdesk', 'Website', 'http://www.abrasiontechnology.com/');
    xarModSetVar('helpdesk', 'Default rows per page', 20);
    xarModSetVar('helpdesk', 'Page Count Limit', 10);
    xarModSetVar('helpdesk', 'default_open_status', 2);
    xarModSetVar('helpdesk', 'open_statuses', serialize(array(1,2)));
    xarModSetVar('helpdesk', 'default_resolved_status', 3);
    xarModSetVar('helpdesk', 'resolved_statuses', serialize(array(3,4,5)));

    xarModSetVar('helpdesk', 'Tech NewTicket Msg', 'New Tech Ticket');
    xarModSetVar('helpdesk', 'User NewTicket Msg', 'New User Ticket');
    xarModSetVar('helpdesk', 'MAIN menu Msg', 'Menu Msg');

    // On/Off or Yes/No settings: (1=yes, 0=no)
    xarModSetVar('helpdesk', 'Anonymous can Submit', 1);
    xarModSetVar('helpdesk', 'User can Submit', 1);
    xarModSetVar('helpdesk', 'User can check status',1);
    xarModSetVar('helpdesk', 'Techs see all tickets', 1);
    xarModSetVar('helpdesk', 'EnforceAuthKey', 1);
    xarModSetVar('helpdesk', 'Enable Images',0);
    xarModSetVar('helpdesk', 'AllowCloseOnSubmit',1);
    xarModSetVar('helpdesk', 'ShowOpenedByInSummary',1);
    xarModSetVar('helpdesk', 'ShowAssignedToInSummary',1);
    xarModSetVar('helpdesk', 'ShowClosedByInSummary',1);
    xarModSetVar('helpdesk', 'OpenedByDefaultToLoggedIn',1);
    xarModSetVar('helpdesk', 'AssignedToDefaultToLoggedIn',1);
    xarModSetVar('helpdesk', 'ShowDateEnteredInSummary',1);
    xarModSetVar('helpdesk', 'ShowLastModifiedInSummary',1);
    xarModSetVar('helpdesk', 'ShowPriorityInSummary',1);
    xarModSetVar('helpdesk', 'ShowStatusInSummary',1);
    xarModSetVar('helpdesk', 'AllowDomainName', 1);
    xarModSetVar('helpdesk', 'EnableMyStatsHyperLink', 1);
    // Module variable for testing
    xarModSetVar('helpdesk', 'debug message', '');

    xarRegisterMask('viewhelpdesk',   'All','helpdesk','helpdesk','All', 'ACCESS_OVERVIEW');
    xarRegisterMask('readhelpdesk',   'All','helpdesk','helpdesk','All', 'ACCESS_READ');
    xarRegisterMask('submithelpdesk', 'All','helpdesk','helpdesk','All', 'ACCESS_COMMENT');
    xarRegisterMask('edithelpdesk',   'All','helpdesk','helpdesk','All', 'ACCESS_EDIT');
    xarRegisterMask('addhelpdesk',    'All','helpdesk','helpdesk','All', 'ACCESS_ADD');
    xarRegisterMask('deletehelpdesk', 'All','helpdesk','helpdesk','All', 'ACCESS_DELETE');
    xarRegisterMask('adminhelpdesk',  'All','helpdesk','helpdesk','All', 'ACCESS_ADMIN');

    // let's hook cats in
    $cid = xarModAPIFunc('categories', 'admin', 'create',
        array(
            'name' => 'Helpdesk',
            'description' => 'Main Helpdesk Cats.',
            'parent_id' => 0
        )
    );
    // Note: you can have more than 1 mastercid (cfr. articles module)
    xarModSetVar('helpdesk', 'number_of_categories', 1);
    xarModSetVar('helpdesk', 'mastercids', $cid);
    $categories = array();
    $categories[] = array(
        'name' => "General Helpdesk",
        'description' => "General helpdesk"
    );
    $categories[] = array(
        'name' => "Networking",
        'description' => "Networking"
    );
    $categories[] = array(
        'name' => "Tech Support",
        'description' => "Tech Support"
    );
    $categories[] = array(
        'name' => "Software",
        'description' => "Software"
    );
    foreach($categories as $subcat)
    {
        $subcid = xarModAPIFunc('categories', 'admin', 'create',
            array(
                'name' => $subcat['name'],
                'description' => $subcat['description'],
                'parent_id' => $cid
            )
        );
    }

    xarModAPILoad('helpdesk');
    // Enable categories hooks for helpdesk
    xarModAPIFunc('modules','admin','enablehooks',
        array(
            'callerModName' => 'helpdesk',
            'callerItemType' => TICKET_ITEMTYPE, // Ticket Item Type
            'hookModName' => 'categories'
        )
    );

    // Enable comments hooks for helpdesk
    xarModAPIFunc('modules','admin','enablehooks',
        array(
            'callerModName' => 'helpdesk',
            'callerItemType' => TICKET_ITEMTYPE, // Ticket Item Type
            'hookModName' => 'hitcount'
        )
    );

    // Enable owner hooks for helpdesk
    //xarModAPIFunc('modules','admin','enablehooks',
    //      array('callerModName' => 'helpdesk', 'hookModName' => 'owner'));

    // Enable security hooks for helpdesk
    xarModAPIFunc('modules','admin','enablehooks',
        array(
            'callerModName' => 'helpdesk',
            'callerItemType' => TICKET_ITEMTYPE, // Ticket Item Type
            'hookModName' => 'security'
        )
    );

    // Default Security Levels for helpesk.

    /**
    * Ok, Now lets create all of our dd objects
    */
    $path = "modules/helpdesk/xardata/";

    /*
    * The Priority Object
    */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'hd_priority.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('helpdesk','priorityobjectid',$objectid);
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'hd_priority.data.xml'));
    if (empty($objectid)) return;

    /*
    * The Sources Object
    */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'hd_sources.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('helpdesk','sourcesobjectid',$objectid);
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'hd_sources.data.xml'));
    if (empty($objectid)) return;

    /*
    * The Status Object
    */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'hd_status.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('helpdesk','statusobjectid',$objectid);
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'hd_status.data.xml'));
    if (empty($objectid)) return;

    /*
    * The Rep Object
    */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'hd_representatives.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('helpdesk','representativesobjectid',$objectid);

    // Initialisation successful
    return true;
}

/**
* upgrade the _helpdesk module from an old version
*/
function helpdesk_upgrade($oldversion)
{
    xarModAPILoad('helpdesk');

    // Get database information
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Create tables
    $helpdesktable = $xartable['helpdesk_tickets'];

    switch($oldversion) {
        case '.3':
        case '.3.0':
        case '.3.1':
            /**
            * Ok, Now lets create all of our dd objects
            */
            $path = "modules/helpdesk/xardata/";
            /*
            * The Rep Object
            */
            $objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => $path . 'hd_representatives.xml'));
            if (empty($objectid)) return;
            // save the object id for later
            xarModSetVar('helpdesk','representativesobjectid',$objectid);

        case '.3.2':
            $args = array('command' => 'add',
                          'field'   => 'xar_date',
                          'type'    => 'datetime',
                          'null'    => FALSE
                         );
            xarDBAlterTable($helpdesktable, $args, $databaseType = NULL);

            $args = array('command' => 'add',
                          'field'   => 'xar_updated',
                          'type'    => 'datetime',
                          'null'    => FALSE
                         );
            xarDBAlterTable($helpdesktable, $args, $databaseType = NULL);


            // let's hook it in
            $cid = xarModAPIFunc('categories', 'admin', 'create',
                                array('name' => 'Helpdesk',
                                    'description' => 'Main Helpdesk Cats.',
                                    'parent_id' => 0));
            // Note: you can have more than 1 mastercid (cfr. articles module)
            xarModSetVar('helpdesk', 'number_of_categories', 1);
            xarModSetVar('helpdesk', 'mastercids', $cid);
            $categories = array();
            $categories[] = array('name' => "General Helpdesk",
                                'description' => "General helpdesk");
            $categories[] = array('name' => "Networking",
                                'description' => "Networking");
            $categories[] = array('name' => "Tech Support",
                                'description' => "Tech Support");
            $categories[] = array('name' => "Software",
                                'description' => "Software");
            foreach($categories as $subcat) {
                $subcid = xarModAPIFunc('categories', 'admin', 'create',
                                        array('name' => $subcat['name'],
                                            'description' => $subcat['description'],
                                            'parent_id' => $cid));
            // Enable categories hooks for helpdesk
            xarModAPIFunc('modules','admin','enablehooks',
                          array('callerModName' => 'helpdesk', 'hookModName' => 'categories'));
            }

        case '.3.3':
        case '0.3.3':

        case '0.4.0':

        case '0.5.0':
            /**
                Creates the Priority table
            */
            $table  = $xartable['helpdesk_priority'];

            $fields = array(
                'xar_pid'          => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
                'xar_priority'        => array('type'=>'varchar', 'size'=>20,'null'=>FALSE, 'default'=>''),
                'xar_color'       => array('type'=>'varchar', 'size'=>10,'null'=>FALSE, 'default'=>'')
            );

            // Create the Table - the function will return the SQL is successful or
            // raise an exception if it fails, in this case $query is empty
            $query = xarDBCreateTable($table,$fields);
            if (empty($query)) return; // throw back
            $result = $dbconn->Execute($query);
            if (!isset($result)) return;

            /**
                Creates the Status table
            */
            $table  = $xartable['helpdesk_status'];

            $fields = array(
                'xar_sid'          => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
                'xar_status'        => array('type'=>'varchar', 'size'=>20,'null'=>FALSE, 'default'=>'')
            );

            // Create the Table - the function will return the SQL is successful or
            // raise an exception if it fails, in this case $query is empty
            $query = xarDBCreateTable($table,$fields);
            if (empty($query)) return; // throw back
            $result = $dbconn->Execute($query);
            if (!isset($result)) return;

            /**
                Creates the Source table
            */
            $table  = $xartable['helpdesk_source'];

            $fields = array(
                'xar_sid'          => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
                'xar_source'       => array('type'=>'varchar', 'size'=>20,'null'=>FALSE, 'default'=>'')
            );

            // Create the Table - the function will return the SQL is successful or
            // raise an exception if it fails, in this case $query is empty
            $query = xarDBCreateTable($table,$fields);
            if (empty($query)) return; // throw back
            $result = $dbconn->Execute($query);
            if (!isset($result)) return;

            /*
                Now let load the data again
            */
            $path = "modules/helpdesk/xardata/";
            xarModAPIFunc('dynamicdata','util','import',
                          array('file' => $path . 'hd_priority.data.xml'));

            xarModAPIFunc('dynamicdata','util','import',
                          array('file' => $path . 'hd_sources.data.xml'));

            xarModAPIFunc('dynamicdata','util','import',
                          array('file' => $path . 'hd_status.data.xml'));

        case '0.5.1':
        case '0.5.6':
        case '0.7.0':
        case '0.7.1':
        case '0.7.2':
            // Enable security hooks for helpdesk
            xarModAPIFunc('modules','admin','enablehooks',
                array(
                    'callerModName' => 'helpdesk',
                    'callerItemType' => TICKET_ITEMTYPE, // Ticket Item Type
                    'hookModName' => 'security'
                )
            );

            // owner is not need as we just do a join on the helpdesk owner field
            xarModAPIFunc('modules','admin','disablehooks',
                array(
                    'callerModName' => 'helpdesk',
                    'hookModName' => 'owner'
                )
            );

        case '0.7.5':
            xarModAPIFunc('modules','admin','disablehooks',
                array(
                    'callerModName' => 'helpdesk',
                    'hookModName' => 'comments'
                )
            );

        case '0.7.6':
            xarModSetVar('helpdesk', 'open_statuses', serialize(array(1,2)));
            xarModSetVar('helpdesk', 'resolved_statuses', serialize(array(3,4,5)));
        case '0.7.7':
            xarModSetVar('helpdesk', 'default_open_status', 2);
            xarModSetVar('helpdesk', 'default_resolved_status', 3);
        case '0.7.8':
        case '0.7.9':

        default:
            break;
    }
    // If all else fails, return true so the module no longer shows "Upgrade" in module administration
    return true;
}

/**
* delete the helpdesk module
*/
function helpdesk_delete()
{
    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Get database information
    $dbconn   =& xarDBGetConn();
    $xartable =  xarDBGetTables();

    // Delete tables
    $query = xarDBDropTable($xartable['helpdesk_tickets']);
    $result =& $dbconn->Execute($query);

   // Delete tables
    $query = xarDBDropTable($xartable['helpdesk_source']);
    $result =& $dbconn->Execute($query);

   // Delete tables
    $query = xarDBDropTable($xartable['helpdesk_status']);
    $result =& $dbconn->Execute($query);

   // Delete tables
    $query = xarDBDropTable($xartable['helpdesk_priority']);
    $result =& $dbconn->Execute($query);

    $objectid = xarModGetVar('helpdesk','priorityobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

    $objectid = xarModGetVar('helpdesk','sourcesobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

    $objectid = xarModGetVar('helpdesk','statusobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

    $objectid = xarModGetVar('helpdesk','representativesobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

    xarModDelAllVars('helpdesk');

    // Remove all comments still in the system
    xarModAPIFunc('comments', 'admin', 'remove_module',
        array(
            'objectid' => 'helpdesk',
            'extrainfo' => true
        )
    );

    // Delete all security levels for tickets
    xarModAPIFunc('security', 'admin', 'delete',
        array(
            'modid' => xarModGetIdFromName('helpdesk')
        )
    );

    // Removes hooks
    xarModAPIFunc('modules','admin','disablehooks',
        array(
            'callerModName' => 'helpdesk',
            'hookModName' => 'security'
        )
    );
    $result = xarModAPIFunc('modules','admin','disablehooks',
        array(
            'callerModName' => 'helpdesk',
            'hookModName' => 'comments'
        )
    );
    if( empty($result) ){ return false; }

    xarModAPIFunc('modules','admin','disablehooks',
        array(
            'callerModName' => 'helpdesk',
            'hookModName' => 'categories'
        )
    );
    xarModAPIFunc('modules','admin','disablehooks',
        array(
            'callerModName' => 'helpdesk',
            'hookModName' => 'hitcount'
        )
    );

    xarRemoveMasks('helpdesk');
    xarRemoveInstances('helpdesk');

    return true;
}

function helpdesk_get_sources()
{
    $sources = array(

    );

    return $sources;
}

?>
