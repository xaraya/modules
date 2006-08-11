<?php
/**
 * Module Initialization Functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage window
 * @link http://xaraya.com/index.php/release/3002.html
 * @author Marc Lutolf
 * @author Yassen Yotov (CyberOto)
 */

function window_init()
{
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();

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

    xarRegisterMask('ViewWindow',   'All', 'window', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadWindow',   'All', 'window', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditWindow',   'All', 'window', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddWindow',    'All', 'window', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteWindow', 'All', 'window', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminWindow',  'All', 'window', 'Item', 'All:All:All', 'ACCESS_ADMIN');

  /* This init function brings our module to version 1.0.3, run the upgrades for the rest of the initialisation */
    return window_upgrade('1.0.3');
}

function window_upgrade($oldversion)
{
    switch($oldversion){
        case '1.0.0':
            $modversion['user'] = 1;

        case '1.0.1':
            $modversion['user'] = 0;

        case '1.0.3'; //current version
            xarRegisterMask('ViewWindow',   'All', 'window', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
            xarRegisterMask('EditWindow',   'All', 'window', 'Item', 'All:All:All', 'ACCESS_EDIT');
            xarRegisterMask('AddWindow',    'All', 'window', 'Item', 'All:All:All', 'ACCESS_ADD');
            xarRegisterMask('DeleteWindow', 'All', 'window', 'Item', 'All:All:All', 'ACCESS_DELETE');

        case '1.1.5'; 
            break;
    }
    return true;
}

function window_delete()
{
    $dbconn =& xarDBGetConn();
    $tables =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();

    $query = xarDBDropTable($tables['window']);
    if (empty($query)) return; // throw back
    if (!$dbconn->Execute($query)) return;

    xarRemoveMasks('window');
    xarRemoveInstances('window');

    xarModDelAllVars('window');

    return true;
}
?>