<?php 
// ----------------------------------------------------------------------
// Xaraya Content Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.com/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Chris Dudley
// Purpose of file:  Initialisation functions for pubsub
// ----------------------------------------------------------------------

/**
 * initialise the pubsub module
 */
function pubsub_init()
{
    // Get database information
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    // Create tables
    $pubsubeventstable = $pntable['pubsub_events'];
    $sql = "CREATE TABLE $pubsubeventstable (
            pn_eventid int(10) NOT NULL auto_increment,
            pn_module varchar(32) NOT NULL default '',
            pn_eventtype varchar(64) NOT NULL default '',
            pn_groupdescr varchar(64) NOT NULL default '',
            pn_actionid  varchar(100) NOT NULL default 'email',
            PRIMARY KEY(pn_eventid))";
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        // Report failed initialisation attempt
        return false;
    }

    $pubsubregtable = $pntable['pubsub_reg'];
    $sql = "CREATE TABLE $pubsubregtable (
            pn_pubsubid int(10) NOT NULL default '',
            pn_eventid int(10) NOT NULL default '',
            pn_users text NOT NULL default '',
	    pn_actionid varchar(100) NOT NULL default 'email',
	    PRIMARY KEY(pn_pubsubid))";
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        // Report failed initialisation attempt
        return false;
    }

    $pubsubprocesstable = $pntable['pubsub_process'];
    $sql = "CREATE TABLE $pubsubprocesstable (
            pn_handlingid int(10) NOT NULL default '',
            pn_pubsubid int(10) NOT NULL default '',
            pn_objectid int(10) NOT NULL default '',
	    pn_status varchar(100) NOT NULL default '',
	    PRIMARY KEY(pn_handlingid))";
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        // Report failed initialisation attempt
        return false;
    }

    // Set up module hooks
    if (!pnModRegisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'admin',
                           'addevent')) {
        return false;
    }
    if (!pnModRegisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'user',
                           'adduser')) {
        return false;
    }
    if (!pnModRegisterHook('category',
                           'display',
                           'GUI',
                           'pubsub',
                           'user',
                           'display')) {
        return false;
    }
    if (!pnModRegisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'user',
                           'deluser')) {
        return false;
    }
    if (!pnModRegisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'admin',
                           'delevent')) {
        return false;
    }

    // Initialisation successful
    return true;
}

/**
 * upgrade the pubsub module from an old version
 */
function pubsub_upgrade($oldversion)
{
    return true;
}

/**
 * delete the pubsub module
 */
function pubsub_delete()
{
    // Remove module hooks
    if (!pnModUnregisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'admin',
                           'addevent')) {
        pnSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!pnModUnregisterHook('item',
                           'create',
                           'API',
                           'pubsub',
                           'user',
                           'adduser')) {
        pnSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!pnModUnregisterHook('category',
                           'display',
                           'GUI',
                           'pubsub',
                           'user',
                           'display')) {
        pnSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!pnModUnregisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'user',
                           'deluser')) {
        pnSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }
    if (!pnModUnregisterHook('item',
                           'delete',
                           'API',
                           'pubsub',
                           'admin',
                           'delevent')) {
        pnSessionSetVar('errormsg', _PUBSUBSCOULDNOTUNREGISTER);
    }

    // Get database information
    list($dbconn) = pnDBGetConn();
    $pntable = pnDBGetTables();

    // Delete tables
    $sql = "DROP TABLE $pntable[pubsub_events]";
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        // Report failed deletion attempt
        return false;
    }

    $sql = "DROP TABLE $pntable[pubsub_reg]";
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        // Report failed deletion attempt
        return false;
    }

    $sql = "DROP TABLE $pntable[pubsub_process]";
    $dbconn->Execute($sql);

    // Check database result
    if ($dbconn->ErrorNo() != 0) {
        // Report failed deletion attempt
        return false;
    }
    // Deletion successful
    return true;
}

?>
