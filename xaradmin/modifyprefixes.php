<?php
/**
 * AddressBook admin modifylabels
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * Display form used to update the prefixes in the name form
 * Handle the data submission
 *
 * @param GET / POST passed from modifyprefix form
 * @return xarTemplate data
 */
function addressbook_admin_modifyprefixes($args)
{
    $output = array (); // template contents go here
    /**
     * Security check first
     */
    if (xarSecurityCheck('AdminAddressBook',0)) {
        /*
         * Check if we've come in via a submit, commit everything and cary on
         */
        xarVarFetch('formSubmit', 'str::', $formSubmit,FALSE);
        if ($formSubmit) {

            if (!xarSecConfirmAuthKey())
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));


            if (!xarVarFetch ('id', 'array::',$formData['id'], FALSE)) {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('del', 'array::',$formData['del'], FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('name', 'array::',$formData['name'], FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('newname','str::30',$formData['newname'],FALSE))  {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }

            if (!xarModAPIFunc('addressbook','admin','updateprefixes',$formData)) {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
        }

        // get the list of prefixes
        $output['prefixes'] = xarModAPIFunc('addressbook','util','getitems',array('tablename'=>'prefixes'));

        // Generate a one-time authorisation code for this operation
        $output['authid'] = xarSecGenAuthKey();

        // Submit button
        $output['btnCommitText'] = xarML('Commit Changes');

    } else {
        return xarTplModule('addressbook','user','noauth');
    }

    return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

} // END modifyprefixes

?>