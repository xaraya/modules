<?php
/**
 * Login via a block.
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 * @link http://xaraya.com/index.php/release/30205.html
 */

/**
 * Login via a block.
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 * @author Jim McDonald
 * initialise block
 * @return array
 */
function registration_rloginblock_init()
{
    return array(
        'showlogout' => 0,
        'logouttitle' => '',
        'nocache' => 1, // don't cache by default
        'pageshared' => 1, // if you do, share across pages
        'usershared' => 0, // but don't share for different users
        'cacheexpire' => null
    );
}

/**
 * get information on block
 * @return array
 */
function registration_rloginblock_info()
{
    return array(
        'text_type' => 'Login',
        'module' => 'registration',
        'text_type_long' => 'Registration and login'
    );
}

/**
 * Display func.
 * @param $blockinfo array containing title,content
 * @return array $blockinfo
 */
function registration_rloginblock_display($blockinfo)
{
    // Security Check
    if(!xarSecurityCheck('ViewRegistrationLogin',0,'Block',"rlogin:" . $blockinfo['title'] . ":" . $blockinfo['bid'],'All')) return;

    // Get variables from content block
    if (!is_array($blockinfo['content'])) {
        $vars = unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Display logout block if user is already logged in
    // e.g. when the login/logout block also contains a search box
    if (xarUserIsLoggedIn()) {
        if (!empty($vars['showlogout'])) {
            $args['name'] = xarUserGetVar('name');

            // Since we are logged in, set the template base to 'logout'.
            // FIXME: not allowed to set BL variables directly
            $blockinfo['_bl_template_base'] = 'logout';

            if (!empty($vars['logouttitle'])) {
                $blockinfo['title'] = $vars['logouttitle'];
            }
        } else {
            return;
        }
    } elseif (xarServerGetVar('REQUEST_METHOD') == 'GET') {
        // URL of this page
        $args['return_url'] = xarServerGetCurrentURL();
    } else {
        // Base URL of the site
        $args['return_url'] = xarServerGetBaseURL();
    }

    // Used in the templates.
    $args['blockid'] = $blockinfo['bid'];

    $blockinfo['content'] = $args;
    return $blockinfo;
}

?>