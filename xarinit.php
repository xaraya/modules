<?php
/**
 * Initialize the SiteContact Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Initialize the SiteContact Module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 *
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
sys::import('xaraya.structures.hooks.observer');

function sitecontact_init()
{
    /* Setup our table for holding the different contact itemtype forms */
    $dbconn = xarDB::getConn();
    $xarTables = xarDB::getTables();

    $sitecontactTable = $xarTables['sitecontact'];

    sys::import('xaraya.tableddl');
    $fields = array(
        'xar_scid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_sctypename' => array('type' => 'varchar', 'size' => 100, 'null' => false, 'default' => ''),
        'xar_sctypedesc' => array('type' => 'varchar', 'size' => 254, 'null' => false, 'default' => ''),
        'xar_customtext' => array('type' => 'text', 'size'=>'medium', 'null' => false),
        'xar_customtitle' => array('type' => 'varchar', 'size' => 150, 'null' => false, 'default' => ''),
        'xar_optiontext' => array('type' => 'text', 'size'=>'medium', 'null' => false),
        'xar_webconfirmtext' => array('type' => 'text', 'size'=>'medium', 'null' => false),
        'xar_notetouser' => array('type' => 'text', 'size'=>'medium', 'null' => false),
        'xar_allowcopy' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_usehtmlemail' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_scdefaultemail' => array('type' => 'varchar', 'size' => 254, 'null' => false, 'default' => ''),
        'xar_scdefaultname' => array('type' => 'varchar', 'size' => 254, 'null' => false, 'default' => ''),
        'xar_scactive' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_savedata' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_permissioncheck' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_termslink' => array('type' => 'varchar', 'size' => 254, 'null' => false, 'default' => ''),
        'xar_soptions' => array('type' => 'text', 'size'=>'large', 'null' => false,)
        );

    $query = xarDBCreateTable($sitecontactTable , $fields);
    if (empty($query)) return; // throw back

    $result = &$dbconn->Execute($query);
    if (!$result) return;


    /* Set up a table for holding any saved data, if that option is chosen */
    $sitecontactResponseTable = $xarTables['sitecontact_response'];

    $fields = array(
        'xar_scrid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
        'xar_scid' => array('type' => 'integer', 'null' => false, 'default' => '0'),
        'xar_username' => array('type' => 'varchar', 'size' => 100, 'null' => false, 'default' => ''),
        'xar_useremail' => array('type' => 'varchar', 'size' => 254, 'null' => false, 'default' => ''),
        'xar_requesttext' => array('type' => 'varchar', 'size' => 150, 'null' => false, 'default' => ''),
        'xar_company' =>  array('type' => 'varchar', 'size' => 150, 'null' => false, 'default' => ''),
        'xar_usermessage' => array('type' => 'text', 'size'=>'medium', 'null' => false),
        'xar_useripaddress' => array('type' => 'varchar', 'size' => 24, 'null' => false, 'default' => ''),
        'xar_userreferer' => array('type' => 'varchar', 'size' => 254, 'null' => false, 'default' => ''),
        'xar_sendcopy' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_permission' => array('type' => 'integer', 'size' => 'small', 'null' => false, 'default' => '0'),
        'xar_bccrecipients' => array('type' => 'varchar', 'size' => 254, 'null' => false, 'default' => ''),
        'xar_ccrecipients' => array('type' => 'varchar', 'size' => 254, 'null' => false, 'default' => ''),
        'xar_responsetime' => array('type' => 'integer', 'size' => '10', 'null' => false, 'default' => '0')
        );

    $query = xarDBCreateTable( $sitecontactResponseTable, $fields);
    if (empty($query)) return; // throw back

    $result = &$dbconn->Execute($query);
    if (!$result) return;

//    $defaultemail=  xarModVars::get('mail', 'adminmail');

    xarModVars::set('sitecontact', 'scdefaultname', 'Basic Form');
    xarModVars::set('sitecontact', 'SupportShortURLs', 0);
    xarModVars::set('sitecontact', 'useModuleAlias',0);
    xarModVars::set('sitecontact', 'aliasname','');
    xarModVars::set('sitecontact', 'allowcc', false);
    xarModVars::set('sitecontact', 'allowbcc', false);
    xarModVars::set('sitecontact', 'admincc', false);
    xarModVars::set('sitecontact', 'savedata', 0);
    xarModVars::set('sitecontact', 'termslink', '');
    xarModVars::set('sitecontact', 'soptions', '');
    xarModVars::set('sitecontact', 'permissioncheck', 0);
    xarModVars::set('sitecontact', 'itemsperpage', 10);
    xarModVars::set('sitecontact', 'defaultform',1);
    xarModVars::set('sitecontact', 'defaultsort','scid');
    xarModVars::set('sitecontact', 'scactive', 1);
    xarModVars::set('sitecontact', 'usehtmlemail', 0);
    xarModVars::set('sitecontact', 'allowcopy', 0); //bug 5800 set it off by default
    xarModVars::set('sitecontact', 'allowanoncopy', 0); //bug 5800 set it off by default
    xarModVars::set('sitecontact', 'scdefaultemail',xarModVars::get('mail', 'adminmail'));
    xarModVars::set('sitecontact', 'customtitle','Contact and Feedback');
    xarModVars::set('sitecontact','useantibot',true);
    xarModVars::set('sitecontact', 'customtext',
    'Thank you for visiting. We appreciate your feedback.
    Please let us know how we can assist you.');

    xarModVars::set('sitecontact', 'optiontext',
    'Information request,
General assistance,
Website issue,
Spam/Abuse report,
Complaint, Thank you!');

    xarModVars::set('sitecontact', 'webconfirmtext',
    'Your message has been sent. Thank you for contacting us.
');
    xarModVars::set('sitecontact', 'defaultnote',
    'Dear %%username%%

This message confirms your email has been sent.

Thank you for your feedback.

Administrator
%%sitename%%
-------------------------------------------------------------');
  xarModVars::set('sitecontact','notetouser',xarModVars::get('sitecontact','defaultnote'));


     // Begin 2x
     /* No longer use hooks
     // Enable dynamicdata hooks for sitecontact forms
    if (xarModIsAvailable('dynamicdata')) {
        xarModAPIFunc('modules','admin','enablehooks',
                       array('callerModName' => 'sitecontact', 'hookModName' => 'dynamicdata'));
    }
    */
    $observer = new BasicObserver('sitecontact','admin','createhook');
    $observer->register('module', 'create', 'API');
    // End 2x

// initialize the block
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                        array('modName' => 'sitecontact',
                              'blockType' => 'sitecontact'))) return;

/* Define instances for sitecontact forms  */

    $query1 = "SELECT DISTINCT xar_scid FROM  $sitecontactTable";
    $instances = array(
                        array('header' => 'Form ID:',
                                'query' => $query1,
                                'limit' => 20
                            )
                    );
    xarDefineInstance('sitecontact', 'ContactForm', $instances);

    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ReadSiteContactBlock', 'All', 'sitecontact', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('SubmitSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_COMMENT'); //required where saving forms is done
    xarRegisterMask('EditSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_EDIT');//Do we need these?!
    xarRegisterMask('AddSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_ADD');//Do we need these?!
    xarRegisterMask('DeleteSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_DELETE');//Do we need these?!
    xarRegisterMask('AdminSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful


    //This initialization takes us to version 2.0.0 - continue in upgrade
        // Begin 2x
        # --------------------------------------------------------
        #
        # Create DD objects
        #
            $module = 'sitecontact';
            $objects = array(
                        'sitecontact_basicform',
                        'sitecontact_definition',
                    );

        xarModAPIFunc('modules','admin','standardinstall',array('module' => 'sitecontact', 'objects' => $objects));

        // Register a hook for utility modules
        xarModRegisterHook('module', 'getconfig', 'API','sitecontact', 'admin', 'getconfighook');
        //End 2x

    return sitecontact_upgrade('2.0.0');
}

/**
 * upgrade the SiteContact module from an old version
 * This function can be called multiple times
 */
function sitecontact_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {

        case '2.0.0': //current version

             break;
    }
    // Update successful
    return true;
}

/**
 * delete the SiteContact module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function sitecontact_delete()
{
    $this_module = 'sitecontact';

    /* drop the sitecontact table */
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();
    xarDBLoadTableMaintenanceAPI();

    $sitecontactTable = $xarTables['sitecontact'];
    $sitecontactResponseTable = $xarTables['sitecontact_response'];

    try {
        $query = xarDBDropTable($sitecontactTable);
        if (empty($query)) return; // throw back
        $result = &$dbconn->Execute($query);

        $query = xarDBDropTable($sitecontactResponseTable);
        if (empty($query)) return; // throw back
        $result = &$dbconn->Execute($query);
    } catch(SQLException $e) {
        // if they dont exist, no worries
    }

    /* Remove any module aliases before deleting module vars */
    $aliasname =xarModVars::get('sitecontact','aliasname');
    $isalias = xarModAlias::resolve($aliasname);
    if (isset($isalias) && ($isalias =='sitecontact')){
        xarModAlias::delete($aliasname,'sitecontact');
    }
    // Delete any module variables
     xarModVars::delete_all('sitecontact');

    // UnRegister blocks
    if (!xarModAPIFunc('blocks', 'admin', 'unregister_block_type',
                 array('modName' => 'sitecontact',
                       'blockType' => 'sitecontact'))) return;
/*
    // Remove module hooks
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'sitecontact', 'user', 'usermenu')) {
        return false;
    }
*/
    xarRemoveMasks('sitecontact');
    xarRemoveInstances('sitecontact');

    $deinstall = xarModAPIFunc('modules','admin','standarddeinstall',array('module' => 'sitecontact'));

    // Deletion successful
    return true;
}

?>