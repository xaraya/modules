<?php
/**
 * File: $Id: s.xarinit.php 1.11 03/01/18 11:39:31-05:00 John.Cox@mcnabb. $
 *
 * Xaraya xarbb
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage xarbb Module
 * @author John Cox
*/

//Load Table Maintainance API
xarDBLoadTableMaintenanceAPI();

/**
 * initialise the xarbb module
 */
function xarbb_init()
{

    if((!xarModIsAvailable('categories')) || (!xarModIsAvailable('hitcount')) || (!xarModIsAvailable('comments'))) {
        $msg=xarML('The categories, comments, and hitcount module should be activated first');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION,'MODULE_DEPENDENCY',
                        new SystemException($msg));
        return;
    }

    // Set up database tables
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];

    // FID -- Forum ID
    // FNAME -- Name of forum
    // FDESC -- Description of forum
    // FTOPICS -- Number of topics in forum
    // FPOSTS -- TOTAL replies and posts
    // FPOSTER -- UID of the last poster of replies or topics
    // FPOSTID -- Time of the last reply

    $fields = array(
    'xar_fid'          => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
    'xar_fname'        => array('type'=>'varchar','size'=>255,'null'=>false,'default'=>''),
    'xar_fdesc'        => array('type'=>'text'),
    'xar_ftopics'      => array('type'=>'integer','null'=>false,'increment'=>false,'primary_key'=>false),
    'xar_fposts'       => array('type'=>'integer','null'=>false,'increment'=>false,'primary_key'=>false),
    'xar_fposter'      => array('type'=>'integer','null'=>false,'increment'=>false,'primary_key'=>false),
    'xar_fpostid'      => array('type'=>'datetime','null'=>false, 'default'=>'1970-01-01 00:00')
    );

    // TODO NEED FORUM STATUS

    $query = xarDBCreateTable($xbbforumstable,$fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $xbbtopicstable = $xartable['xbbtopics'];


    // TID -- Topic ID
    // FID -- Forum ID the topic belongs to
    // TTITLE -- The title of the topic
    // TPOST -- Text of the post
    // TPOSTER -- UID of the original topic
    // TTIME -- Time of the last reply or topic generation
    // TREPLIES -- Number of replies to topic
    // TREPLIER -- UID of the last replier
    // TSTATUS -- Special attributes of the topic (sticky, locked, etc)

    $fields = array(
    'xar_tid'          => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
    'xar_fid'          => array('type'=>'integer','null'=>false,'increment'=>false,'primary_key'=>false),
    'xar_ttitle'       => array('type'=>'varchar','size'=>255,'null'=>false,'default'=>''),
    'xar_tpost'        => array('type'=>'text'),
    'xar_tposter'      => array('type'=>'integer','null'=>false,'increment'=>false,'primary_key'=>false),
    'xar_ttime'        => array('type'=>'datetime','null'=>false, 'default'=>'1970-01-01 00:00'),
    'xar_treplies'     => array('type'=>'integer','null'=>false,'increment'=>false,'primary_key'=>false),
    'xar_treplier'     => array('type'=>'integer','null'=>false,'increment'=>false,'primary_key'=>false),
    'xar_tstatus'      => array('type'=>'integer','size'=>'tiny','null'=>FALSE,'default'=>'0')
    );

    //TODO ADD TREPLIES FOR NUMBER OF REPLIES

    $query = xarDBCreateTable($xbbtopicstable,$fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    //-----------------------------------------------------------------------------------
    // Forum
    $instances = array(
        array('header' => 'Forum ID:',
            'query' => "SELECT distinct xar_fid FROM " . $xbbforumstable,
            'limit' => 20
            ),
        array('header' => 'Forum Name:',
            'query' => "SELECT distinct xar_fname FROM ".$xbbforumstable,  // Todo
            'limit' => 20
            )
        );
    xarDefineInstance('xarbb', 'Forum', $instances);
    // Register Masks
    xarRegisterMask('ReadxarBB','All','xarbb','Forum','All:All','ACCESS_READ');     // Allows Posting Replys and Topics
    xarRegisterMask('EditxarBB','All','xarbb','Forum','All:All','ACCESS_EDIT');
    xarRegisterMask('AddxarBB','All','xarbb','Forum','All:All','ACCESS_ADD');
    xarRegisterMask('DeletexarBB','All','xarbb','Forum','All:All','ACCESS_DELETE');
    xarRegisterMask('AdminxarBB','All','xarbb','Forum','All:All','ACCESS_ADMIN');	// Allows all ;D
    xarRegisterMask('ModxarBB','All','xarbb','Forum','All:All','ACCESS_MODERATE');	// Allows Editing + Deleting Replys + Topics
    xarRegisterMask('ViewxarBB','All','xarbb','Forum','All:All','ACCESS_OVERVIEW'); // Allows seeing Forum + reading Forum
	// for what is moderate good?

 /*   //-----------------------------------------------------------------------------------
    // Topic
    $instances = array(
        array('header' => 'Topic ID:',
            'query' => "SELECT DISTINCT xar_tid FROM " . $xbbtopicstable,
            'limit' => 20
            )
        );
    xarDefineInstance('xarbb', 'Topic', $instances);
    // Register Masks
    xarRegisterMask('ReadxarBB','All','xarbb','Topic','All','ACCESS_READ');
    xarRegisterMask('EditxarBB','All','xarbb','Topic','All','ACCESS_EDIT');
    xarRegisterMask('AddxarBB','All','xarbb','Topic','All','ACCESS_ADD');
    xarRegisterMask('DeletexarBB','All','xarbb','Topic','All','ACCESS_DELETE');
    xarRegisterMask('AdminxarBB','All','xarbb','Topic','All','ACCESS_ADMIN');
    xarRegisterMask('ModxarBB','All','xarbb','Topic','All','ACCESS_MODERATE');    */
	// for what is moderate good?


    // Initialisation successful
    return true;
}

function xarbb_activate()
{
    // Enable categories hooks for xarbb forums (= item type 1)
    xarModAPIFunc('modules','admin','enablehooks', array('callerModName'    => 'xarbb',
                                                         'callerItemType'   => 1,
                                                         'hookModName'      => 'categories'));


    // Enable comments hooks for xarbb topics (= item type 2)
    xarModAPIFunc('modules','admin','enablehooks', array('callerModName'    => 'xarbb',
                                                             'callerItemType'   => 2,
                                                             'hookModName'      => 'comments'));

    // Enable hitcount hooks for xarbb topics (= item type 2)
    xarModAPIFunc('modules','admin','enablehooks', array('callerModName'    => 'xarbb',
                                                             'callerItemType'   => 2,
                                                             'hookModName'      => 'hitcount'));

    // modvars
    xarModSetVar('xarbb', 'hottopic', 10);
    xarModSetVar('xarbb', 'redhottopic', 20);
    xarModSetVar('xarbb', 'number_of_categories', 1);
    xarModSetVar('xarbb', 'topicsperpage', 50);

    $xarbbcid = xarModAPIFunc('categories',
        'admin',
        'create',
        Array('name' => 'xarbb',
            'description' => 'XarBB Categories',
            'parent_id' => 0));
    // Note: you can have more than 1 mastercid (cfr. articles module)
    xarModSetVar('xarbb', 'number_of_categories', 1);
    xarModSetVar('xarbb', 'mastercids', $xarbbcid);
    $xarbbcategories = array();
    $xarbbcategories[] = array('name' => "Forum Category One",
        'description' => "description one");
    $xarbbcategories[] = array('name' => "Forum Category Two",
        'description' => "description two");
    $xarbbcategories[] = array('name' => "Forum Category Three",
        'description' => "description three");
    foreach($xarbbcategories as $subcat) {
        $xabbsubcid = xarModAPIFunc('categories',
            'admin',
            'create',
            Array('name'        => $subcat['name'],
                'description'   => $subcat['description'],
                'parent_id'     => $xarbbcid));
    }

    return true;
}


function xarbb_delete()
{
    // Drop the table
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];
    $query = xarDBDropTable($xbbforumstable);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $xbbtopicstable = $xartable['xbbtopics'];
    $query = xarDBDropTable($xbbtopicstable);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Remove Masks and Instances
    xarRemoveMasks('xarbb');
    xarRemoveInstances('xarbb');

    return true;
}

?>