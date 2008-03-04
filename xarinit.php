<?php
/**
 * XarBB - A lightweight BB for Xaraya
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarbb Module
 * @link http://xaraya.com/index.php/release/300.html
 * @author John Cox, Mikespub, Jo Dalle Nogare
 */

include_once 'includes/xarDate.php';
/* Load Table Maintainance API */
xarDBLoadTableMaintenanceAPI();

/**
 * initialise the xarbb module
 */
function xarbb_init()
{
    /* Set up database tables */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];

    // FID -- Forum ID
    // FNAME -- Name of forum
    // FDESC -- Description of forum
    // FTOPICS -- Number of topics in forum
    // FPOSTS -- TOTAL replies and posts
    // FPOSTER -- UID of the last poster of replies or topics
    // FPOSTID -- Time of the last reply

    $fields = array(
    'xar_fid'          => array('type'=>'integer', 'null'=>false,'default'=>'0','increment'=>true,'primary_key'=>true),
    'xar_fname'        => array('type'=>'varchar', 'null'=>false,'default'=>'','size'=>255),
    'xar_fdesc'        => array('type'=>'text'),
    'xar_ftopics'      => array('type'=>'integer', 'null'=>false,'default'=>'0','increment'=>false,'primary_key'=>false),
    'xar_fposts'       => array('type'=>'integer', 'null'=>false,'default'=>'0','increment'=>false,'primary_key'=>false),
    'xar_fposter'      => array('type'=>'integer', 'null'=>false, 'default'=>'0', 'increment' => false, 'primary_key' => false),
    'xar_fpostid'      => array('type'=>'integer', 'unsigned'=>TRUE, 'null'=>FALSE, 'default'=>'0'),
    'xar_fstatus'      => array('type'=>'integer', 'null'=>false, 'default'=>'0', 'size'=>'tiny'),
    'xar_foptions'      => array('type'=>'text'),
    'xar_forder'        => array('type'=>'integer','null'=>false, 'default'=>'0','increment'=>false,'primary_key'=>false)
    //'xar_fpostid'      => array('type'=>'datetime','null'=>false,'default'=>'1970-01-01 00:00')
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
    'xar_tid'          => array('type'=>'integer', 'null'=>false, 'default' => '0' , 'increment' => true, 'primary_key' => true),
    'xar_fid'          => array('type'=>'integer', 'null'=>false, 'default'=>'0', 'increment'=> false,  'primary_key' => false),
    'xar_ttitle'       => array('type'=>'varchar', 'null'=>false, 'default'=>'','size'=>255 ),
    'xar_tpost'        => array('type'=>'text'),
    'xar_tposter'      => array('type'=>'integer', 'null'=>false, 'default'=> '0', 'increment' => false, 'primary_key' => false),
    'xar_thostname'    => array('type'=>'varchar',  'null' => FALSE,  'size'=>255),
    'xar_ttime'        => array('type'=>'integer', 'unsigned'=>TRUE, 'null'=>FALSE, 'default'=>'0'),
    'xar_tftime'       => array('type'=>'integer', 'unsigned'=>TRUE, 'null'=>FALSE, 'default'=>'0'),
    'xar_treplies'     => array('type'=>'integer', 'null'=>false, 'default' => '0', 'increment' => false, 'primary_key' => false),
    'xar_treplier'     => array('type'=>'integer', 'null'=>false, 'default'=>'0', 'increment' => false, 'primary_key' => false),
    'xar_tstatus'      => array('type'=>'integer', 'null'=>false, 'default'=>'0', 'size'=>'tiny'),
    'xar_toptions'     => array('type'=>'text')
    );

     $query = xarDBCreateTable($xbbtopicstable,$fields);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . $xbbtopicstable . '_fid',
                   'fields'    => array('xar_fid'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xbbtopicstable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . $xbbtopicstable . '_tstatus',
                   'fields'    => array('xar_tstatus'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xbbtopicstable,$index);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $index = array('name'      => 'i_' . $xbbtopicstable . '_tposter',
                   'fields'    => array('xar_tposter'),
                   'unique'    => FALSE);
    $query = xarDBCreateIndex($xbbtopicstable,$index);
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

    // Register Block types
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName' => 'xarbb',
                             'blockType' => 'latestposts'))) return;

    // Register Masks
    // Mask = Categorie : Id : Name
    xarRegisterMask('ViewxarBB','All','xarbb','Read','All:All','ACCESS_OVERVIEW');    // Allows seeing Forum
    xarRegisterMask('ReadxarBB','All','xarbb','Forum','All:All','ACCESS_READ');     // Allows reading Topics and Postings
    xarRegisterMask('PostxarBB','All','xarbb','Forum','All:All','ACCESS_COMMENT');  // Allows Posting Replys and Topics
    xarRegisterMask('ModxarBB','All','xarbb','Forum','All:All','ACCESS_MODERATE');    // Allows Editing + Deleting Replys + Topics
    xarRegisterMask('EditxarBB','All','xarbb','Forum','All:All','ACCESS_EDIT');
    xarRegisterMask('AddxarBB','All','xarbb','Forum','All:All','ACCESS_ADD');
    xarRegisterMask('DeletexarBB','All','xarbb','Forum','All:All','ACCESS_DELETE');
    xarRegisterMask('AdminxarBB','All','xarbb','Forum','All:All','ACCESS_ADMIN');    // Allows all ;D
    // for what is moderate good?

     // Enable categories hooks for xarbb forums
    if (xarModIsAvailable('categories')) {
        xarModAPIFunc('modules','admin','enablehooks',
                       array('callerModName' => 'xarbb', 'hookModName' => 'categories'));
    }
    // Enable hitcount hooks for xarbb forums
    if (xarModIsAvailable('hitcount')) {
        xarModAPIFunc('modules','admin','enablehooks',
                       array('callerModName' => 'xarbb','hookModName' => 'hitcount'));
    }

    // Note: comments should not be hooked to xarbb - it is accessed directly via APIs

    // User preferences
    xarModSetVar('xarbb', 'autosubscribe', 'none');
    // modvars
    xarModSetVar('xarbb', 'cookiename', 'xarbb');
    xarModSetVar('xarbb','masternntpsetting',false); //added 2006-04-09 : see admin new.php function
    xarModSetVar('xarbb', 'cookiepath', '/');
    xarModSetVar('xarbb', 'cookiedomain', '');
    xarModSetVar('xarbb', 'forumsperpage', 20); //only need this for admin view
    // If your module supports short URLs, the website administrator should
    // be able to turn it on or off in your module administration
    xarModSetVar('xarbb', 'SupportShortURLs', 0);
    // xarModSetVar('xarbb', 'allowhtml', 1);
    xarModSetVar('xarbb', 'useModuleAlias',false);
    xarModSetVar('xarbb','aliasname','');
    // default settings for xarbb
    $settings = array();
    $settings['postsperpage']       = 20;
    $settings['topicsperpage']      = 20;
    $settings['hottopic']           = 20;
    $settings['editstamp']          = 0;
    $settings['allowhtml']          = false;
    $settings['allowbbcode']        = false;
    $settings['showcats']           = false;
    $settings['linknntp']           = false;
    $settings['nntpport']           = 119;
    $settings['nntpserver']         = 'news.xaraya.com';
    $settings['nntpgroup']          = 'xaraya.test';

    //Create the default categories for xarbb
    $xarbbcid = xarModAPIFunc('categories','admin','create',
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
    //Set default settings
    xarModSetVar('xarbb', 'settings', serialize($settings));

    if (!xarModRegisterHook('item', 'create', 'API', 'xarbb', 'admin', 'createhook')) {
        return false;
    }

    if (!xarModRegisterHook('item', 'new', 'GUI', 'xarbb', 'admin', 'newhook')) {
        return false;
    }

    // search hook
    if (!xarModRegisterHook('item', 'search', 'GUI', 'xarbb', 'user', 'search')) {
        return false;
    }

    return true;
}

/**
 * upgrade xarbb from an earlier version
 */
function xarbb_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {

        // TODO: version numbers - normalise.
        case '.9':
            // Set up module hooks
            xarRegisterMask('ViewxarBB','All','xarbb','Forum','All:All','ACCESS_OVERVIEW');
            // fall through to next upgrade

        case '0.9.1.0':
            // Load database tables etc.
            xarModAPILoad('categories','user');

            // Get database information
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $linkagetable = $xartable['categories_linkage'];

            // update item type in categories - you need to upgrade categories first :-)
            $modid = xarModGetIDFromName('xarbb');
            $update =  "UPDATE $linkagetable SET xar_itemtype = 1 WHERE xar_modid = $modid";
            $result =& $dbconn->Execute($update);
            if (!$result) return;
            // fall through to next upgrade

        case '1.0':
        case '1.0.0':
        // New modvars
            xarModSetVar('xarbb', 'forumsperpage', 20);
            xarModSetVar('xarbb', 'postsperpage', 20);
       // Get database information
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
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
            // fall through to next upgrade

        case '1.0.1':
            //<jojodee> Start of upgrade function and conversion of date fields
            //<jojodee> TODO: is working, still requires some additional error checking/except. messages
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $topicstable = $xartable['xbbtopics'];
            $xbbforumstable = $xartable['xbbforums'];

             xarDBLoadTableMaintenanceAPI();

            $query = xarDBAlterTable($topicstable,
                              array('command' => 'add',
                                    'field'   => 'xar_ntftime',
                                    'type'    => 'integer',
                                    'unsigned'=> true,
                                    'null'    => false,
                                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;

            $query = xarDBAlterTable($topicstable,
                              array('command' => 'add',
                                    'field'   => 'xar_nttime',
                                    'type'    => 'integer',
                                    'unsigned'=> true,
                                    'null'    => false,
                                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;
            //Now the forums table
            $query = xarDBAlterTable($xbbforumstable,
                              array('command' => 'add',
                                    'field'   => 'xar_nfpostid',
                                    'type'    => 'integer',
                                    'unsigned'=> true,
                                    'null'    => false,
                                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.

            $result = &$dbconn->Execute($query);
            if (!$result) return;
            //convert both topics and forum dates
            $converttopicstable=xarbb_convertdates();
            if (!$converttopicstable)  return;
            //move to new fields and drop temp fields
            $coyptopicdates=xarbb_copydates();
            if (!$coyptopicdates)  return;
            // fall through to next upgrade

        case '1.0.2':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $topicstable = $xartable['xbbforums'];

             xarDBLoadTableMaintenanceAPI();
            // Update the topics table with a first post date tfpost field
           $query = xarDBAlterTable($topicstable,
                              array('command' => 'add',
                                    'field'   => 'xar_fstatus',
                                    'type'    => 'integer',
                                    'null'    => false,
                                    'default' => '0',
                                    'size'    => 'tiny'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;
            // fall through to next upgrade

        case '1.0.3':
            xarModSetVar('xarbb', 'allowhtml', 1);
            // fall through to next upgrade

        case '1.0.4':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $topicstable = $xartable['xbbtopics'];

             xarDBLoadTableMaintenanceAPI();
            // Update the topics table with a first post date tfpost field
           $query = xarDBAlterTable($topicstable,
                              array('command' => 'add',
                                    'field'   => 'xar_thostname',
                                    'type'    => 'varchar',
                                    'null'    => false,
                                    'size'    => '255',
                                    'default' => ''));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;
            // fall through to next upgrade

        case '1.0.5':
                $forums = xarModAPIFunc('xarbb','user','getallforums');
                //Need to start the settings
                $settings = array();
                $settings['postsperpage']       = 20;
                $settings['topicsperpage']      = 20;
                $settings['forumsperpage']      = 20;
                $settings['hottopic']           = 20;
                $settings['editlink']           = 0;
                $settings['allowhtml']          = false;
                $settings['allowbbcode']        = false;
                $settings['showcats']           = false;
                $settings['linknntp']           = false;
                $settings['nntpport']           = 119;
                $settings['nntpserver']         = 'news.xaraya.com';
                $settings['nntpgroup']          = 'xaraya.test';
                foreach($forums as $forum) {
                    xarModSetVar('xarbb', 'settings.'.$forum['fid'], serialize($settings));
                }
            // fall through to next upgrade

        case '1.0.6':
            xarModDelVar('xarbb', 'hottopic');
            xarModDelVar('xarbb', 'redhottopic');
            xarModDelVar('xarbb', 'topicsperpage');
            xarModDelVar('xarbb', 'postsperpage');
            xarModDelVar('xarbb', 'allowhtml');

            xarModSetVar('xarbb', 'cookiename', 'xarbb');
            xarModSetVar('xarbb', 'cookiepath', '/');
            xarModSetVar('xarbb', 'cookiedomain', '');
            xarModSetVar('xarbb', 'forumsperpage', 50);
            // fall through to next upgrade

        case '1.0.7':
            //catlinkage table updated with itemtype 2 - not required now
            // fall through to next upgrade

        case '1.0.8':
            //Update the new default cid
            $oldcatno =xarModGetVar('xarbb','number_of_categories.1');
            if (isset($oldcatno) && !empty($oldcatno)) {
                xarModSetVar('xarbb','number_of_categories',$oldcatno);
            }
            $oldbasecids= xarModGetVar('xarbb','mastercids.1');
            if (isset($oldbasecids) && !empty($oldbasecids)) {
                xarModSetVar('xarbb','mastercids',$oldbasecids);
            }
            //let's clean up the catlinkage table
            //And make sure all forums have a catlink entry for each existing forum
            //in prior versions hooks were not consistently called and mixed itemtypes added
            //TODO
            $cleanupxarbb=xarbb_cleanitemtypes();
                if (!$cleanupxarbb)  return;
            // fall through to next upgrade

        case '1.0.9':
            if (!xarModRegisterHook('item', 'create', 'API',
                                   'xarbb', 'admin', 'createhook')) {
                return false;
            }

            if (!xarModRegisterHook('item', 'new', 'GUI',
                                   'xarbb', 'admin', 'newhook')) {
                return false;
            }
            // fall through to next upgrade

        case '1.1.0':
        case '1.1.1':
            // search hook
            if (!xarModRegisterHook('item', 'search', 'GUI', 'xarbb', 'user', 'search')) {
                return false;
            }
            // fall through to next upgrade

        case '1.1.2':
            // Load database tables etc.
            xarModAPILoad('comments','user');

            // Get database information
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $topicstable = $xartable['xbbtopics'];
            $commentstable = $xartable['comments'];

            // update item type of replies in comments, based on forum id
            $query = "SELECT DISTINCT xar_objectid, xar_fid
                      FROM $commentstable
                      LEFT JOIN $topicstable
                      ON xar_objectid = xar_tid
                      WHERE xar_modid = ? AND xar_itemtype = ?";
            $modid = xarModGetIDFromName('xarbb');
            $bindvars = array((int)$modid,0);
            $result =& $dbconn->Execute($query,$bindvars);
            if (!$result) return;

            $update = "UPDATE $commentstable
                       SET xar_itemtype = ?
                       WHERE xar_modid = ? AND xar_itemtype = ? AND xar_objectid = ?";
            while (!$result->EOF) {
                list($objectid,$fid) = $result->fields;
                $bindvars = array((int)$fid,(int)$modid,0,(int)$objectid);
                $dbconn->Execute($update,$bindvars);
                $result->MoveNext();
            }
            $result->Close();
            // fall through to next upgrade
        case '1.1.3':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $xbbforumstable = $xartable['xbbforums'];

            xarDBLoadTableMaintenanceAPI();
            // Update the topics table with a first post date tfpost field
            $query = xarDBAlterTable($xbbforumstable,
                              array('command' => 'add',
                                    'field'   => 'xar_foptions',
                                    'type'    => 'text'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;
            // fall through to next upgrade
        case '1.1.4':
            $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $xbbforumstable = $xartable['xbbtopics'];
            xarDBLoadTableMaintenanceAPI();
            $query = xarDBAlterTable($xbbforumstable,
                              array('command' => 'add',
                                    'field'   => 'xar_toptions',
                                    'type'    => 'text'));
            $result = &$dbconn->Execute($query);
            if (!$result) return;
            // fall through to next upgrade
        case '1.1.5':
            $modversion['name'] = 'xarbb';
            // fall through to next upgrade

        case '1.1.6':
            // Set up database tables
           $dbconn =& xarDBGetConn();
            $xartable =& xarDBGetTables();
            $xbbforumstable = $xartable['xbbforums'];
            xarDBLoadTableMaintenanceAPI();
            $query = xarDBAlterTable($xbbforumstable,
                              array('command' => 'add',
                                    'field'   => 'xar_forder',
                                    'type'    => 'integer'));
            $result = &$dbconn->Execute($query);
            $result->Close();

            $query2= "SELECT COUNT(1)
                    FROM $xbbforumstable";
            $result2 =& $dbconn->Execute($query2);
            if (!$result2) return;

            for (; !$result2->EOF; $result2->MoveNext()) {
                $fquery = "UPDATE $xbbforumstable
                           SET xar_forder    = xar_fid";

                 $result3 =& $dbconn->Execute($fquery);
                 if (!$result3) return;
            }
             $result2->Close();
             $result3->Close();
            // fall through to next upgrade

        case '1.1.7':
            // In this version (1.2.1) we introduce a new way to handle
            // topic tracking. Some module variables need to be created
            // for each forum.

            // Remove old module variables we no longer need.
            xarModDelVar('xarbb', 'cookiename');
            xarModDelVar('xarbb', 'cookiepath');
            xarModDelVar('xarbb', 'cookiedomain');

            // For each forum that already exists, create a set of module
            // variables for user tracking.
            $all_forums = xarModAPIFunc('xarbb', 'user', 'getallforums');
            foreach($all_forums as $forum) {
                $fid = $forum['fid'];
                // Last visited time.
                xarModSetVar('xarbb', 'f_' . $fid, '0');
                // Topic tracking array.
                $topic_tracking = array();
                xarModSetVar('xarbb', 'topics_' . $fid, serialize($topic_tracking));
            }

            // Fall through to next upgrade version.

        case '1.2.1':
            // In this version (1.2.2) we introduce a new way to handle
            // last visit time. Some module variables need to be created
            // for each forum.
            // For each forum that already exists, create a set of module
            // variables for user tracking.
            $all_forums = xarModAPIFunc('xarbb', 'user', 'getallforums');
            foreach($all_forums as $forum) {
                $fid = $forum['fid'];
                // Last visited time.
                xarModSetVar('xarbb', 'fr_' . $fid, '0');
            }

        case '1.2.2':
            // In this version (1.3.1) we have introduced a new user preferences page
            // As it stands this includes just the one user variable
            xarModSetVar('xarbb', 'autosubscribe', 'none');

            // Fall through to next upgrade version

        default:
            break;
    }

    return true;
}

function xarbb_delete()
{
    /* Let's first get all the forums */
    $forums = xarModAPIFunc('xarbb','user','getallforums');

    /* Now if there are forums, let's identify all the topics associated with each forum
     * and delete all the replies associated with each topic
     */
    foreach($forums as $forum) {
           xarModAPIFunc('xarbb','admin','deletealltopics',
                                  array('fid'=>$forum['fid']));
    }

    /* Drop the table */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];
    $query = xarDBDropTable($xbbforumstable);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $xbbtopicstable = $xartable['xbbtopics'];
    $query = xarDBDropTable($xbbtopicstable);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
   /* Remove any module aliases before deleting module vars */
    $aliasname =xarModGetVar('xarbb','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='xarbb')){
        xarModDelAlias($aliasname,'xarbb');
    }
    // Delete any module variables
    xarModDelAllVars('xarbb');
    // Remove Masks and Instances
    xarRemoveMasks('xarbb');
    xarRemoveInstances('xarbb');

    if (!xarModUnregisterHook('item', 'create', 'API', 'xarbb', 'admin', 'createhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'new', 'GUI', 'xarbb', 'admin', 'newhook')) {
        return false;
    }
    if (!xarModUnregisterHook('item', 'search', 'GUI', 'xarbb', 'user', 'search')) {
        return false;
    }

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
       $dbconn =& xarDBGetConn();
       $xartable =& xarDBGetTables();
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
                   if (!$result) return;
                   $result->Close();
               }
           }
       }

 return true;
}

/**
 * Copy fields from temp fields to new fields
 * Due to change from datetime to integer field types for topic table dates in 1.0.2
 * Routine for forum and topics table
*/

function xarbb_copydates()
{
       $dbconn =& xarDBGetConn();
       $xartable =& xarDBGetTables();
       $xbbtopicstable = $xartable['xbbtopics'];
       $xbbforumstable = $xartable['xbbforums'];

       $query= "SELECT COUNT(1)
                    FROM $xbbtopicstable";
       $result =& $dbconn->Execute($query);
       if (!$result) return;

       for (; !$result->EOF; $result->MoveNext()) {

           $docopy = "UPDATE $xbbtopicstable
                      SET xar_ttime    = xar_nttime,
                          xar_tftime   = xar_ntftime";
           $doupdate =& $dbconn->Execute($docopy);
           if (!$doupdate) return;
       }

       //Now do forums table
       $query= "SELECT COUNT(1)
                    FROM $xbbforumstable";
       $result =& $dbconn->Execute($query);
       if (!$result) return;

       for (; !$result->EOF; $result->MoveNext()) {

           $docopy = "UPDATE $xbbforumstable
                      SET xar_fpostid  = xar_nfpostid";
           $doupdate =& $dbconn->Execute($docopy);
           if (!$doupdate) return;
       }

       //Drop the temp fields
       $query="ALTER TABLE $xbbtopicstable DROP xar_nttime";
       // Pass to ADODB, and send exception if the result isn't valid.
       $result = &$dbconn->Execute($query);
       if (!$result) return;

       $query="ALTER TABLE $xbbtopicstable DROP xar_ntftime";
      // Pass to ADODB, and send exception if the result isn't valid.
       $result = &$dbconn->Execute($query);
       if (!$result) return;

       //Drop the temp fields in forums table
       $query="ALTER TABLE $xbbforumstable DROP xar_nfpostid";
       // Pass to ADODB, and send exception if the result isn't valid.
       $result = &$dbconn->Execute($query);
       if (!$result) return;


       $result->close();
 return true;
}

/**
 * Convert topic table ttime and tftime datetime fields to integer and copy to temporary fields
 * Due to change from datetime to integer field types for topic table dates in 1.0.2
*/
function xarbb_convertdates()
{
       $dbconn =& xarDBGetConn();
       $xartable =& xarDBGetTables();
       $xbbtopicstable = $xartable['xbbtopics'];
       $xbbforumstable = $xartable['xbbforums'];
       //First do the topics table
       $tottopics = "SELECT xar_tid,xar_ttime,xar_tftime,xar_nttime,xar_ntftime
                     FROM $xbbtopicstable";
       $result =& $dbconn->Execute($tottopics);
       if (!$result) return;

       for (; !$result->EOF; $result->MoveNext()) {
          list($tid,$ttime, $tftime, $nttime, $ntftime) = $result->fields;

          // Covert the first field data
          $thisdate = new xarDate();
          $thisdate->DBtoTS($ttime);
          $newttime=$thisdate->timestamp;
          $anotherdate = new xarDate();
          $anotherdate->DBtoTS($tftime);
          $newtftime=$anotherdate->timestamp;
          //Make sure fields aren't null - recent changes will cause error

          if (!isset($newttime) || empty($newttime)) {
              $newttime=0;
          }
          if (!isset($newtftime) || empty($newtftime)) {
              $newtftime=0;
          }
          //Copy to temp fields
          $docopy = "UPDATE $xbbtopicstable
                      SET xar_nttime = $newttime,
                          xar_ntftime= $newtftime
                     WHERE xar_tid   = $tid";
           $doupdate =& $dbconn->Execute($docopy);
           if(!$doupdate) return;
       }
       //Drop both the original fields now we have them copied, and recreate them clean
       $query="ALTER TABLE $xbbtopicstable DROP xar_ttime";
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;

       $query="ALTER TABLE $xbbtopicstable DROP xar_tftime";
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;
       $query = xarDBAlterTable($xbbtopicstable,
                              array('command' => 'add',
                                    'field'   => 'xar_tftime',
                                    'type'    => 'integer',
                                    'unsigned'=> true,
                                    'null'    => false,
                                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;

        $query = xarDBAlterTable($xbbtopicstable,
                              array('command' => 'add',
                                    'field'   => 'xar_ttime',
                                    'type'    => 'integer',
                                    'unsigned'=> true,
                                    'null'    => false,
                                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;
           $result->Close();

        //Now do the same for forums table
        $totforums = "SELECT xar_fid, xar_fpostid, xar_nfpostid
                     FROM $xbbforumstable";
        $result =& $dbconn->Execute($totforums);
        if (!$result) return;

        for (; !$result->EOF; $result->MoveNext()) {
          list($fid, $fpostid, $nfpostid) = $result->fields;
          //Make sure fields aren't null - recent changes will cause error

          // Covert the first field data
          $thisdate = new xarDate();
          $thisdate->DBtoTS($fpostid);
          $newfposttime=$thisdate->timestamp;
          if (!isset($newfposttime) || empty($newfposttime)) {
              $newfposttime=0;
          }

          //Copy to temp fields
          $docopy = "UPDATE $xbbforumstable
                      SET xar_nfpostid = $newfposttime
                     WHERE xar_fid   = $fid";
           $doupdate =& $dbconn->Execute($docopy);
           if(!$doupdate) return;
        }

        $query="ALTER TABLE $xbbforumstable DROP xar_fpostid";
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;

        $query = xarDBAlterTable($xbbforumstable,
                              array('command' => 'add',
                                    'field'   => 'xar_fpostid',
                                    'type'    => 'integer',
                                    'unsigned'=> true,
                                    'null'    => false,
                                    'default' => '0'));
            // Pass to ADODB, and send exception if the result isn't valid.
            $result = &$dbconn->Execute($query);
            if (!$result) return;
           $result->Close();

    return true;
}

//Cleanout catlinkage table so only xarbb itemtypes of 0 remain
function xarbb_cleanitemtypes()
{   //We have to assume here everyone has been working with itemtype = 1 for forums
    //As has been setin upgrade in 0.9 above
    //May also be junk with itemtypes > 0
    $xbbid=xarModGetIDFromName('xarbb');
    // Update catlinkage table so all entries of itemtype 1 are are itemtype 0
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $catlinkage =  xarDBGetSiteTablePrefix() . '_categories_linkage';

    $updatetypes = "UPDATE $catlinkage SET xar_itemtype = 0
                    WHERE  xar_modid= $xbbid AND xar_itemtype =1";
    $result =& $dbconn->Execute($updatetypes);
    if (!$result) return;
    //Remove all catlinkage entries where itemtype >0 for xarbb
    $removeoldtypes = "DELETE FROM $catlinkage
                   WHERE  xar_modid = $xbbid AND xar_itemtype > 0";
    $result =& $dbconn->Execute($removeoldtypes);
    if (!$result) return;

    $result->Close();
    return true;
}

?>
