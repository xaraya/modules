<?php
/**
 * Twitter Module
 *
 * @package modules
 * @copyright (C) 2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
/**
 * Initialise the module
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
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

    /* This init function brings our module to version 0.0.1, run the upgrades for the rest of the initialisation */
    return twitter_upgrade('0.0.1');
}

/**
 * Upgrade the module from an old version
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
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
        xarModSetVar('twitter', 'deftimeline', 'public');
        xarModSetVar('twitter', 'showpublic', true);
        xarModSetVar('twitter', 'showuser', false);
        xarModSetVar('twitter', 'showfriends', false);
      case '0.0.2':
        xarModSetVar('twitter', 'screen_name', '');
        xarModSetVar('twitter', 'screen_pass', '');
      case '0.0.3':
        xarModSetVar('twitter', 'friends_timeline', 0);
        xarModSetVar('twitter', 'user_timeline', 0);
        xarModSetVar('twitter', 'profile_image', 0);
        xarModSetVar('twitter', 'profile_description',0);
        xarModSetVar('twitter', 'profile_location',0);
        xarModSetVar('twitter', 'followers_count',0);
        xarModSetVar('twitter', 'friends_count',0);
        xarModSetVar('twitter', 'last_status',0);
        xarRegisterMask('CommentTwitter',   'All', 'twitter', 'All', 'All', 'ACCESS_COMMENT');
        xarModSetVar('twitter', 'main_tab', '');
        xarModSetVar('twitter', 'profile_tab', '');
        xarModSetVar('twitter', 'statuses_count', 0);
        xarModSetVar('twitter', 'favourites_display', 0);
        xarModSetVar('twitter', 'friends_display', 0);
        $public_timeline = xarModGetVar('twitter', 'showpublic');
        xarModSetVar('twitter', 'public_timeline', $public_timeline);
        $account_display = (xarModGetVar('twitter', 'showuser') || xarModGetVar('twitter', 'showfriends')) ? true : false;
        xarModSetVar('twitter', 'account_display', $account_display);
        xarModSetVar('twitter', 'users_display', true);
        $site_screen_name = xarModGetVar('twitter', 'username');
        $site_screen_pass = xarModGetVar('twitter', 'password');
        $site_screen_role = xarModGetVar('roles', 'admin');
        xarModSetVar('twitter', 'site_screen_pass', $site_screen_pass);
        xarModSetVar('twitter', 'site_screen_name', $site_screen_name);
        xarModSetVar('twitter', 'site_screen_role', $site_screen_role);

        xarModDelVar('twitter', 'username');
        xarModDelVar('twitter', 'password');
        xarModDelVar('twitter', 'owner');
        xarModDelVar('twitter', 'showpublic');
        xarModDelVar('twitter', 'friends_timeline');
        xarModDelVar('twitter', 'showuser');
        xarModDelVar('twitter', 'showfriends');
        xarModDelVar('twitter', 'screen_name');
        xarModDelVar('twitter', 'screen_pass');

      // v0.1.0 - 2nd point upgrade, signifies addition of approved twitter source param
      case '0.1.0':
        // current version
      break;
    }
    /* Update successful */
    return true;
}

/**
 * Delete the module
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
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
