<?php
/**
 * File: $Id$
 *
 * SiteContact initialization functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Initialize the SiteContact Module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 */
function sitecontact_init()
{
    xarModSetVar('sitecontact', 'itemsperpage', 10);
    xarModSetVar('sitecontact', 'SupportShortURLs', 0);
    xarModSetVar('sitecontact', 'usehtmlemail', 0);
    xarModSetVar('sitecontact', 'allowcopy', 1);
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
/*
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                 array('modName' => 'sitecontact',
                       'blockType' => 'sitecontactblock'))) return;
*/
    // Register our hooks that we are providing to other modules.  The example
    // module shows an example hook in the form of the user menu.
    if (!xarModRegisterHook('item', 'usermenu', 'GUI',
                            'sitecontact', 'user', 'usermenu')) {
        return false;
    }
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
            return sitecontact_upgrade('0.0.2');
        case '0.0.2':
            // Code to upgrade from version 1.0 goes here
            break;
        case '2.0.0':
            // Code to upgrade from version 2.0 goes here
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
    // Delete any module variables
     xarModDelAllVars('sitecontact');
    // UnRegister blocks
/*    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                 array('modName' => 'sitecontact',
                       'blockType' => 'sitecontactblock'))) return;
*/
    // Remove module hooks
    if (!xarModUnregisterHook('item', 'usermenu', 'GUI',
            'sitecontact', 'user', 'usermenu')) {
        return false;
    }

    xarRemoveMasks('sitecontact');
    xarRemoveInstances('sitecontact');

    // Deletion successful
    return true;
}

?>
