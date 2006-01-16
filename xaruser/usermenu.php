<?php
/**
 * Display the user menu hook
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Sigmapersonnel Module Development Team
 */

/**
 * Display the user menu hook
 * This is a standard function to provide a link in the "Your Account Page"
 *
 * @author the MichelV
 * @param  $phase is the which part of the loop you are on
 */
function sigmapersonnel_user_usermenu($args)
{
    extract($args);
    /* Security check  - if the user has read access to the menu, show a
     * link to display the details of the item
     */
    if (!xarSecurityCheck('ViewSIGMAPersonnel')) return;

    /* First, lets find out where we are in our logic.  If the phase
     * variable is set, we will load the correct page in the loop.
     */
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'menu', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'menu':
            /* We need to define the icon that will go into the page. */
            $icon = 'modules/sigmapersonnel/xarimages/preferences.gif';

            /* Now lets send the data to the template which name we choose here. */
            $data = xarTplModule('sigmapersonnel', 'user', 'usermenu_icon', array('iconbasic' => $icon));

            break;

        case 'form':
            /* Its good practice for the user menu to be personalized.  In order to do so, we
             * need to get some information about the user.
             */
            $name = xarUserGetVar('name');
            $uid = xarUserGetVar('uid');
            /* We also need to set the SecAuthKey, in order to stop hackers from setting user
             * vars off site.
             */
            $authid = xarSecGenAuthKey('sigmapersonnel');
            $itemsperpage = xarModGetUserVar('sigmapersonnel', 'itemsperpage', $uid);
            // Get the person for this uid
            $person = xarModAPIFunc('sigmapersonnel', 'user', 'getpersonid', array('uid'=> $uid));
            // This call does not generate an error!
            $persondata = '';
            if (!empty($person) && is_array($person)) {
                $personid = $person['personid'];
                // Implement check on person status here
                $persondata = xarModAPIFunc('sigmapersonnel', 'user', 'get', array('personid'=> $personid));
            }
            $data = xarTplModule('sigmapersonnel', 'user', 'usermenu_form', array('authid' => $authid,
                    'name' => $name,
                    'uid' => $uid,
                    'itemsperpage' => $itemsperpage,
                    'persondata' => $persondata,
                    'cities' => xarModAPIFunc('sigmapersonnel', 'user', 'gets',
                                      array('itemtype' => 4)),
                    'districts' => xarModAPIFunc('sigmapersonnel', 'user', 'gets',
                                      array('itemtype' => 3)),
                    'persstatusses' => xarModAPIFunc('sigmapersonnel', 'user', 'gets',
                                      array('itemtype' => 6))
                                      ));
            break;

        case 'update':
            /* First we need to get the data back from the template in order to process it.
             * The sigmapersonnel module is not setting any user vars at this time, but an sigmapersonnel
             * might be the number of items to be displayed per page.
             */
            if (!xarVarFetch('uid', 'int:1:', $uid)) return;
            if (!xarVarFetch('itemsperpage', 'str:1:100', $itemsperpage, '20', XARVAR_NOT_REQUIRED)) return;
            // Names etc
            if (!xarVarFetch('firstname', 'str:1:', $firstname, '',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('lastname', 'str:1:', $lastname, '',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('tussenvgsl', 'str:1:', $tussenvgsl, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('initials', 'str:1:', $initials, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('sex', 'str:1:', $sex, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('title', 'str:1:', $title, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('street', 'str:1:100', $street, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('zip', 'str:1:100', $zip, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('cityid', 'int:1:', $cityid, '',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('phonehome', 'str:1:100', $phonehome, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('mobile', 'str:1:100', $mobile, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('phonework', 'str:1:100', $phonework, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('email', 'str:1:100', $email, '', XARVAR_NOT_REQUIRED)) return; //Type email?
            // Privacy options
            if (!xarVarFetch('privphonehome', 'checkbox', $privphonehome, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('privwork', 'checkbox', $privwork,false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('privemail', 'checkbox', $privemail, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('privbirthdate', 'checkbox', $privbirthdate, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('privaddress', 'checkbox', $privaddress, false,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('privphonework', 'checkbox', $privphonework, false,XARVAR_NOT_REQUIRED)) return;
            // Contact person
            if (!xarVarFetch('contactname', 'str:1:100', $contactname, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('contactphone', 'str:1:100', $contactphone, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('contactstreet', 'str:1:100', $contactstreet, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('contactcityid', 'int:1:', $contactcityid, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('contactrelation', 'str:1:100', $contactrelation, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('contactmobile', 'str:1:100', $contactmobile, '', XARVAR_NOT_REQUIRED)) return;


            /* Confirm authorisation code. */
            if (!xarSecConfirmAuthKey()) return;

            xarModSetUserVar('sigmapersonnel', 'itemsperpage', $itemsperpage, $uid);
            /* Redirect back to the account page.  We could also redirect back to our form page as
             * well by adding the phase variable to the array.
             */
            xarResponseRedirect(xarModURL('roles', 'user', 'account', array('moduleload' => 'sigmapersonnel')));

            break;
    }
    /* Finally, we need to send our variables to block layout for processing.  Since we are
     * using the data var for processing above, we need to do the same with the return.
     */
    return $data;
}
?>