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
        // set up the cache directory
        $varCacheDir = sys::varpath() . '/cache';
        $twitterCacheDir = $varCacheDir . '/twitter';
        if (!is_dir($twitterCacheDir) && is_writable($varCacheDir)) {
            $old_umask = umask(0);
            mkdir($twitterCacheDir, 0770);
            umask($old_umask);
            if (!file_exists($twitterCacheDir.'/index.html')) {
                @touch($twitterCacheDir.'/index.html');
            }            
        }
        if (!is_dir($twitterCacheDir) || !is_writable($twitterCacheDir)) {
            // tell them that cache needs to be writable or manually create output dir
            $msg=xarML('The #(1) directory must be writable by the web server 
                       for the Twitter module to set up caching. 
                       The Twitter module cache is not configured, 
                       please make the #(1) directory writable by the web server
                       if you want to take advantage of caching.  
                       Alternatively, you can manually create the #(2) directory
                        - the #(2) directory must be writable by the web server for 
                       caching to work.',
                       $varCacheDir,
                       $twitterCacheDir);
            xarErrorSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                            new SystemException($msg));
            return false;
        }

    /*
    if (!xarMod::apiFunc('blocks',
            'admin',
            'register_block_type',
            array('modName' => 'twitter',
                'blockType' => 'timeline'))) return;
    */
    if (!xarModRegisterHook('item', 'create', 'API',
            'twitter', 'hooks', 'itemcreate')) {
        return false;
    }
    $xartable =& xarDB::getTables();

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
    xarRegisterMask('AddTwitter',    'All', 'twitter', 'All', 'All', 'ACCESS_ADD');
    xarRegisterMask('AdminTwitter',  'All', 'twitter', 'All', 'All', 'ACCESS_ADMIN');
    
    return true;
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
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    switch ($oldversion) {
      case '0.0.1':
        // to v0.0.2
        xarModVars::set('twitter', 'deftimeline', 'public');
        xarModVars::set('twitter', 'showpublic', true);
        xarModVars::set('twitter', 'showuser', false);
        xarModVars::set('twitter', 'showfriends', false);
      case '0.0.2':
        // to v0.0.3
        xarModVars::set('twitter', 'screen_name', '');
        xarModVars::set('twitter', 'screen_pass', '');
      case '0.0.3':
        // to v0.0.4
        xarModVars::set('twitter', 'friends_timeline', 0);
        xarModVars::set('twitter', 'user_timeline', 0);
        xarModVars::set('twitter', 'profile_image', 0);
        xarModVars::set('twitter', 'profile_description',0);
        xarModVars::set('twitter', 'profile_location',0);
        xarModVars::set('twitter', 'followers_count',0);
        xarModVars::set('twitter', 'friends_count',0);
        xarModVars::set('twitter', 'last_status',0);
        xarRegisterMask('CommentTwitter',   'All', 'twitter', 'All', 'All', 'ACCESS_COMMENT');
        xarModVars::set('twitter', 'main_tab', '');
        xarModVars::set('twitter', 'profile_tab', '');
        xarModVars::set('twitter', 'statuses_count', 0);
        xarModVars::set('twitter', 'favourites_display', 0);
        xarModVars::set('twitter', 'friends_display', 0);
        $public_timeline = xarModVars::get('twitter', 'showpublic');
        xarModVars::set('twitter', 'public_timeline', $public_timeline);
        $account_display = (xarModVars::get('twitter', 'showuser') || xarModVars::get('twitter', 'showfriends')) ? true : false;
        xarModVars::set('twitter', 'account_display', $account_display);
        xarModVars::set('twitter', 'users_display', true);
        $site_screen_name = xarModVars::get('twitter', 'username');
        $site_screen_pass = xarModVars::get('twitter', 'password');
        $site_screen_role = xarModVars::get('roles', 'admin');
        xarModVars::set('twitter', 'site_screen_pass', $site_screen_pass);
        xarModVars::set('twitter', 'site_screen_name', $site_screen_name);
        xarModVars::set('twitter', 'site_screen_role', $site_screen_role);

        xarModVars::delete('twitter', 'username');
        xarModVars::delete('twitter', 'password');
        xarModVars::delete('twitter', 'owner');
        xarModVars::delete('twitter', 'showpublic');
        xarModVars::delete('twitter', 'friends_timeline');
        xarModVars::delete('twitter', 'showuser');
        xarModVars::delete('twitter', 'showfriends');
        xarModVars::delete('twitter', 'screen_name');
        xarModVars::delete('twitter', 'screen_pass');
      case '0.0.4':
        // to v0.1.0 - 2nd point upgrade, signifies addition of approved twitter source param
      case '0.1.0':
        // to v0.1.1 - added createhook function
        // these are the module defaults for the hook functions (added in v0.1.1)
        $settings = array(
            'urltype' => 'user',
            'urlfunc' => 'display',
            'urlitemtype' => 'itemtype',
            'urlitemid' => 'itemid',
            'senduser' => 0,
            'sendsite' => 0,
            'fieldname' => '',
            'urlextra' => '',
        );
        xarModVars::set('twitter', 'twitter', serialize($settings));
      case '0.1.1':
          // Bug 6397: strip html entities from urls created by hooks
      case '0.1.2':
              $data['consumer_key'] = xarModVars::get('twitter', 'consumer_key');
              $data['consumer_secret'] = xarModVars::get('twitter', 'consumer_secret');
              $data['access_token'] = xarModVars::get('twitter', 'access_token');
              $data['access_token_secret'] = xarModVars::get('twitter', 'access_token_secret');
          // Remove all vars
          xarModVars::delete_all('twitter');
          foreach ($data as $k => $v)
              xarModVars::set('twitter', $k, $v);
          // unregister hook
          if (!xarModUnregisterHook('item', 'create', 'API',
              'twitter', 'user', 'createhook')) return false;
          // register hooks
          if (!xarModRegisterHook('item', 'create', 'API',
              'twitter', 'hooks', 'itemcreate')) return false;
          if (!xarModRegisterHook('module', 'modifyconfig', 'GUI',
              'twitter', 'hooks', 'modulemodifyconfig')) return false;
          if (!xarModRegisterHook('module', 'updateconfig', 'API',
              'twitter', 'hooks', 'moduleupdateconfig')) return false;

          // remove masks
          xarRemoveMasks('twitter');
          // register required masks
          xarRegisterMask('ReadTwitterBlock', 'All', 'twitter', 'Block', 'All', 'ACCESS_OVERVIEW');
          xarRegisterMask('AddTwitter',    'All', 'twitter', 'All', 'All', 'ACCESS_ADD');
          xarRegisterMask('AdminTwitter',  'All', 'twitter', 'All', 'All', 'ACCESS_ADMIN');
        // set up the cache directory
        $varCacheDir = 'var/cache';
        $twitterCacheDir = $varCacheDir . '/twitter';
        if (!is_dir($twitterCacheDir) && is_writable($varCacheDir)) {
            $old_umask = umask(0);
            mkdir($twitterCacheDir, 0770);
            umask($old_umask);
            if (!file_exists($twitterCacheDir.'/index.html')) {
                @touch($twitterCacheDir.'/index.html');
            }            
        }
        if (!is_dir($twitterCacheDir) || !is_writable($twitterCacheDir)) {
            // tell them that cache needs to be writable or manually create output dir
            $msg=xarML('The #(1) directory must be writable by the web server 
                       for the Twitter module to set up caching. 
                       The Twitter module cache is not configured, 
                       please make the #(1) directory writable by the web server
                       if you want to take advantage of caching.  
                       Alternatively, you can manually create the #(2) directory
                        - the #(2) directory must be writable by the web server for 
                       caching to work.',
                       $varCacheDir,
                       $twitterCacheDir);
            xarErrorSet(XAR_SYSTEM_EXCEPTION,'FUNCTION_FAILED',
                            new SystemException($msg));
            return false;
        }
      case '0.9.0': //current version         
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
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
    $datadict =& xarDBNewDataDict($dbconn, 'ALTERTABLE');
    //$twittertable = $xartable['twitter'];
    // $result = $datadict->dropTable($twittertable);

    $aliasname = xarModVars::get('twitter','aliasname');
    $isalias = xarModAlias::resolve($aliasname);
    if (isset($isalias) && ($isalias =='twitter')){
        xarModAlias::delete($aliasname,'twitter');
    }

    xarModVars::delete_all('twitter');

    if (!xarMod::apiFunc('blocks',
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
