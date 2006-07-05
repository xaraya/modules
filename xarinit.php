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
                        '0',
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

    /* Set up a table for holding any saved data, if that option is chosen */
    $sitecontactResponseTable = $xarTables['sitecontact_response'];

    /* Get a data dictionary object with all the item create methods in it */
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

    $fields= "xar_scrid           I      AUTO       PRIMARY,
              xar_scid            I      NotNull    DEFAULT 0,
              xar_username        C(100) NotNull    DEFAULT '',
              xar_useremail       C(254) NotNull    DEFAULT '',
              xar_requesttext     C(150) NotNull    DEFAULT '',
              xar_company         C(150) NotNull    DEFAULT '',
              xar_usermessage     X      NotNull    DEFAULT '',
              xar_useripaddress   C(24)  NotNull    DEFAULT '',
              xar_userreferer     C(254) NotNull    DEFAULT '',
              xar_sendcopy        I1     NotNull    DEFAULT 0,
              xar_permission      I1     NotNull    DEFAULT 0,
              xar_bccrecipients   C(254) NotNull    DEFAULT '',
              xar_ccrecipients    C(254) NotNull    DEFAULT '',
              xar_responsetime   I(10)  NotNull    DEFAULT 0
              ";
            $result = $datadict->changeTable($sitecontactResponseTable, $fields);
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
    xarModSetVar('sitecontact', 'allowcopy', 0); //bug 5800 set it off by default
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
            $scdefaultname=xarModGetVar('sitecontact', 'scdefaultname');
            if (!isset($scdefaultname) || trim($scdefaultname) == '') {
                $scdefaultname = xarML('Admin');
            }
            $usehtmlemail = xarModGetVar('sitecontact', 'usehtmlemail');
            $allowcopy = xarModGetVar('sitecontact', 'allowcopy');
            $customtitle = xarModGetVar('sitecontact', 'customtitle');
            $customtext = xarModGetVar('sitecontact', 'customtext');
            $optiontext = xarModGetVar('sitecontact', 'optiontext');
            $webconfirmtext = xarModGetVar('sitecontact', 'webconfirmtext');
            $notetouser = xarModGetVar('sitecontact', 'notetouser');
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
             return sitecontact_upgrade('0.5.1');
        case '0.5.1': //current version
        $dbconn =& xarDBGetConn();
        $xarTables =& xarDBGetTables();

        /* Set up a table for holding any saved data, if that option is chosen */
        $sitecontactResponseTable = $xarTables['sitecontact_response'];
        $sitecontactTable = $xarTables['sitecontact'];
        
        /* Get a data dictionary object with all the item create methods in it */
        $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');

        $fields= "xar_scrid          I      AUTO       PRIMARY,
                 xar_scid            I      NotNull    DEFAULT 0,
                 xar_username        C(100) NotNull    DEFAULT '',
                 xar_useremail       C(254) NotNull    DEFAULT '',
                 xar_requesttext     C(150) NotNull    DEFAULT '',
                 xar_company         C(150) NotNull    DEFAULT '',
                 xar_usermessage     X      NotNull    DEFAULT '',
                 xar_useripaddress   C(24)  NotNull    DEFAULT '',
                 xar_userreferer     C(254) NotNull    DEFAULT '',
                 xar_sendcopy        I1     NotNull    DEFAULT 0,
                 xar_permission      I1     NotNull    DEFAULT 0,
                 xar_bccrecipients   C(254) NotNull    DEFAULT '',
                 xar_ccrecipients    C(254) NotNull    DEFAULT '',
                 xar_responsetime    I(10)  NotNull    DEFAULT 0
              ";
            $result = $datadict->changeTable($sitecontactResponseTable, $fields);
            if (!$result) {return;}

        //register a new mask for submitting and *saving* a site contact form
        xarRegisterMask('SubmitSiteContact', 'All', 'sitecontact', 'Item', 'All:All:All', 'ACCESS_COMMENT'); //required where saving forms is done

        /* Define instances for sitecontact forms  */

        $query1 = "SELECT DISTINCT xar_scid FROM  $sitecontactTable";
        $instances = array(
                        array('header' => 'Form ID:',
                                'query' => $query1,
                                'limit' => 20
                            )
                    );
        xarDefineInstance('sitecontact', 'ContactForm', $instances);
           return sitecontact_upgrade('0.6.0');
        case '0.6.0':
            xarModSetVar('sitecontact', 'allowcopy', 0); //bug 5800 set it off by default
           return sitecontact_upgrade('0.6.1');
       case '0.6.1': //current version
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
 
    /* Let's clean up - delete the DD objects we have created if any, for each form type */
    $formtypes = xarModAPIFunc('sitecontact','user','getcontacttypes');
    $moduleid= xarModGetIDFromName('sitecontact');    

    if (is_array($formtypes)) {
        foreach ($formtypes as $formtype) {
            $objectinfo= xarModAPIFunc('dynamicdata','user','getobjectinfo',
                array('moduleid'=>$moduleid, 'itemtype'=>$formtype['scid']));
            
            $objectid= $objectinfo['objectid'];
            
            if (!empty($objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
            }
        }
    }


    /* drop the sitecontact table */
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    $sitecontactTable = $xarTables['sitecontact'];
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $result = $datadict->dropTable($sitecontactTable);

    $sitecontactResponseTable = $xarTables['sitecontact_response'];
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    $result = $datadict->dropTable($sitecontactResponseTable);


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