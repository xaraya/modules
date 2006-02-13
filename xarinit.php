<?php
/**
 * Legis initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Initialise the module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 *
 * @author Jo Dalle Nogare
 */
function legis_init()
{
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    /* Create the table to hold the master document definitions */
    $LegisMasterTable = $xarTables['legis_master'];

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $fields = "xar_mdid      I         AUTO       PRIMARY,
               xar_mdname    C(100)    NotNull    DEFAULT '',
               xar_mdorder   L         NotNull    DEFAULT 0,
               xar_mddef     X         NotNull    DEFAULT ''
              ";
    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($LegisMasterTable, $fields);
    if (!$result) {return;}

    /* Create the table to hold the doclet definitions */
    $LegisDocletsTable = $xarTables['legis_doclets'];
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $fields = "xar_did      I         AUTO       PRIMARY,
               xar_dname    C(100)    NotNull    DEFAULT '',
               xar_dlabel   C(100)    NotNull    DEFAULT '',
               xar_dlabel2  C(100)    NotNull    DEFAULT '',
               xar_ddef     X         NotNull    DEFAULT ''
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($LegisDocletsTable, $fields);
    if (!$result) {return;}

    /* Create the table to hold the compiled documents */
    $LegisCompiledTable = $xarTables['legis_compiled'];
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $fields = "xar_cdid         I         AUTO       PRIMARY,
               xar_mdid         I         NotNull    Default 0,
               xar_cdnum        I         NotNull    DEFAULT 0,
               xar_cdtitle      C(250)    NotNull    DEFAULT '',
               xar_docstatus    I         NotNull    DEFAULT 0,
               xar_votestatus   I         NotNull    DEFAULT 0,
               xar_vetostatus   I         NotNull    DEFAULT 0,
               xar_submitdate   I         NotNull    DEFAULT 0,
               xar_submitter    C(100)    NotNull    DEFAULT '',
               xar_reviewdate   I         NotNull    DEFAULT 0,
               xar_passdate     I         NotNull    DEFAULT 0,
               xar_vetodate     I         NotNull    DEFAULT 0,
               xar_archivedate  I         NotNull    DEFAULT 0,
               xar_archswitch   L         NotNull    DEFAULT 0,
               xar_contributors X         NotNull    DEFAULT '',
               xar_doccontent   X         NotNull    DEFAULT '',
               xar_dochall      I         NotNull    DEFAULT 0,
               xar_pubnotes     X         NotNull    DEFAULT ''
              ";

    /* Create or alter the table as necessary */
    $result = $datadict->changeTable($LegisCompiledTable, $fields);
    if (!$result) {return;}

    /* Create an index for compiled docs */
    $result = $datadict->createIndex(
        'i_' . xarDBGetSiteTablePrefix() . '_legis_compiled_num',
        $LegisCompiledTable,
        'xar_mdid,xar_cdnum',
        'UNIQUE'
    );
    if (!$result) {return;}

   /* Create some default Doclets*/
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $LegisDocletsTable = $xarTables['legis_doclets'];
    $query = "INSERT INTO $LegisDocletsTable (xar_did, xar_dname, xar_dlabel, xar_dlabel2, xar_ddef) VALUES (1, 'whereas', 'Whereas', 'Whereas', ' ')";
    $result =& $dbconn->Execute($query);
    $query = "INSERT INTO $LegisDocletsTable (xar_did, xar_dname, xar_dlabel, xar_dlabel2, xar_ddef) VALUES (2, 'beitenacted', 'Be it therefore enacted', 'Be it further enacted', ' ')";
    $result =& $dbconn->Execute($query);
    $query = "INSERT INTO $LegisDocletsTable (xar_did, xar_dname, xar_dlabel, xar_dlabel2, xar_ddef) VALUES (3, 'beitresolved', 'Be it therefore resolved', 'Be it further resolved', ' ')";
    $result =& $dbconn->Execute($query);
    $query = "INSERT INTO $LegisDocletsTable (xar_did, xar_dname, xar_dlabel, xar_dlabel2, xar_ddef) VALUES (4, 'beitamended', 'Be it therefore amended', 'Be it further amended', ' ')";
    $result =& $dbconn->Execute($query);

    /* Create some default legislation Types */
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    $LegisMasterTable = $xarTables['legis_master'];
    //Create some default records
    $query = "INSERT INTO $LegisMasterTable (
               xar_mdid ,
               xar_mdname ,
               xar_mdorder ,
               xar_mddef )
               VALUES (1, 'Resolution', 0, 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"3\";}')";
   
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    $query = "INSERT INTO $LegisMasterTable (
               xar_mdid ,
               xar_mdname ,
               xar_mdorder ,
               xar_mddef )
               VALUES (2, 'Bill', 0, 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"2\";}')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $query = "INSERT INTO $LegisMasterTable (
               xar_mdid ,
               xar_mdname ,
               xar_mdorder ,
               xar_mddef )
               VALUES (3, 'Amendment', 0, 'a:2:{i:0;s:1:\"1\";i:1;s:1:\"4\";}')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;


    /* If Categories API loaded and available, generate proprietary
     * module master category cid and child subcids
     */
    if (xarModIsAvailable('categories')) {
        xarModAPIFunc('modules','admin','enablehooks',
                  array('callerModName' => 'legis', 'hookModName' => 'categories'));

        $legiscid = xarModAPIFunc('categories','admin','create',
            Array('name' => 'Legislation',
                  'description' => 'Legislation Halls',
                  'parent_id' => 0));
        /* Note: you can have more than 1 mastercid (cfr. articles module) */
        xarModSetVar('legis', 'number_of_categories', 1);
        xarModSetVar('legis', 'mastercids', $legiscid);
        $legiscategories = array();
        $legiscategories[] = array('name' => "Hall1",
            'description' => "Hall 1 description one");
        $legiscategories[] = array('name' => "Hall2",
            'description' => "Hall2 description two");
        $legiscategories[] = array('name' => "Hall3",
            'description' => "Hall3 description three");
        foreach($legiscategories as $subcat) {
            $legissubcid = xarModAPIFunc('categories','admin','create',
                Array('name'        => $subcat['name'],
                      'description' =>$subcat['description'],
                      'parent_id'   => $legiscid));
        }
    }


      //Let's make a default Roles 'Executive' Groups and set it in default
    xarMakeGroup('LegisExecutives');
    xarMakeGroup('LegisAdmins');
    //Get the default User Group
    $defaultgroup  = xarModGetVar('roles','defaultgroup');
    //Put the Executives and Admins in the defaultgroup
    xarMakeRoleMemberByUName('LegisExecutives',$defaultgroup);
    xarMakeRoleMemberByUName('LegisAdmins',$defaultgroup);
    $newexecs = xarModAPIFunc('roles','user','get',
                                     array('uname'  => 'LegisExecutives',
                                           'type'   => 1));
    $newexec=$newexecs['uid'];
    //get some default for the default hall else we run into problems in setup
    $halls=xarModApiFunc('categories','user','getchildren',array('cid'=>$legiscid));
    $hallids=array();
    foreach ($halls as $k=>$v) {
        $hallids[]=$v['cid'];
    }
    xarModSetVar('legis', 'itemsperpage', 10);
    xarModSetVar('legis', 'SupportShortURLs', 0);
    xarModSetVar('legis', 'useModuleAlias',false);
    xarModSetVar('legis', 'aliasname','');
    xarModSetVar('legis', 'defaultmaster',1);
    xarModSetVar('legis', 'defaulthall',$hallids[1]);
    xarModSetVar('legis', 'moderatorgroup',$newexec);
    xarModSetVar('legis', 'allowhallchange',false);  
    xarModSetVar('legis', 'docname','Legislation');          
    // Register Block types (this *should* happen at activation/deactivation)
    if (!xarModAPIFunc('blocks','admin','register_block_type',
                 array('modName' => 'legis',
                       'blockType' => 'latest'))) return;
  if (!xarModAPIFunc('blocks','admin','register_block_type',
                 array('modName' => 'legis',
                       'blockType' => 'adminlinks'))) return;
     /* Register our hooks that we are providing to other modules.  The Legis
     * module shows an Legis hook in the form of the user menu.
     */
     
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
            'legis', 'user', 'usermenu')) {
        return false;
    }
    //Now enable the hook for roles
    xarModAPIFunc('modules','admin','enablehooks',
                  array('callerModName' => 'roles', 
                        'hookModName' => 'legis'));
    /* Register search hook */
    if (!xarModRegisterHook('item', 'search', 'GUI', 'legis', 'user', 'search')) {
        return false;
    }
      //Now enable the hook for search if search module is installed
    if (xarModIsAvailable('search')) {
       xarModAPIFunc('modules','admin','enablehooks',
                  array('callerModName' => 'search',
                        'hookModName' => 'legis'));
    }
    /**
     * import our example object definition and properties from XML file (

     $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/legis/legislation.xml'));
    if (empty($objectid)) return;

    xarModSetVar('dyn_example','objectid',$objectid);


    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => 'modules/legis/legislation.data.xml'));
    if (empty($objectid)) return;

    */

    $query1 = "SELECT DISTINCT xar_cdtitle FROM " . $LegisCompiledTable;
    $query2 = "SELECT DISTINCT xar_cdnum FROM " . $LegisCompiledTable;
    $query3 = "SELECT DISTINCT xar_cdid FROM " . $LegisCompiledTable;
    $instances = array(
        array('header' => 'Legislation Name:',
            'query' => $query1,
            'limit' => 20
            ),
        array('header' => 'Legislation Number:',
            'query' => $query2,
            'limit' => 20
            ),
        array('header' => 'Legislation ID:',
            'query' => $query3,
            'limit' => 20
            )
        );
    xarDefineInstance('Legis', 'Item', $instances);
    /* You can also use some external "wizard" function to specify instances :

      $instances = array(
          array('header' => 'external', // this keyword indicates an external "wizard"
                'query'  => xarModURL('Legis','admin','privileges',array('foo' =>'bar')),
                'limit'  => 0
          )
      );
      xarDefineInstance('Legis', 'Item', $instances);
     
     */
    $instancestable = $xarTables['block_instances'];
    $typestable = $xarTables['block_types'];
    $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'Legis'";
    $instances = array(
        array('header' => 'Legis Block Title:',
              'query' => $query,
              'limit' => 20
            )
        );
    xarDefineInstance('Legis', 'Block', $instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ReadLegisBlock', 'All', 'Legis', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewLegis', 'All', 'Legis', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadLegis', 'All', 'Legis', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('SubmitLegis', 'All', 'Legis', 'Item', 'All:All:All', 'ACCESS_COMMENT');
    xarRegisterMask('ModerateLegis', 'All', 'Legis', 'Item', 'All:All:All', 'ACCESS_MODERATE');    
    xarRegisterMask('EditLegis', 'All', 'Legis', 'Item', 'All:All:All', 'ACCESS_EDIT');
    xarRegisterMask('AddLegis', 'All', 'Legis', 'Item', 'All:All:All', 'ACCESS_ADD');
    xarRegisterMask('DeleteLegis', 'All', 'Legis', 'Item', 'All:All:All', 'ACCESS_DELETE');
    xarRegisterMask('AdminLegis', 'All', 'Legis', 'Item', 'All:All:All', 'ACCESS_ADMIN');

    /* Initialisation successful so return true */
    return true;
}

/**
 * Upgrade the module from an old version
 *
 * This function can be called multiple times
 */
function legis_upgrade($oldversion)
{
    /* Upgrade dependent on old version number */
    switch ($oldversion) {
        case '0.1.0':
             return legis_upgrade('0.2.0');
        case '0.2.0':
             return legis_upgrade('0.5.0');
        case '0.5.0':/* current version */
            /* Code to upgrade from version 1.5.0 goes here */
            break;
    }
    /* Update successful */
    return true;
}

/**
 * Delete the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function legis_delete()
{

    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    $LegisMasterTable = $xarTables['legis_master'];
    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    /* Drop the Legis tables */
     $result = $datadict->dropTable($LegisMasterTable);

    $LegisDocletsTable = $xarTables['legis_doclets'];
    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    /* Drop the Legis tables */
     $result = $datadict->dropTable($LegisDocletsTable);

    $LegisCompiledTable = $xarTables['legis_compiled'];
    /* Get a data dictionary object with item create and delete methods */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    /* Drop the Legis tables */
     $result = $datadict->dropTable($LegisCompiledTable);
     
     /* Remove any module aliases before deleting module vars */
    /* Assumes one module alias in this case */
    $aliasname =xarModGetVar('legis','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='legis')){
        xarModDelAlias($aliasname,'legis');
    }

    /* Delete any module variables */
    xarModDelAllVars('legis');

    /* UnRegister all blocks that the module uses*/
    if (!xarModAPIFunc('blocks','admin','unregister_block_type',
            array('modName' => 'legis',
                'blockType' => 'adminlinks'))) return;
    /* UnRegister all blocks that the module uses*/
    if (!xarModAPIFunc('blocks','admin','unregister_block_type',
            array('modName' => 'legis',
                'blockType' => 'latest'))) return;

    /* Unregister each of the hooks that have been created */
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'legis', 'user', 'usermenu')) {
        return false;
    }
   if (!xarModUnregisterHook('item', 'search', 'GUI',
            'legis', 'user', 'search')) {
        return false;
    }
    /* Remove Masks and Instances
     */
    xarRemoveMasks('legis');
    xarRemoveInstances('legis');

     /* Deletion successful*/
    return true;
}
?>