<?php
/**
 * Display the user menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dynamic Data Example Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * display the user menu hook
 * This is a standard function to provide a link in the "Your Account Page"
 *
 * @param $phase is the which part of the loop you are on
 *
 */
function labaccounting_user_usermenu($args)
{
    extract($args);
    // Security check  - if the user has read access to the menu, show a
    // link to display the details of the item
    if (!xarSecurityCheck('ViewJournal')) return;

    // First, lets find out where we are in our logic.  If the phase
    // variable is set, we will load the correct page in the loop.
    if(!xarVarFetch('phase','str', $phase, 'menu', XARVAR_NOT_REQUIRED)) {return;}
    
    $authid = xarSecGenAuthKey('labaccounting');

    switch(strtolower($phase)) {
        case 'menu':
            $tplvars = array('icon' => 'modules/labaccounting/xarimages/admin.gif');
            
            $tplvars['showjournalicon'] = false;
            
            if (xarSecurityCheck('ViewJournal', false)) {
                $tplvars['showjournalicon'] = true;
            }
            
            $tplvars['showledgericon'] = false;
            
            if (xarSecurityCheck('ViewLedger', false)) {
                $tplvars['showledgericon'] = true;
            }
            
            $data = xarTplModule('labaccounting','user', 'usermenu_icon', $tplvars);
            break;

        case 'form':
            $tplvars = array('authid' => $authid);
            $tplvars['agentid'] = xarModGetUserVar('labaccounting','myagentid');
            $tplvars['contactid'] = xarModGetUserVar('labaccounting','mycontactid');
            $tplvars['addressid'] = xarModGetUserVar('labaccounting','myaddressid');
            
            if($tplvars['contactid'] > 0) {
                $addresslist = xarModAPIFunc('dossier','locations','getallcontact',array('contactid'=>$tplvars['contactid']));
                
                if($addresslist === false) return;
                
                $tplvars['addresslist'] = $addresslist;
            }
    
            $journals = xarModAPIFunc('labaccounting', 'journals', 'getall', array('owneruid' => xarUserGetVar('uid'), 'journaltype' => 'all'));
            
            if($journals === false) return;
            
            $tplvars['journals'] = $journals;
            
            $data = xarTplModule('labaccounting','user', 'usermenu_form', $tplvars);
            break;

        case 'update':
            // First we need to get the data back from the template in order to process it.
            // The example module is not setting any user vars at this time, but an example
            // might be the number of items to be displayed per page.
            if(!xarVarFetch('uid','int', $uid, 0, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('name','str', $name, '', XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('agentid','int', $agentid, 0, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('contactid','int', $contactid, 0, XARVAR_NOT_REQUIRED)) {return;}
            if(!xarVarFetch('addressid','int', $addressid, 0, XARVAR_NOT_REQUIRED)) {return;}

            // Confirm authorisation code.
            if (!xarSecConfirmAuthKey()) return;
//die("test: ".$agentid);
            xarModSetUserVar('labaccounting','myagentid', $agentid);
            xarModSetUserVar('labaccounting','mycontactid', $contactid);
            xarModSetUserVar('labaccounting','myaddressid', $addressid);
            
            // Redirect back to the account page.  We could also redirect back to our form page as
            // well by adding the phase variable to the array.
            xarResponseRedirect(xarModURL('roles', 'user', 'account',array('moduleload'=>'labaccounting')));

            break;
    }
    
    // Finally, we need to send our variables to block layout for processing.  Since we are
    // using the data var for processing above, we need to do the same with the return.
    return $data;
}

?>
