<?php
/**
 * File: $Id$
 *
 * Xaraya Autolinks
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Autolinks Module
 * @author Jim McDonald; Jason Judge
*/

//Load Table Maintainance API
xarDBLoadTableMaintenanceAPI();

/**
 * initialise the autolinks module
 */
function autolinks_init()
{
    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];

    // Table didn't exist, create table
    /*****************************************************************
    * $query = "CREATE TABLE $autolinkstable (
    *       xar_lid INT(11) NOT NULL auto_increment,
    *       xar_keyword VARCHAR(100) NOT NULL default '',
    *       xar_title VARCHAR(100) NOT NULL default '',
    *       xar_url VARCHAR(200) NOT NULL default '',
    *       xar_comment VARCHAR(200) NOT NULL default '',
    *       PRIMARY KEY (xar_lid),
    *       UNIQUE KEY keyword (xar_keyword))";
    *****************************************************************/
    $fields = array (
    'xar_lid'           => array ('type'=>'integer', 'null'=>false, 'increment'=>true, 'primary_key'=>true),
    'xar_keyword'       => array ('type'=>'varchar', 'size'=>200, 'null'=>false, 'default'=>''),
    'xar_title'         => array ('type'=>'varchar', 'size'=>100, 'null'=>false, 'default'=>''),
    'xar_url'           => array ('type'=>'varchar', 'size'=>200, 'null'=>false, 'default'=>''),
    'xar_comment'       => array ('type'=>'varchar', 'size'=>200, 'null'=>false, 'default'=>''),
    'xar_enabled'       => array ('type'=>'integer', 'size'=>'tiny', 'null'=>false, 'default'=>'1'),
    'xar_valid'         => array ('type'=>'integer', 'size'=>'tiny', 'null'=>false, 'default'=>'1'),
    'xar_match_re'      => array ('type'=>'integer', 'size'=>'tiny', 'null'=>false, 'default'=>'0'),
    'xar_sample'        => array ('type'=>'varchar', 'size'=>200, 'null'=>true, 'default'=>''),
    'xar_cache_replace' => array ('type'=>'varchar', 'size'=>200, 'null'=>true, 'default'=>'')
    );

    $query = xarDBCreateTable($autolinkstable,$fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . xarDBGetSiteTablePrefix() . '_autolinks_1',
                   'fields'    => array ('xar_keyword'),
                   'unique'    => TRUE);
    $query = xarDBCreateIndex($autolinkstable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Set up module variables
    xarModSetVar('autolinks', 'itemsperpage', 20);
    xarModSetVar('autolinks', 'decoration', '');
    xarModSetVar('autolinks', 'maxlinkcount', '');
    xarModSetVar('autolinks', 'newwindow', 1);
    xarModSetVar('autolinks', 'punctuation', ".!?\"';:");
    xarModSetVar('autolinks', 'nbspiswhite', '1');

    // Set up module hooks
    if (!xarModRegisterHook(
            'item', 'transform', 'API',
            'autolinks', 'user', 'transform')
    ) {return;}

    $query1 = "SELECT DISTINCT xar_keyword FROM ".$autolinkstable;
    $query2 = "SELECT DISTINCT xar_lid FROM ".$autolinkstable;
    $instances = array (
        array (
            'header' => 'Autolink Keyword:',
            'query' => $query1,
            'limit' => 20
        ),
        array (
            'header' => 'Autolink ID:',
            'query' => $query2,
            'limit' => 20
        )
    );

    xarDefineInstance('autolinks', 'Autolinks', $instances, 0, 'All', 'All', 'All', 'Security instance for autolinks module.');

    // Register Masks
    xarRegisterMask('ReadAutolinks','All','autolinks','All','All','ACCESS_READ');
    xarRegisterMask('EditAutolinks','All','autolinks','All','All','ACCESS_EDIT');
    xarRegisterMask('AddAutolinks','All','autolinks','All','All','ACCESS_ADD');
    xarRegisterMask('DeleteAutolinks','All','autolinks','All','All','ACCESS_DELETE');
    xarRegisterMask('AdminAutolinks','All','autolinks','All','All','ACCESS_ADMIN');

    // Initialisation successful
    return true;
}

/**
 * upgrade the autolinks module from an older version
 */
function autolinks_upgrade($oldversion)
{
    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];

    // Upgrade dependent on old version number
    switch ($oldversion) {

        // TODO: version numbers - normalise.
        case '1.0':
            // Changes to upgrade from 1.0 to 1.1

            // Convert some module variables to the new versions.
            // If only first link is selected, then set link count to one.
            if (xarModGetVar('autolinks', 'linkfirst') == 1) {
                xarModSetVar('autolinks', 'maxlinkcount', '1');
            } else {
                xarModSetVar('autolinks', 'maxlinkcount', '');
            }

            // If underlining is disabled then set the link style to 'none'.
            if (xarModGetVar('autolinks', 'invisilinks') == 1) {
                xarModSetVar('autolinks', 'decoration', 'none');
            } else {
                xarModSetVar('autolinks', 'decoration', '');
            }

            // Remove old module variables.
            xarModDelVar('autolinks', 'linkfirst');
            xarModDelVar('autolinks', 'invisilinks');

        case '1.1':
            // Changes to upgrade from 1.0 to 1.1

            // Add columns to the Autolinks table.
            $queries = array ();

            $queries[] = xarDBAlterTable(
                $autolinkstable,
                array (
                    'command'    => 'add',
                    'field'      => 'xar_enabled',
                    'type'       => 'integer',
                    'size'       => 'tiny',
                    'null'       => false,
                    'first'      => false,
                    'default'    => '1'
                )
            );

            $queries[] = xarDBAlterTable(
                $autolinkstable,
                array (
                    'command'    => 'add',
                    'field' => 'xar_valid',
                    'type' => 'integer',
                    'size' => 'tiny',
                    'null' => false,
                    'first' => false,
                    'default' => '1'
                )
            );

            foreach ($queries as $query)
            {
                // Pass to ADODB, and send exception if the result isn't valid.
                $result =& $dbconn->Execute($query);
                if (!$result) {
                    return;
                }
            }

        case '1.2':
            // Changes to upgrade from 1.1 to 1.2

            $queries[] = xarDBAlterTable(
                $autolinkstable,
                array (
                    'command'    => 'add',
                    'field' => 'xar_match_re',
                    'type' => 'integer',
                    'size' => 'tiny',
                    'null' => false,
                    'first' => false,
                    'default' => '0'
                )
            );

            $queries[] = xarDBAlterTable(
                $autolinkstable,
                array (
                    'command'    => 'add',
                    'field' => 'xar_sample',
                    'type' => 'varchar',
                    'size' => '200',
                    'null' => true,
                    'first' => false,
                    'default' => ''
                )
            );

            $queries[] = xarDBAlterTable(
                $autolinkstable,
                array (
                    'command'    => 'add',
                    'field' => 'xar_cache_replace',
                    'type' => 'varchar',
                    'size' => '200',
                    'null' => true,
                    'first' => false,
                    'default' => ''
                )
            );

            /*
            // TODO: modify DDL is not yet supported.
            $queries[] = xarDBAlterTable(
                $autolinkstable,
                array (
                    'command'    => 'modify',
                    'field' => 'xar_keyword',
                    'type' => 'varchar',
                    'size' => '200',
                    'null' => false,
                    'first' => false,
                    'default' => ''
                )
            );
            */

            foreach ($queries as $query)
            {
                // Pass to ADODB, and send exception if the result isn't valid.
                $result =& $dbconn->Execute($query);
                if (!$result) {
                    return;
                }
            }

            xarModSetVar('autolinks', 'punctuation', ".!?\"';:");
            xarModSetVar('autolinks', 'nbspiswhite', '1');

        case '1.3':
            // The current version.
            return true;
    }

    // Should never reach this point.
    return false;
}

/**
 * Delete the Autolinks module
 */
function autolinks_delete()
{
    // Remove module hooks
    if (!xarModUnregisterHook('item',
                             'transform',
                             'API',
                             'autolinks',
                             'user',
                             'transform')) {return;}

    // Drop the table
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $autolinkstable = $xartable['autolinks'];
    $query = xarDBDropTable($autolinkstable );
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Remove module variables
    xarModDelVar('autolinks', 'itemsperpage');
    xarModDelVar('autolinks', 'maxlinkcount');
    xarModDelVar('autolinks', 'decoration');
    xarModDelVar('autolinks', 'punctuation');
    xarModDelVar('autolinks', 'nbspiswhite');

    // Remove Masks and Instances
    xarRemoveMasks('autolinks');
    xarRemoveInstances('autolinks');

    // Deletion successful
    return true;
}

?>
