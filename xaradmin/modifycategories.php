<?php
/**
 * AddressBook admin modifyCategories
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage AddressBook Module
 * @author Garrett Hunter <garrett@blacktower.com>
 * Based on pnAddressBook by Thomas Smiatek <thomas@smiatek.com>
 */

/**
 * Display form used to update the category settings
 * Handle the data submission
 *
 * @param GET / POST passed from modifycategories form
 * @return xarTemplate data
 */
function addressbook_admin_modifycategories()
{

    $output = array();

    /**
     * Security check first
     */
    if (xarSecurityCheck('AdminAddressBook',0)) {
        /*
         * Check if we've come in via a submit, commit everything and cary on
         */
        xarVarFetch('formSubmit', 'str::', $formSubmit,FALSE);
        if ($formSubmit) {
            /**
             * Data integrity / Security check
             */
            if (!xarSecConfirmAuthKey())
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

            if (!xarVarFetch ('id', 'array::',$formData['id'], FALSE)) return;
            if (!xarVarFetch ('del', 'array::',$formData['del'], FALSE)) return;
            if (!xarVarFetch ('name', 'array::',$formData['name'], FALSE)) return;
            if (!xarVarFetch ('newname','str::30',$formData['newname'], FALSE)) return;

            if (!xarModAPIFunc('addressbook','admin','updatecategories',$formData)) {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
        }

        // get the list of categories
        $output['categories'] = xarModAPIFunc('addressbook','util','getitems',array('tablename'=>'categories'));

        // Generate a one-time authorisation code for this operation
        $output['authid'] = xarSecGenAuthKey();

        // Submit button
        $output['btnCommitText'] = xarVarPrepHTMLDisplay(xarML('Commit Changes'));

    } else {
        return xarTplModule('addressbook','user','noauth');
    }


    return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

} // END modifycategories

?>