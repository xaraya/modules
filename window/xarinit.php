<?php
// ----------------------------------------------------------------------
// Xaraya Applications Framework
// Ported as a Xaraya module by Marc Lutolf.
// http://www.xaraya.com/
// ----------------------------------------------------------------------
// Based on: POST-NUKE Content Management System
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// ----------------------------------------------------------------------
// Original Author of file: Yassen Yotov
// ----------------------------------------------------------------------

function window_init()
{


    list($dbconn) = xarDBGetConn();
    $tables = xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();

    $sitePrefix = xarDBGetSiteTablePrefix();
    $tables['window'] = $sitePrefix . '_window';

    $query = xarDBCreateTable($tables['window'],
        array('xar_id' => array('type' => 'integer',
                'null' => false,
                'default' => '0',
                'increment' => true,
                'primary_key' => true),
            'xar_name' => array('type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => ''),
            'xar_alias' => array('type' => 'varchar',
                'size' => 255,
                'null' => false,
                'default' => ''),
            'xar_reg_user_only' => array('type' => 'integer',
                'null' => false,
                'default' => '0'),
            'xar_open_direct' => array('type' => 'integer',
                'null' => false,
                'default' => '0'),
            'xar_use_fixed_title' => array('type' => 'integer',
                'null' => false,
                'default' => '0'),
            'xar_auto_resize' => array('type' => 'integer',
                'null' => false,
                'default' => '0'),
            'xar_vsize' => array('type' => 'integer',
                'null' => false,
                'default' => '0'),
            'xar_hsize' => array('type' => 'varchar',
                'size' => 255,
                'null' => false)));

    if (!$dbconn->Execute($query)) return;

    // Define the module variables

    xarModSetVar('window', 'allow_local_only', "0");  // 1 = display off-site pages, 0 = NO display off-site pages 0*
    xarModSetVar('window', 'use_buffering', "1");     // 0 = NO buffering output before sending, 1 = buffering output before sending 0*
    xarModSetVar('window', 'reg_user_only', "0");     // 0 = works for everyone, 1 = works for logged in users only 0*
    xarModSetVar('window', 'no_user_entry', "0");     // 1 = allow input from browser address bar, 0 = NOT allow input from browser address bar 0*
    xarModSetVar('window', 'open_direct', "0");       // 0 = NO $open_direct_msg link display, 1 = $open_direct_msg link display  0*
    xarModSetVar('window', 'use_fixed_title', "0");   // 0 = NO title from $title_msg or ptitle, 1 = title from $title_msg or ptitle 0*

    // Auto window height calculation (requires Javascript, see the README file)
    xarModSetVar('window', 'auto_resize', "0");       // 0 = default size
    xarModSetVar('window', 'vsize', "600");           // set to height of screen size for loaded window 600*
    xarModSetVar('window', 'hsize', "100%");          // set to width of screen size for loaded window 100%*

    //Set security checkinkg for URL
    xarModSetVar('window', 'security', "1");          // 0 = NO check with DB, 1 = check with DB) 1*

    xarRegisterMask('AdminWindow','All','All','All','All','ACCESS_ADMIN');
    xarRegisterMask('ReadWindow','All','All','All','All','ACCESS_READ');

    // Initialisation successful
    return true;
}

function window_upgrade($oldversion)
{
    switch($oldversion){
        case '1.0':
            break;
    }
    return true;
}

function window_delete()
{
    // Delete module variables
    xarModDelVar('window', 'allow_local_only');
    xarModDelVar('window', 'use_buffering');
    xarModDelVar('window', 'reg_user_only');
    xarModDelVar('window', 'use_auth_hosts');
    xarModDelVar('window', 'no_user_entry');
    xarModDelVar('window', 'open_direct');
    xarModDelVar('window', 'use_fixed_title');
    xarModDelVar('window', 'auto_resize');
    xarModDelVar('window', 'auto_resize_type');
    xarModDelVar('window', 'auto_resize_trim');
    xarModDelVar('window', 'vsize');
    xarModDelVar('window', 'hsize');
    xarModDelVar('window', 'full_screen');
    xarModDelVar('window', 'table_borders');

    list($dbconn) = xarDBGetConn();
    $tables = xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();

    $query = xarDBDropTable($tables['window']);
    if (empty($query)) return; // throw back
    if (!$dbconn->Execute($query)) return;

    xarRemoveMasks('window');
    xarRemoveInstances('window');

    //Success
    return true;
}
?>
