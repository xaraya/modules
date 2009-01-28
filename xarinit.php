<?php
/**
 * Example Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Initialise the module
 *
 * This function is only ever called once during the lifetime of a particular
 * module instance. It holds all the installation routines and sets the variables used
 * by this module. This function is the place to create you database structure and define
 * the privileges your module uses.
 *
 * @author Example Module Development Team
 * @param none
 * @return bool true on success of installation
 */
function twitter_init()
{

    xarModSetVar('twitter', 'username', '');
    xarModSetVar('twitter', 'password', '');
    xarModSetVar('twitter', 'useModuleAlias', false);
    xarModSetVar('twitter', 'aliasname', '');
    xarModSetVar('twitter', 'SupportShortURLs', 0);
    xarModSetVar('twitter', 'itemsperpage', 20);

    /* Register Block types (this *should* happen at activation/deactivation) */
    if (!xarModAPIFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'twitter',
                'blockType' => 'timeline'))) return;

    if (!xarModRegisterHook('item', 'create', 'API',
            'twitter', 'user', 'createhook')) {
        return false;
    }
    $xartable =& xarDBGetTables();

    $instancestable = $xartable['block_instances'];
    $typestable = $xartable['block_types'];
    $query = "SELECT DISTINCT i.xar_name FROM $instancestable i, $typestable t WHERE t.xar_id = i.xar_type_id AND t.xar_module = 'twitter'";
    $instances = array(
        array('header' => 'Twitter Block Name:',
            'query' => $query,
            'limit' => 20
            )
        );
    xarDefineInstance('twitter', 'Block', $instances);

    xarRegisterMask('ReadTwitterBlock', 'All', 'twitter', 'Block', 'All', 'ACCESS_OVERVIEW');
    /* Then for all operations */
    xarRegisterMask('ViewTwitter',   'All', 'twitter', 'All', 'All', 'ACCESS_OVERVIEW');
    xarRegisterMask('ReadTwitter',   'All', 'twitter', 'All', 'All', 'ACCESS_READ');
    xarRegisterMask('EditTwitter',   'All', 'twitter', 'All', 'All', 'ACCESS_EDIT');
    xarRegisterMask('AddTwitter',    'All', 'twitter', 'All', 'All', 'ACCESS_ADD');
    xarRegisterMask('DeleteTwitter', 'All', 'twitter', 'All', 'All', 'ACCESS_DELETE');
    xarRegisterMask('AdminTwitter',  'All', 'twitter', 'All', 'All', 'ACCESS_ADMIN');

    /* This init function brings our module to version 1.0, run the upgrades for the rest of the initialisation */
    return twitter_upgrade('0.0.1');
}

/**
 * Upgrade the module from an old version
 *
 * This function can be called multiple times. It holds all the routines for each version
 * of the module that are necessary to upgrade to a new version. It is very important to keep the
 * initialisation and the upgrade compatible with eachother.
 *
 * @author Example Module Development Team
 * @param string oldversion. This function takes the old version that is currently stored in the module db
 * @return bool true on succes of upgrade
 * @throws mixed This function can throw all sorts of errors, depending on the functions present
                 Currently it can raise database errors.
 */
function twitter_upgrade($oldversion)
{
    /* Upgrade dependent on old version number */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    switch ($oldversion) {
      case '0.0.1':

      case '0.0.2':

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
 * @author Example Module Development Team
 * @param none
 * @return bool true on succes of deletion
 */
function twitter_delete()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    //$twittertable = $xartable['twitter'];
    // $result = $datadict->dropTable($twittertable);

    $aliasname = xarModGetVar('twitter','aliasname');
    $isalias = xarModGetAlias($aliasname);
    if (isset($isalias) && ($isalias =='twitter')){
        xarModDelAlias($aliasname,'twitter');
    }

    xarModDelAllVars('twitter');

    if (!xarModAPIFunc('blocks',
            'admin',
            'unregister_block_type',
            array('modName' => 'twitter',
                'blockType' => 'timeline'))) return;

    if (!xarModUnregisterHook('item', 'create', 'API',
            'twitter', 'user', 'createhook')) {
        return false;
    }

    xarRemoveMasks('twitter');
    xarRemoveInstances('twitter');

    return true;
}
?>
