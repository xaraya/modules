<?php
/**
 * AuthInvision module - authenticate against Invision PB forum
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authinvision
 * @link http://xaraya.com/index.php/release/950.html
 * @author ladyofdragons
 */

/**
 * Initialisation function
*/
function authinvision_init()
{
    // Set up module variables
    xarModSetVar('authinvision','server', 'localhost');
    xarModSetVar('authinvision','database', 'iboard');
    xarModSetVar('authinvision','username','root');
    xarModSetVar('authinvision','password', '');
    xarModSetVar('authinvision','prefix','ibf');
    xarModSetVar('authinvision','defaultgroup','Users');
    xarModSetVar('authinvision','forumroot','iboard');
    xarModSetVar('authinvision','version','1');
  
    // Register blocks
    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                       array('modName'  => 'authinvision',
                             'blockType'=> 'usercp'))) return;

    if (!xarModAPIFunc('blocks', 'admin', 'register_block_type',
                       array('modName'  => 'authinvision',
                             'blockType'=> 'whos_online'))) return;                             
                             
    /* as soon as I finish a lastxposts block I'll add that here */
    /*if (!xarModAPIFunc('blocks',
                       'admin',
                       'register_block_type',
                       array('modName'  => 'authinvision',
                             'blockType'=> 'lastxposts'))) return;*/

    // Define mask definitions for security checks
    xarRegisterMask('Adminauthinvision','All','authinvision','All','All','ACCESS_ADMIN');
    xarRegisterMask('Readauthinvision','All','authinvision','All','All','ACCESS_READ');

    // Do not add authinvision to Site.User.AuthenticationModules in xar_config_vars here
/*
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModules[] = 'authinvision';

    // Sort array so authinvision is before authsystem
    sort($authModules);

    xarConfigSetVar('Site.User.AuthenticationModules',$authModules);
*/

    // Initialization successful
    return true;
}

/**
 * Module upgrade function
 *
 */
function authinvision_upgrade($oldVersion)
{
    switch($oldVersion) {
        case '1.0.0':
            xarModSetVar('authinvision','version','1');
    }
    return true;
}
/**
 * module removal function
*/
function authinvision_delete()
{
    xarModDelVar('authinvision','server');
    xarModDelVar('authinvision','database');
    xarModDelVar('authinvision','username');
    xarModDelVar('authinvision','password');
    xarModDelVar('authinvision','prefix');
    xarModDelVar('authinvision','defaultgroup');
    xarModDelVar('authinvision','forumroot');
    xarModDelVar('authinvision','version');

    // Remove authinvision to Site.User.AuthenticationModules in xar_config_vars
    $authModules = xarConfigGetVar('Site.User.AuthenticationModules');
    $authModulesUpdate = array();

    // Loop through current auth modules and remove 'authinvision'
    foreach ($authModules as $authType) {
        if ($authType != 'authinvision')
            $authModulesUpdate[] = $authType;
    }

    xarConfigSetVar('Site.User.AuthenticationModules',$authModulesUpdate);
    
    // UnRegister blocks
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'authinvision',
                             'blockType'=> 'usercp'))) return;
                             
    if (!xarModAPIFunc('blocks',
                       'admin',
                       'unregister_block_type',
                       array('modName'  => 'authinvision',
                             'blockType'=> 'whos_online'))) return;
    
    // Deletion successful
    return true;
}
?>
