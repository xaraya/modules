<?php
/**
* eBulletin initialization functions
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Initialize the module
*
* This function is only ever called once during the lifetime of a particular
* module instance.
*/
function ebulletin_init()
{
    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();

    // publications table
    $pubstable = $xartable['ebulletin'];
    $fields = array(
        'xar_id'           => array(
            'type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true
        ),
        'xar_name'         => array('type' => 'varchar', 'size' => 255,   'null' => false),
        'xar_desc'         => array('type' => 'varchar', 'size' => 255,   'null' => false),
        'xar_public'       => array('type' => 'integer', 'size' => 1,     'null' => false),
        'xar_from'         => array('type' => 'varchar', 'size' => 255,   'null' => false),
        'xar_fromname'     => array('type' => 'varchar', 'size' => 255,   'null' => false),
        'xar_replyto'      => array('type' => 'varchar', 'size' => 255,   'null' => false),
        'xar_replytoname'  => array('type' => 'varchar', 'size' => 255,   'null' => false),
        'xar_subject'      => array('type' => 'varchar', 'size' => 255,   'null' => false),
        'xar_template'     => array('type' => 'varchar', 'size' => 255,   'null' => false),
        'xar_html'         => array('type' => 'integer', 'size' => 1,     'null' => false),
        'xar_startday'     => array('type' => 'integer', 'size' => 8,     'null' => false),
        'xar_endday'       => array('type' => 'integer', 'size' => 8,     'null' => false),
        'xar_theme'        => array('type' => 'varchar', 'size' => 255,   'null' => false),
/*
        'xar_tpl_txt'      => array('type' => 'varchar', 'size' => 255,   'null' => false),
        'xar_tpl_html'     => array('type' => 'varchar', 'size' => 255,   'null' => false),
        'xar_numsago'      => array('type' => 'integer', 'size' => 8,     'null' => false),
        'xar_unitsago'     => array('type' => 'varchar', 'size' => 8,     'null' => false),
        'xar_startsign'    => array('type' => 'varchar', 'size' => 8,     'null' => false),
        'xar_numsfromnow'  => array('type' => 'integer', 'size' => 8,     'null' => false),
        'xar_unitsfromnow' => array('type' => 'varchar', 'size' => 8,     'null' => false),
        'xar_endsign'      => array('type' => 'varchar', 'size' => 8,     'null' => false)*/
    );

    // let xarDB create the query for us
    $query = xarDBCreateTable($pubstable, $fields);
    if (empty($query)) return;

    // create the table
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // issues table
    $issuestable = $xartable['ebulletin_issues'];
    $fields = array(
        'xar_id'        => array(
            'type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true
        ),
        'xar_pid'       => array('type' => 'integer', 'size' => 'small', 'null' => false),
        'xar_issuedate' => array('type' => 'date',                       'null' => false),
        'xar_subject'   => array('type' => 'varchar', 'size' => 255,     'null' => false),
        'xar_body_html' => array('type' => 'text',                       'null' => false),
        'xar_body_txt'  => array('type' => 'text',                       'null' => false),
        'xar_published' => array('type' => 'boolean',                    'null' => false)
    );

    // let xarDB create the query for us
    $query = xarDBCreateTable($issuestable, $fields);
    if (empty($query)) return;

    // create the table
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // subscriptions table
    $subscriptionstable = $xartable['ebulletin_subscriptions'];
    $fields = array(
        'xar_id'    => array(
            'type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true
        ),
        'xar_pid'   => array('type' => 'integer', 'size' => 'small', 'null' => false),
        'xar_name'  => array('type' => 'varchar', 'size' => 255,     'null' => false),
        'xar_email' => array('type' => 'varchar', 'size' => 255,     'null' => false),
        'xar_uid'   => array('type' => 'integer', 'size' => 'large', 'null' => false)
    );

    // let xarDB create the query for us
    $query = xarDBCreateTable($subscriptionstable, $fields);
    if (empty($query)) return;

    // create the table
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // set module vars
    xarModSetVar('ebulletin', 'admin_issuesperpage', 10);
    xarModSetVar('ebulletin', 'admin_subsperpage',   40);
    xarModSetVar('ebulletin', 'SupportShortURLs',    0);
    xarModSetVar('ebulletin', 'useModuleAlias',      false);
    xarModSetVar('ebulletin', 'aliasname',           '');
    xarModSetVar('ebulletin', 'msglimit',            '');
    xarModSetVar('ebulletin', 'msgunit',             'hour');
    xarModSetVar('ebulletin', 'requirevalidation',   1);

    // register blocks
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
        array('modName' => 'ebulletin', 'blockType' => 'subscription'))
    ) return;

    // register usermenu hook (give user some subscription options)
    if (!xarModRegisterHook('item', 'usermenu', 'GUI', 'ebulletin', 'user', 'usermenu')) {
        return false;
    }
    // register createhook hook (adds default subscriptions to newly registered users)
    if (!xarModRegisterHook('item', 'create', 'API', 'ebulletin', 'user', 'createhook')) {
        return false;
    }

    // define publication instances
    $query1 = "SELECT DISTINCT xar_name FROM " . $pubstable;
    $query3 = "SELECT DISTINCT xar_id FROM " . $pubstable;
    $instances = array(
        array('header' => 'Publication Name:', 'query' => $query1, 'limit' => 20),
        array('header' => 'Publication ID:',   'query' => $query3, 'limit' => 20)
    );
    xarDefineInstance('ebulletin', 'Publication', $instances);

    // define block instance
    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_title
        FROM $instancestable i, $typestable t
        WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'ebulletin'
    ";
    $instances = array(array('header' => 'Block Title:', 'query' => $query, 'limit' => 20));
    xarDefineInstance('ebulletin', 'Block', $instances);

    // register block masks
    xarRegisterMask('ReadeBulletinBlock', 'All', 'ebulletin', 'Block', 'All', 'ACCESS_OVERVIEW');

    // register publication masks
    xarRegisterMask('VieweBulletin',   'All', 'ebulletin', 'Publication', 'All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadeBulletin',   'All', 'ebulletin', 'Publication', 'All:All', 'ACCESS_READ');
    xarRegisterMask('EditeBulletin',   'All', 'ebulletin', 'Publication', 'All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddeBulletin',    'All', 'ebulletin', 'Publication', 'All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteeBulletin', 'All', 'ebulletin', 'Publication', 'All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdmineBulletin',  'All', 'ebulletin', 'Publication', 'All:All', 'ACCESS_ADMIN');

    // success
    return true;
}

/**
* Upgrade the module from an old version
*
* This function can be called multiple times.
*
* @param string $oldVersion Version to upgrade from
*/
function ebulletin_upgrade($oldversion)
{
    // Set up database tables
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ebulletintable = $xartable['ebulletin'];
    $subscriptionstable = $xartable['ebulletin_subscriptions'];

    // Get a data dictionary object with item create methods.
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    switch($oldversion) {
    case '0.9.5':
        // changes after 0.9.5 here
    case '0.9.7':
        /**
        * Changes after 0.9.7:
        *
        * Move uid for registered subscribers to its own column
        * instead of storing them in xar_email.
        */

        // create new column
        $result = $datadict->ChangeTable(
            $subscriptionstable, 'xar_uid I8 NotNull DEFAULT 0'
        );
        if (!$result) {return;}

        // move uids to new column
        $query = "
            UPDATE $subscriptionstable
            SET
                xar_uid = xar_email,
                xar_email = ''
            WHERE xar_email REGEXP '^[[:digit:]]+$'
        ";
        $result = $dbconn->Execute($query);
        if (!$result) {return;}
    case '0.9.8':
        // changes after 0.9.8 here
    case '1.0.0':
        // changes after 1.0.0 here
    case '1.0.1':
        /**
        * Changes after 1.0.1:
        *
        * Delete "to" and "toname" fields from main table.
        * Add scheduler options.
        */

        // drop columns
        $result = $datadict->DropColumn(
            $ebulletintable, 'xar_to,xar_toname'
        );
        if (!$result) {return;}

        // add scheduler modvars
        xarModSetVar('ebulletin', 'msglimit', '');
        xarModSetVar('ebulletin', 'msgunit', 'hour');

    case '1.1.0':
        // changes after 1.1.0 here
        // LOTS of stuff should go here.......
    case '1.2.0':
        // changes after 1.2.0 here
        // TBD
    }
    return true;
}

/**
* Delete the module
*
* This function is only ever called once during the lifetime of a particular
* module instance.
*/
function ebulletin_delete()
{
    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();

    // make list of tables
    $tables = array(
        $xartable['ebulletin'],
        $xartable['ebulletin_issues'],
        $xartable['ebulletin_subscriptions']
    );

    // drop tables one by one
    foreach ($tables as $table) {

        // let xarDB create the query for us
        $query = xarDBDropTable($table);
        if (empty($query)) return;

        // drop the table
        $result = $dbconn->Execute($query);
        if (!$result) return;
    }

    // remove module vars, masks, and instances
    xarModDelAllVars('ebulletin');
    xarRemoveMasks('ebulletin');
    xarRemoveInstances('ebulletin');

    // success
    return true;
}

?>
