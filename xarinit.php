<?php
/**
 * Initialize the SiteContact Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
function sitecontact_init()
{
    /* Setup our table for holding the different contact itemtype forms */
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    $sitecontactTable = $xarTables['sitecontact'];

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $fields= "xar_scid            I      AUTO       PRIMARY,
              xar_sctypename      C(100) NotNull    DEFAULT '',
              xar_sctypedesc      C(254) NotNull    DEFAULT '',
              xar_customtext      X      NotNull    DEFAULT '',
              xar_customtitle     C(150) NotNull    DEFAULT '',
              xar_optiontext      X      NotNull    DEFAULT '',
              xar_webconfirmtext  X      NotNull    DEFAULT '',
              xar_notetouser      X      NotNull    DEFAULT '',
              xar_allowcopy       I1     NotNull    DEFAULT 0,
              xar_usehtmlemail    I1     NotNull    DEFAULT 0,
              xar_scdefaultemail  C(254) NotNull    DEFAULT '',
              xar_scdefaultname   C(254) NotNull    DEFAULT '',
              xar_scactive        I1     NotNull    DEFAULT 1,
              xar_savedata        I1     NotNull    DEFAULT 0,
              xar_permissioncheck I1     NotNull    DEFAULT 0,
              xar_termslink       C(254) NotNull    DEFAULT '',
              xar_soptions        X      NotNull    DEFAULT ''
              ";
            $result = $datadict->changeTable($sitecontactTable, $fields);
           if (!$result) {return;}

    /* Create a default form */
    $defaultemail=  xarModGetVar('mail', 'adminmail');
    $sitecontactTable = $xarTables['sitecontact'];
    $query = "INSERT INTO $sitecontactTable
                  (xar_scid,
                   xar_sctypename,
                   xar_sctypedesc,
                   xar_customtext,
                   xar_customtitle,
                   xar_optiontext,
                   xar_webconfirmtext,
                   xar_notetouser,
                   xar_allowcopy,
                   xar_usehtmlemail,
                   xar_scdefaultemail,
                   xar_scdefaultname,
                   xar_scactive,
                   xar_savedata,
                   xar_permissioncheck,
                   xar_termslink,
                   xar_soptions)
                VALUES (1,
                        'basic',
                        'Basic contact form',
                        'Thank you for visiting. We appreciate your feedback.\nPlease let us know how we can assist you.',
                        'Contact and Feedback',
                        'Information request,\nGeneral assistance,\nWebsite issue,\nSpam report,\nComplaint,\nThank you!',
                        'Your message has been sent. Thank you for contacting us.',
                        'Dear %%username%%\n\nThis message confirms your email has been sent.\n\nThank you for your feedback.\n\nAdministrator\n%%sitename%%\n-------------------------------------------------------------',
                        '1',
                        '0',
                        ?,
                        'Site Admin',
                        1,
                        0,
                        0,
                        '',
                        ''
                        )";

    $bindvars = array($defaultemail);
    $result = &$dbconn->Execute($query,$bindvars);
           if (!$result) {return;}

    xarModSetVar('sitecontact', 'savedata', 0);
    xarModSetVar('sitecontact', 'termslink', '');
    xarModSetVar('sitecontact', 'soptions', '');
    xarModSetVar('sitecontact', 'permissioncheck', 0);
    xarModSetVar('sitecontact', 'itemsperpage', 10);
    xarModSetVar('sitecontact', 'defaultform',1);
    xarModSetVar('sitecontact', 'defaultsort','scid');
    xarModSetVar('sitecontact', 'scactive', 1);
    xarModSetVar('sitecontact', 'SupportShortURLs', 0);
    xarModSetVar('sitecontact', 'useModuleAlias',0);
    xarModSetVar('sitecontact', 'aliasname','');
    xarModSetVar('sitecontact', 'usehtmlemail', 0);
    xarModSetVar('sitecontact', 'allowcopy', 1);
    xarModSetVar('sitecontact', 'scdefaultemail',xarModGetVar('mail', 'adminmail'));
    xarModSetVar('sitecontact', 'customtitle','Contact and Feedback');
    xarModSetVar('sitecontact', 'customtext',
    'Thank you for visiting. We appreciate your feedback.
    Please let us know how we can assist you.');

    xarModSetVar('sitecontact', 'optiontext', 
    'Information request,
General assistance,
Website issue,
Spam/Abuse report,
Complaint, Thank you!');

    xarModSetVar('sitecontact', 'webconfirmtext',
    'Your message has been sent. Thank you for contacting us.
');
    xarModSetVar('sitecontact', 'defaultnote',
    'Dear %%username%%

This message confirms your email has been sent.

Thank you for your feedback.

Administrator
%%sitename%%
-------------------------------------------------------------');
  xarModSetVar('sitecontact','notetouser',xarModGetVar('sitecontact','defaultnote'));


     // Enable dynamicdata hooks for sitecontact forms
    if (xarModIsAvailable('dynamicdata')) {
        xarModAPIFunc('modules','admin','enablehooks',
                       array('callerModName' => 'sitecontact', 'hookModName' => 'dynamicdata'));
    }


/*
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                 array('modName' => 'sitecontact',
                       'blockType' => 'sitecontactblock'))) return;
*/
    // Register our hooks that we are providing to other modules.  The example
    // module shows an example hook in the form of the user menu.
 /*   if (!xarModRegisterHook('item', 'usermenu', 'GUI',
                            'sitecontact', 'user', 'usermenu')) {
        return false;
    }
 */
/*
    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_title FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'sitecontact'";
    $instances = array(
        array('header' => 'SiteContact Block Title:',
              'query' => $query,
              'limit' => 20
              )
        );
    xarDefineInstance('sitecontact', 'Block', $instances);
*/
    /**
     * Register the module components that are privileges objects
     * Format is
     * xarregisterMask(Name,Realm,Module,Component,Instance,Level,Description)
     */

    xarRegisterMask('ReadSiteContactBlock', 'All', 'sitecontact', 'Block', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ViewSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_READ');
    xarRegisterMask('EditSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_EDIT');//Do we need these?!
    xarRegisterMask('AddSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_ADD');//Do we need these?!
    xarRegisterMask('DeleteSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_DELETE');//Do we need these?!
    xarRegisterMask('AdminSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_ADMIN');
    // Initialisation successful
    return true;
}

/**
 * upgrade the SiteContact module from an old version
 * This function can be called multiple times
 */
function sitecontact_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch ($oldversion) {
        case '0.0.1':
        //Add two mod vars in version 0.0.2
            xarModSetVar('sitecontact', 'usehtmlemail', 0);
            xarModSetVar('sitecontact', 'allowcopy', 1);

        case '0.0.2':
            // Code to upgrade from version 1.0 goes here

       case '0.2.0':

        case '0.3.0':
           xarModSetVar('sitecontact', 'useModuleAlias',0);
           xarModSetVar('sitecontact', 'aliasname','');

        case '0.3.5':
          // Remove incomplete module hook until ready
           if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
              'sitecontact', 'user', 'usermenu')) {
               return false;
           }
            /* New modvars */
            xarModSetVar('sitecontact', 'defaultform',1);
            xarModSetVar('sitecontact', 'scactive',1);
            xarModSetVar('sitecontact', 'defaultsort','scid');

            /* Setup our table for holding the different contact itemtype forms */
            $dbconn =& xarDBGetConn();
            $xarTables =& xarDBGetTables();

            $sitecontactTable = $xarTables['sitecontact'];

            /* Get a data dictionary object with all the item create methods in it */
            $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

            $fields= "xar_scid           I      AUTO       PRIMARY,
                      xar_sctypename     C(100) NotNull    DEFAULT '',
                      xar_sctypedesc     C(254) NotNull    DEFAULT '',
                      xar_customtext     X      NotNull    DEFAULT '',
                      xar_customtitle    C(150) NotNull    DEFAULT '',
                      xar_optiontext     X      NotNull    DEFAULT '',
                      xar_webconfirmtext X      NotNull    DEFAULT '',
                      xar_notetouser     X      NotNull    DEFAULT '',
                      xar_allowcopy      I1     NotNull    DEFAULT 0,
                      xar_usehtmlemail   I1     NotNull    DEFAULT 0,
                      xar_scdefaultemail C(254) NotNull    DEFAULT '',
                      xar_scdefaultname  C(254) NotNull    DEFAULT '',
                      xar_scactive       I1     NotNull    DEFAULT 1
                  ";
            $result = $datadict->changeTable($sitecontactTable, $fields);
            if (!$result) {return;}
            
            /* Create a default form */
            $scdefaultemail=  xarModGetVar('sitecontact', 'scdefaultemail');
            $usehtmlemail = xarModGetVar('sitecontact', 'usehtmlemail');
            $allowcopy = xarModGetVar('sitecontact', 'allowcopy');
            $scdefaultemail = xarModGetVar('sitecontact', 'scdefaultemail');
            $customtitle = xarModGetVar('sitecontact', 'customtitle');
            $customtext = xarModGetVar('sitecontact', 'customtext');
            $optiontext = xarModGetVar('sitecontact', 'optiontext');
            $webconfirmtext = xarModGetVar('sitecontact', 'webconfirmtext');
            $notetouser = xarModGetVar('sitecontact', 'notetouser');
            $scdefaultname=xarModGetVar('sitecontact', 'scdefaultname');
            $sitecontactTable = $xarTables['sitecontact'];
            $query ="INSERT INTO $sitecontactTable
                  (xar_scid,
                   xar_sctypename,
                   xar_sctypedesc,
                   xar_customtext,
                   xar_customtitle,
                   xar_optiontext,
                   xar_webconfirmtext,
                   xar_notetouser,
                   xar_allowcopy,
                   xar_usehtmlemail,
                   xar_scdefaultemail,
                   xar_scdefaultname,
                   xar_scactive)
                VALUES (1,
                        'basic',
                        'Basic contact form',
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        ?,
                        1
                        )";

                $bindvars = array($customtext,$customtitle,$optiontext,$webconfirmtext,$notetouser,$allowcopy,$usehtmlemail,$scdefaultemail,$scdefaultname);
                $result = &$dbconn->Execute($query,$bindvars);
                if (!$result) {return;}


        case '0.4.0':
            /* New modvars */
            xarModSetVar('sitecontact', 'savedata', 0);
            xarModSetVar('sitecontact', 'termslink', '');
            xarModSetVar('sitecontact', 'soptions', '');
            xarModSetVar('sitecontact', 'permissioncheck', 0);

            $dbconn =& xarDBGetConn();
            $xarTables =& xarDBGetTables();

            $sitecontactTable = $xarTables['sitecontact'];

            /* Get a data dictionary object with all the item create methods in it */
            $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

            /* Add a few more fields */
            $fields= "xar_savedata        I1     NotNull    DEFAULT 0,
                      xar_permissioncheck I1     NotNull    DEFAULT 0,
                      xar_termslink       C(254) NotNull    DEFAULT '',
                      xar_soptions        X      NotNull    DEFAULT ''
                     ";
            $result = $datadict->changeTable($sitecontactTable, $fields);
            if (!$result) {return;}

            /* Enable dynamicdata hooks for sitecontact forms - now a dependency */
            if (xarModIsAvailable('dynamicdata')) {
               xarModAPIFunc('modules','admin','enablehooks',
                   array('callerModName' => 'sitecontact', 'hookModName' => 'dynamicdata'));
           }
           return sitecontact_upgrade('0.5.0'); // Go direct to 0.5.0 to skip the intermediary release
           break;
        case '0.4.1': //0.4.1 users missed an intermediary release and the function changes. Take them back
           return sitecontact_upgrade('0.4.0');
           break;
        case '0.5.0': //nothing new here
             break;
        case '0.5.1': //current version

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
    /* drop the sitecontact table */
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    $sitecontactTable = $xarTables['sitecontact'];
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $result = $datadict->dropTable($sitecontactTable);

    /* Remove any module aliases before deleting module vars */
    $aliasname =xarModGetVar('sitecontact','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='sitecontact')){
        xarModDelAlias($aliasname,'sitecontact');
    }
    // Delete any module variables
     xarModDelAllVars('sitecontact');
    // UnRegister blocks
/*    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                 array('modName' => 'sitecontact',
                       'blockType' => 'sitecontactblock'))) return;

    // Remove module hooks
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'sitecontact', 'user', 'usermenu')) {
        return false;
    }
*/
    xarRemoveMasks('sitecontact');
    xarRemoveInstances('sitecontact');

    // Deletion successful
    return true;
}

?>