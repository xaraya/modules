<?php
/**
 * Display the user menu hook
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Display the user menu hook
 *
 * @author jojodee
 * @param  $phase is the which part of the loop you are on
 */
function legis_user_usermenu($args)
{
    extract($args);
    if (!xarSecurityCheck('ViewLegis')) return;
    
    /* First, lets find out where we are in our logic.  If the phase
     * variable is set, we will load the correct page in the loop.
     */
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'menu', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'menu':
            /* We need to define the icon that will go into the page. */
            $icon = xarTplGetImage('preferences.gif','legis');

            /* Now lets send the data to the template which name we choose here. */
            $data = xarTplModule('legis', 'user', 'usermenu_icon', array('iconbasic' => $icon));

            break;

        case 'form':
            $name = xarUserGetVar('name');
            $uid = xarUserGetVar('uid');
            $defaulthall = xarModGetUserVar('legis', 'defaulthall', $uid);
            $hallsparent=xarModGetVar('legis','mastercids');
            $halls=xarModApiFunc('categories','user','getchildren',array('cid'=>$hallsparent));
            $authid = xarSecGenAuthKey('legis');
            //create the hall links
            foreach ($halls as $k=>$hall) {
            if (isset($defaulthall) && $defaulthall!='') {
                $halls[$k]['link']=xarModURL('legis','user','view',
                          array('hall'=>$hall['cid']));
                }else{
                   $halls[$k]['link']=xarModURL('legis','user','main',
                        array('defaulthall'=>$hall['cid']));
            }
            }
            $value = xarModGetUserVar('legis', 'itemsperpage', $uid);
            $isexec=xarModAPIFunc('legis','user','checkexecstatus');
            if (!xarUserIsLoggedIn() || !$isexec) {
                $cansethall=true;
            } else {
                $cansethall=false;
            }

            $data = xarTplModule('legis', 'user', 'usermenu_form', array('authid' => $authid,
                    'name' => $name,
                    'uid' => $uid,
                    'value' => $value,
                    'defaulthall' => $defaulthall,
                    'halldata'=>$halls,
                    'cansethall'=>$cansethall));
            break;

        case 'update':
            /* First we need to get the data back from the template in order to process it.
             * The legis module is not setting any user vars at this time, but an legis
             * might be the number of items to be displayed per page.
             */
            if (!xarVarFetch('uid', 'int:1:', $uid)) return;
            if (!xarVarFetch('itemsperpage', 'str:1:100', $itemsperpage, '20', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('name', 'str:1:100', $name, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('defaulthall', 'int:1:', $defaulthall, $defaulthall, XARVAR_NOT_REQUIRED)) return;
             /* Confirm authorisation code. */
            if (!xarSecConfirmAuthKey()) return;

            xarModSetUserVar('legis', 'itemsperpage', $itemsperpage, $uid);
            xarModSetUserVar('legis', 'defaulthall', $defaulthall, $uid);
             /* Redirect back to the account page.  We could also redirect back to our form page as
             * well by adding the phase variable to the array.
             */
            xarResponseRedirect(xarModURL('roles', 'user', 'account'));

            break;
    }
    /* Finally, we need to send our variables to block layout for processing.  Since we are
     * using the data var for processing above, we need to do the same with the return.
     */
    return $data;
}
?>
