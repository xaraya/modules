<?php
/**
 * Login via a block
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/77102.html
 * @author Alexander GQ Gerasiov <gq@gq.pp.ru>
*/
/**
 * initialise block
 */
function authphpbb2_phpbb2loginblock_init()
{
    return true;
}

/**
 * get information on block
 */
function authphpbb2_phpbb2loginblock_info()
{
    return array('text_type' => 'Login',
                 'module' => 'authphpbb2',
                 'text_type_long' => 'User\'s login/logout/register link');
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 */
function authphpbb2_phpbb2loginblock_display($blockinfo)
{
// Security Check
    if(!xarSecurityCheck('ViewLogin',1,'Block',"All:" . $blockinfo['title'] . ":All",'All')) return;

    // Get variables from content block
    $vars = unserialize($blockinfo['content']);

    // Display logout block if user is already logged in
    // e.g. when the login/logout block also contains a search box
    if (xarUserIsLoggedIn()) {
        if (!empty($vars['showlogout'])) {
            $args['name'] = xarUserGetVar('name');
            $args['blockid'] = $blockinfo['bid'];
            $blockinfo['content'] = xarTplBlock('authphpbb2', 'phpbb2logout', $args);
            if (!empty($vars['logouttitle'])) {
                $blockinfo['title'] = $vars['logouttitle'];
            }
            return $blockinfo;
        } else {
            return;
        }
    }

    // URL of this page
    $args['showregister'] = $vars['showregister'];
    $args['return_url'] = xarServerGetCurrentURL();
    $args['signinlabel']= xarML('Sign in');
    $args['registerurl']  = xarModGetVar('authphpbb2', 'forumurl')."/profile.php?mode=register";
    $args['blockid'] = $blockinfo['bid'];
    if (empty($blockinfo['template'])) {
        $template = 'phpbb2login';
    } else {
        $template = $blockinfo['template'];
    }
    $blockinfo['content'] = xarTplBlock('authphpbb2', $template, $args);

    return $blockinfo;
}

/**
 * Modify Function to the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function authphpbb2_phpbb2loginblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);

    // Defaults
    if (empty($vars['showlogout'])) {
        $vars['showlogout'] = 0;
    }
    if (empty($vars['showregister'])) {
        $vars['showregister'] = 1;
    }
    if (empty($vars['logouttitle'])) {
        $vars['logouttitle'] = '';
    }

    $args['showlogout'] = $vars['showlogout'];
    $args['showregister'] = $vars['showregister'];
    $args['logouttitle'] = $vars['logouttitle'];

    $args['blockid'] = $blockinfo['bid'];
    $content = xarTplBlock('authphpbb2', 'phpbb2login-modify', $args);

    return $content;
}

/**
 * Updates the Block config from the Blocks Admin
 * @param $blockinfo array containing title,content
 */
function authphpbb2_phpbb2loginblock_update($blockinfo)
{
    if (!xarVarFetch('showlogout', 'notempty', $vars['showlogout'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('showregister', 'notempty', $vars['showregister'], 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('logouttitle', 'notempty', $vars['logouttitle'], '', XARVAR_NOT_REQUIRED)) return;

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

?>