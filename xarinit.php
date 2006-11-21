<?php
/**
 * Site Tools Initialization
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools Module
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * initialise the sitetools module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function sitetools_init()
{
    /* Get datbase setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently.
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $sitetoolstable = $xartable['sitetools'];

    xarDBLoadTableMaintenanceAPI();
    /* Define the table structure in this associative array
     * There is one element for each field.  The key for the element is
     * the physical field name.  The element contains another array specifying the
     * data type and associated parameters
     */
    $fields = array('xar_stid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
                    'xar_stgained' => array('type'=>'float', 'size' =>'decimal', 'width'=>12, 'decimals'=>2)
                );

    $query = xarDBCreateTable($sitetoolstable, $fields);
    if (empty($query)) return; // throw back

    /* Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful */
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    $linkstable = $xartable['sitetools_links'];
    $query = xarDBCreateTable($linkstable,
                             array('xar_id'         => array('type'        => 'integer',
                                                            'null'       => false,
                                                            'increment'  => true,
                                                            'primary_key' => true),
                                   'xar_link'       => array('type'        => 'varchar',
                                                            'size'        => 254,
                                                            'null'        => false,
                                                            'default'     => ''),
                                   'xar_status'     => array('type'        => 'integer',
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'xar_moduleid'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'xar_itemtype'   => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'xar_itemid'     => array('type'        => 'integer',
                                                            'unsigned'    => true,
                                                            'null'        => false,
                                                            'default'     => '0'),
                                   'xar_itemtitle'  => array('type'        => 'varchar',
                                                            'size'        => 254,
                                                            'null'        => false,
                                                            'default'     => ''),
                                   'xar_itemlink'   => array('type'        => 'varchar',
                                                            'size'        => 254,
                                                            'null'        => false,
                                                            'default'     => ''),
                                  ));

    if (empty($query)) return;

    /* Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful */
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    /* allow several entries for the same link here */
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_sitetools_links_link',
        'fields'    => array('xar_link'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($linkstable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    /* allow several links for the same module item */
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_sitetools_links_combo',
        'fields'    => array('xar_moduleid','xar_itemtype','xar_itemid'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($linkstable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    /* allow many entries with the same status here */
    $index = array(
        'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_sitetools_links_status',
        'fields'    => array('xar_status'),
        'unique'    => false
    );
    $query = xarDBCreateIndex($linkstable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    /* create the dynamic object that will represent our items */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/sitetools/sitetools_links.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('sitetools','objectid_links',$objectid);

    /* Set up an initial value for a module variable. */
    /* Use relative path for now */
    /*   if( isset( $_SERVER['PATH_TRANSLATED'] ) )
    {
        $backupdir = dirname(realpath($_SERVER['PATH_TRANSLATED'])) . '/var/uploads/backup';
    } elseif( isset( $_SERVER['SCRIPT_FILENAME'] ) ) {
        $backupdir = dirname(realpath($_SERVER['SCRIPT_FILENAME'])) . '/var/uploads/backup';
    } else {
        $backupdir = 'var/uploads/backup';
    }
    */
    $backupdir=xarCoreGetVarDirPath()."/uploads";
    xarModSetVar('sitetools','adocachepath',xarCoreGetVarDirPath()."/cache/adodb");
    xarModSetVar('sitetools','rsscachepath', xarCoreGetVarDirPath()."/cache/rss");
    xarModSetVar('sitetools','templcachepath', xarCoreGetVarDirPath()."/cache/templates");
    xarModSetVar('sitetools','backuppath', $backupdir);
    xarModSetVar('sitetools','lineterm','\n');
    xarModSetVar('sitetools','timestamp',1);
    xarModSetVar('sitetools','colnumber',3);
    xarModSetVar('sitetools','defaultbktype','complete');
    xarModSetVar('sitetools','links_skiplocal',1);
    xarModSetVar('sitetools','links_method','GET');
    xarModSetVar('sitetools','links_follow',0);
    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ReadSiteToolsBlock', 'All', 'sitetools', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminSiteTools', 'All', 'sitetools', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return sitetools_upgrade('0.2.0');
}

/**
 * upgrade the example module from an old version
 * This function can be called multiple times
 */
function sitetools_upgrade($oldversion)
{
    /* Upgrade dependent on old version number */
    switch ($oldversion) {
        case 0.1:

            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();

            xarDBLoadTableMaintenanceAPI();

            $linkstable = $xartable['sitetools_links'];
            $query = xarDBCreateTable($linkstable,
                                     array('xar_id'         => array('type'        => 'integer',
                                                                    'null'       => false,
                                                                    'increment'  => true,
                                                                    'primary_key' => true),
                                           'xar_link'       => array('type'        => 'varchar',
                                                                    'size'        => 254,
                                                                    'null'        => false,
                                                                    'default'     => ''),
                                           'xar_status'     => array('type'        => 'integer',
                                                                    'null'        => false,
                                                                    'default'     => '0'),
        /* TODO: replace with unique id*/
                                           'xar_moduleid'   => array('type'        => 'integer',
                                                                    'unsigned'    => true,
                                                                    'null'        => false,
                                                                    'default'     => '0'),
                                           'xar_itemtype'   => array('type'        => 'integer',
                                                                    'unsigned'    => true,
                                                                    'null'        => false,
                                                                    'default'     => '0'),
                                           'xar_itemid'     => array('type'        => 'integer',
                                                                    'unsigned'    => true,
                                                                    'null'        => false,
                                                                    'default'     => '0'),
                                           'xar_itemtitle'  => array('type'        => 'varchar',
                                                                    'size'        => 254,
                                                                    'null'        => false,
                                                                    'default'     => ''),
                                           'xar_itemlink'   => array('type'        => 'varchar',
                                                                    'size'        => 254,
                                                                    'null'        => false,
                                                                    'default'     => ''),
                                          ));

            if (empty($query)) return; // throw back

            /* Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful */
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            /* allow several entries for the same link here */
            $index = array(
                'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_sitetools_links_link',
                'fields'    => array('xar_link'),
                'unique'    => false
            );
            $query = xarDBCreateIndex($linkstable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            /* allow several links for the same module item */
            $index = array(
                'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_sitetools_links_combo',
                'fields'    => array('xar_moduleid','xar_itemtype','xar_itemid'),
                'unique'    => false
            );
            $query = xarDBCreateIndex($linkstable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            /* allow many entries with the same status here */
            $index = array(
                'name'      => 'i_' . xarDBGetSiteTablePrefix() . '_sitetools_links_status',
                'fields'    => array('xar_status'),
                'unique'    => false
            );
            $query = xarDBCreateIndex($linkstable,$index);
            $result =& $dbconn->Execute($query);
            if (!$result) return;

            /* create the dynamic object that will represent our items */
            $objectid = xarModAPIFunc('dynamicdata','util','import',
                                      array('file' => 'modules/sitetools/sitetools_links.xml'));
            if (empty($objectid)) return;
            /* save the object id for later */
            xarModSetVar('sitetools','objectid_links',$objectid);
            /*update vars for backup tool*/
            xarModSetVar('sitetools','colnumber',3);
            xarModSetVar('sitetools','defaultbktype','complete');
        case '0.2':
        case '0.2.0':        
            xarModSetVar('sitetools','useftpbackup', false);
            xarModSetVar('sitetools','ftpserver', '');
            xarModSetVar('sitetools','ftpuser', '');
            xarModSetVar('sitetools','ftppw', '');
            xarModSetVar('sitetools','ftpdir', '');
        case '0.2.1':
            xarModSetVar('sitetools','usesftpbackup', false);
        break;
    }
    /* Update successful */
        return true;
}

/**
 * delete the example module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function sitetools_delete()
{
    /* Get datbase setup */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    xarDBLoadTableMaintenanceAPI();
    /* Generate the SQL to drop the table using the API */
    $query = xarDBDropTable($xartable['sitetools']);
    if (empty($query)) return;

    /* Drop the table and send exception if returns false. */
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    /* delete the dynamic object and its properties */
    $objectid = xarModGetVar('sitetools','objectid_links');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',
                      array('objectid' => $objectid));
        xarModDelVar('sitetools','objectid_links');
    }

    /* Generate the SQL to drop the table using the API */
    $query = xarDBDropTable($xartable['sitetools_links']);
    if (empty($query)) return;
    /* Drop the table and send exception if returns false. */
    $result = &$dbconn->Execute($query);
    if (!$result) return;

    /* Delete any sitetools module variables */
    xarModDelAllVars('sitetools');

    /* Remove Masks and Instances */
    xarRemoveMasks('sitetools');

    /* Deletion successful */
    return true;
}

?>