<?php
/**
 * xarinit.php
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage My Bookmarks module
 * @author Scot Gardner
 * @version     $Id$
 */

/**
 * initialise the module.  This function is only ever called once during the
 * lifetime of a particular module instance
 */
function mybookmarks_init()
{


    $dbconn =& xarDBGetConn();
    $xartables =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();


    $mybookmarkstable = $xartables['mybookmarks'];

    $fields = array(

        'xar_bm_id'   =>  array(
            'type'          =>  'integer',
            'size'          =>  '8',
            'null'          =>  false,
            'increment'     =>  true, 'primary_key'   =>  true
            ),
        'xar_user_name'   =>  array(
            'type'          =>  'integer',
            'size'          =>  '11',
            'null'          =>  false,

            ),
        'xar_bm_name'   =>  array(
            'type'          =>  'varchar',
            'size'          =>  '255',
            'null'          =>  false,

            ),
        'xar_bm_url'   =>  array(
            'type'          =>  'varchar',
            'size'          =>  '255',
            'null'          =>  false,

            )
    );

    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($mybookmarkstable,$fields);
    if (empty($query)) return; // throw back

    // Pass the Table Create DDL to adodb to create the table and send exception if unsuccessful
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // INIDZES FOR THE TABLE
    $sitePrefix = xarDBGetSiteTablePrefix();


    // index for name searching
    $index = array(
        'name'      => 'i_' . $sitePrefix . 'mybookmarks_bm_name'
        ,'fields'   => array( 'xar_bm_name' )
        ,'unique'   => false
        );
    $query = xarDBCreateIndex( $xartables['mybookmarks'], $index );
    if (!$query) return;
    $result =& $dbconn->Execute($query);
    if (!$result) return;


    // MODULE WARIABLES FOR THIS TABLE
    xarModSetVar(
        'mybookmarks'
        ,'itemsperpage.1'
        ,10 );



    /*
     * REGISTER THE TABLES AT DYNAMICDATA
     */
    $objectid = xarModAPIFunc(
        'dynamicdata'
        ,'util'
        ,'import'
        ,array(
            'file'  => 'modules/mybookmarks/xarobject.xml'));
    if (empty($objectid)) return;

    /*
     * Module Variable for ShortURLSupport!
     */
    xarModSetVar(
        'mybookmarks'
        ,'SupportShortURLs'
        ,0 );

    /*
     * REGISTER BLOCKS
     */

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'mybookmarks',
                             'blockType'=> 'Bookmarks'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'mybookmarks',
                             'blockType'=> 'TopBookmarks'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'mybookmarks',
                             'blockType'=> 'RandomBookmarks'))) return;


    /*
     * REGISTER HOOKS
     */

    // Hook for module hitcount
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'hitcount'
            ,'callerModName'    => 'mybookmarks'));

    // Hook for module myxaraya
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'myxaraya'
            ,'callerModName'    => 'mybookmarks'));

    // Hook for module ratings
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'ratings'
            ,'callerModName'    => 'mybookmarks'));

    // Hook for module comments
    xarModAPIFunc(
        'modules'
        ,'admin'
        ,'enablehooks'
        ,array(
            'hookModName'       => 'comments'
            ,'callerModName'    => 'mybookmarks'));



    /*
     * REGISTER MASKS
     */

    // for module access
    xarRegisterMask( 'Viewmybookmarks' ,'All' ,'mybookmarks' ,'All' ,'All', 'ACCESS_OVERVIEW' );
    xarRegisterMask( 'Editmybookmarks' ,'All' ,'mybookmarks' ,'All' ,'All', 'ACCESS_EDIT' );
    xarRegisterMask( 'Addmybookmarks' ,'All' ,'mybookmarks' ,'All' ,'All', 'ACCESS_ADD' );
    xarRegisterMask( 'Adminmybookmarks' ,'All' ,'mybookmarks' ,'All' ,'All', 'ACCESS_ADMIN' );



    // Initialisation successful
    return true;
}

/**
 * Remove the module instance from the xaraya installation.
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance.
 */
function mybookmarks_delete()
{
    /*
     * REMOVE MODULE VARS
     */
    if ( !xarModDelAllVars( 'mybookmarks' ) )
        return;


    /*
     * UNREGISTER BLOCKS
     */

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'mybookmarks',
                             'blockType'=> 'Bookmarks'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'mybookmarks',
                             'blockType'=> 'TopBookmarks'))) return;

    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'mybookmarks',
                             'blockType'=> 'RandomBookmarks'))) return;


    /*
     * REMOVE MASKS AND INSTANCES
     */
    xarRemoveMasks( 'mybookmarks' );
    xarRemoveInstances( 'mybookmarks' );


    /*
     * REMOVE THE DATABASE TABLES AND DD OBJECTS
     */
    $dbconn =& xarDBGetConn();
    $xartables =& xarDBGetTables();

    // adodb does not provide the functionality to abstract table creates
    // across multiple databases.  Xaraya offers the xarDropeTable function
    // contained in the following file to provide this functionality.
    xarDBLoadTableMaintenanceAPI();


    // drop table mybookmarks .Generate the SQL to drop
    // the table using the API.
    $query = xarDBDropTable($xartables['mybookmarks']);
    if (empty($query)) return; // throw back

    // Drop the table and send exception if returns false.
    $result =& $dbconn->Execute($query);
    // TODO // CHECK
    if (!$result) return;

    // remove the table from dynamic data
    $objectinfo = xarModAPIFunc(
        'dynamicdata'
        ,'user'
        ,'getobjectinfo'
        ,array(
            'modid'     => xarModGetIDFromName('mybookmarks' )
            ,'itemtype' => 1 ));

    if (!isset($objectinfo) || empty($objectinfo['objectid'])) {
        return;
    }
    $objectid = $objectinfo['objectid'];

    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }


    // Deletion successful
    return true;
}



/**
 * upgrade the module from an older version.
 * This function can be called multiple times
 */
function mybookmarks_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
    case '0.1':
        // compatability upgrade
        break;
    }

    // Update successful
    return true;
}

/*
 * END OF FILE
 */
?>