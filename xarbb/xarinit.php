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
    // TFTIME -- Time of the topic post itself
    $fields = array(
    'xar_tid'          => array('type'=>'integer','null'=>false,'increment'=>true,'primary_key'=>true),
    'xar_fid'          => array('type'=>'integer','null'=>false,'increment'=>false,'primary_key'=>false),
    'xar_ttitle'       => array('type'=>'varchar','size'=>255,'null'=>false,'default'=>''),
    'xar_tpost'        => array('type'=>'text'),
    'xar_tposter'      => array('type'=>'integer','null'=>false,'increment'=>false,'primary_key'=>false),
    'xar_ttime'        => array('type'=>'datetime','null'=>false, 'default'=>'1970-01-01 00:00'),
    'xar_tftime'       => array('type'=>'datetime','null'=>false, 'default'=>'1970-01-01 00:00'),
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
  /* $instances = array(
        array('header' => 'Forum ID:',
            'query' => "SELECT distinct xar_fid FROM " . $xbbforumstable,
            'limit' => 20
            ),
        array('header' => 'Forum Name:',
            'query' => "SELECT distinct xar_fname FROM ".$xbbforumstable,  // Todo
            'limit' => 20
            )
        ); */
    $instances = array(
                    array('header' => 'external', // this keyword indicates an external "wizard"
                    'query'  => xarModURL('xarbb', 'admin', 'privileges'),
                    'limit'  => 0
                   )
           );
    xarDefineInstance('xarbb', 'Forum', $instances);
    // Register Masks
    // Mask = Categorie : Id : Name
    xarRegisterMask('ReadxarBB','All','xarbb','Forum','All:All','ACCESS_READ');     // Allows reading Topics and Postings
    xarRegisterMask('ViewxarBB','All','xarbb','Read','All:All','ACCESS_OVERVIEW');	// Allows seeing Forum
    xarRegisterMask('EditxarBB','All','xarbb','Forum','All:All','ACCESS_EDIT');
    xarRegisterMask('AddxarBB','All','xarbb','Forum','All:All','ACCESS_ADD');
    xarRegisterMask('DeletexarBB','All','xarbb','Forum','All:All','ACCESS_DELETE');
    xarRegisterMask('AdminxarBB','All','xarbb','Forum','All:All','ACCESS_ADMIN');	// Allows all ;D
    xarRegisterMask('ModxarBB','All','xarbb','Forum','All:All','ACCESS_MODERATE');	// Allows Editing + Deleting Replys + Topics
    xarRegisterMask('PostxarBB','All','xarbb','Forum','All:All','ACCESS_COMMENT');  // Allows Posting Replys and Topics
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

    // do this stuff only once
    if (xarModGetVar('xarbb', 'hottopic')) return true;

    // Enable categories hooks for xarbb forums (= item type 1)
    xarModAPIFunc('modules','admin','enablehooks', array('callerModName'    => 'xarbb',
                                                         'callerItemType'   => 1,
                                                         'hookModName'      => 'categories'));


// xarbb is already calling the comments module directly via API, using itemtype 0 (for now ?)
// So you don't want to hook comments to itemtype 2 here as well...
// Dealing with comments once (either via API or via hooks) is enough :-)
/*
    // Enable comments hooks for xarbb topics (= item type 2)
    xarModAPIFunc('modules','admin','enablehooks', array('callerModName'    => 'xarbb',
                                                             'callerItemType'   => 2,
                                                             'hookModName'      => 'comments'));
*/

    // Enable hitcount hooks for xarbb topics (= item type 2)
    xarModAPIFunc('modules','admin','enablehooks', array('callerModName'    => 'xarbb',
                                                             'callerItemType'   => 2,
                                                             'hookModName'      => 'hitcount'));

    // Enable comment hooks for xarbb topics (= item type 2)
    xarModAPIFunc('modules','admin','enablehooks', array('callerModName'    => 'xarbb',
                                                             'callerItemType'   => 2,
                                                             'hookModName'      => 'comments'));

    // modvars
    xarModSetVar('xarbb', 'hottopic', 10);
    xarModSetVar('xarbb', 'redhottopic', 20);
    xarModSetVar('xarbb', 'topicsperpage', 50);
    // If your module supports short URLs, the website administrator should
    // be able to turn it on or off in your module administration
    xarModSetVar('xarbb', 'SupportShortURLs', 0);

    $xarbbcid = xarModAPIFunc('categories',
        'admin',
        'create',
        Array('name' => 'xarbb',
            'description' => 'XarBB Categories',
            'parent_id' => 0));
    // Assign category to item type 1 (= forums)
    // Note: you can have more than 1 mastercid (cfr. articles module)
    xarModSetVar('xarbb', 'number_of_categories.1', 1);
    xarModSetVar('xarbb', 'mastercids.1', $xarbbcid);
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

/**
 * upgrade the smiley module from an old version
 */
function xarbb_upgrade($oldversion)
{

    // Upgrade dependent on old version number
    switch ($oldversion) {

        // TODO: version numbers - normalise.
        case '.9':
            // Set up module hooks
            xarRegisterMask('ViewxarBB','All','xarbb','Forum','All:All','ACCESS_OVERVIEW');
            break;

        case '0.9.1.0':
            // Load database tables etc.
            xarModAPILoad('categories','user');

            // Get database information
            list($dbconn) = xarDBGetConn();
            $xartable = xarDBGetTables();
            $linkagetable = $xartable['categories_linkage'];

            // update item type in categories - you need to upgrade categories first :-)
            $modid = xarModGetIDFromName('xarbb');
            $update =  "UPDATE $linkagetable SET xar_itemtype = 1 WHERE xar_modid = $modid";
            $result =& $dbconn->Execute($update);
            if (!$result) return;
            return xarbb_upgrade('1.0.0');
            break;
        case '1.0':
            return xarbb_upgrade('1.0.0');
            break;
        case '1.0.0':
       // Get database information
            list($dbconn) = xarDBGetConn();
            $xartable = xarDBGetTables();
            $topicstable = $xartable['xbbtopics'];

             xarDBLoadTableMaintenanceAPI();
            // Update the topics table with a first post date tfpost field
           $query = xarDBAlterTable($topicstable,
                              array('command' => 'add',
                                    'field'   => 'xar_tftime',
                                    'type'    => 'datetime',
                                    'null'    => false,
                                    'default' => '1970-01-01 00:00'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return; 

            //Now let's update that field. The only data we have is either
            //ttime in the topic table - if there are no other post replies
            //or use the first post reply time - (or maybe user reg date?) - use first post for now.
            $dotopicstable=xarbb_updatetopicstable();
            if (!$dotopicstable)  return;

            break;
        default:
            break;
    }

    return true;
}

function xarbb_delete()
{
	//Let's first get all the forums
    $forums = xarModAPIFunc('xarbb','user','getallforums');

    //Now if there are forums, let's identify all the topics associated with each forum
    // and delete al the replies associated with each topic
    foreach($forums as $forum) {
           xarModAPIFunc('xarbb','user','deletealltopics',
                                  array('fid'=>$forum['fid']));
    }

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

    // Delete any module variables
    xarModDelVar('xarbb', 'SupportShortURLs');

    // Remove Masks and Instances
    xarRemoveMasks('xarbb');
    xarRemoveInstances('xarbb');

    return true;
}
/**
 * Update the new first post field introduced in v 1.0.1
 *
*/
function xarbb_updatetopicstable()
{
   $allforums = xarModAPIFunc('xarbb','user','getallforums');

   $forums=count($allforums);
       for ($i=0; $i <$forums; $i++) {
           $alltopics[$i]=xarModAPIFunc('xarbb','user','getalltopics',
                                  array('fid'=>$allforums[$i]['fid']));
       }
       list($dbconn) = xarDBGetConn();
       $xartable = xarDBGetTables();
       $xbbtopicstable = $xartable['xbbtopics'];
       foreach ($alltopics as $eachforum) {
           foreach ($eachforum as $eachtopic) {
               if ($eachtopic['treplies']==0) {

                   $query = "UPDATE $xbbtopicstable
                             SET xar_tftime = '" . $eachtopic['ttime'] . "'
                             WHERE xar_tid = " . $eachtopic['tid'];
                   $result =& $dbconn->Execute($query);
                   $result->Close();
                   if (!$result) return;
               } else {
                   $getfirstpost=xarModAPIFunc('comments','user','get_multiple',
                                         array('modid' => xarModGetIdFromName('xarbb'),
                                               'objectid' => $eachtopic['tid']));
                   //Let's put this in the correct date format for tftime 
                   //Must be datetime type format from int(11) format direct conversion
                   $newpostdate=date("Y-m-d H:i:s",$getfirstpost[0]['xar_datetime']);
                   $query = "UPDATE $xbbtopicstable
                             SET xar_tftime = '" . $newpostdate . "'
                             WHERE xar_tid = " . $eachtopic['tid'];
                   $result =& $dbconn->Execute($query);
                   $result->Close();
                   if (!$result) return;
               }
           }
       }

 return true;
}
?>
