<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * The user menu that is used in roles/account
 */
function comments_user_usermenu($args)
{
    extract($args);

    // Security Check
    if (xarSecurity::check('ReadComments',0)) {

    if(!xarVar::fetch('phase','str', $phase, 'menu', xarVar::NOT_REQUIRED)) {return;}

        xarTpl::setPageTitle(xarModVars::get('themes', 'SiteName').' :: '.
                           xarVar::prepForDisplay(xarML('Comments'))
                           .' :: '.xarVar::prepForDisplay(xarML('Your Account Preferences')));

        switch(strtolower($phase)) {
        case 'menu':

            $icon = xarTpl::getImage('comments.gif', 'comments');
            $data = xarTpl::module('comments','user', 'usermenu_icon',
                array('icon' => $icon,
                      'usermenu_form_url' => xarController::URL('comments', 'user', 'usermenu', array('phase' => 'form'))
                     ));
            break;

        case 'form':

            $settings = xarMod::apiFunc('comments','user','getoptions');
            $settings['max_depth'] = _COM_MAX_DEPTH - 1;
            $authid = xarSec::genAuthKey('comments');
            $data = xarTpl::module('comments','user', 'usermenu_form', array('authid'   => $authid,
                                                                           'settings' => $settings));
            break;

        case 'update':

            if(!xarVar::fetch('settings','array', $settings, array(), xarVar::NOT_REQUIRED)) {return;}

            if (count($settings) <= 0) {
                $msg = xarML('Settings passed from form are empty!');
                throw new BadParameterException($msg);
            }

            // Confirm authorisation code.
            if (!xarSec::confirmAuthKey())
                return;

            xarMod::apiFunc('comments','user','setoptions',$settings);

            // Redirect
            xarController::redirect(xarController::URL('roles', 'user', 'account'));

            break;
        }

    } else {
       $data=''; //make sure hooks in usermenu don't fail because this function returns unset
    }
        return $data;
}

?>