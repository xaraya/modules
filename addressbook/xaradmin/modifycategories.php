<?php
/**
 * File: $Id: modifycategories.php,v 1.4 2003/12/22 07:11:41 garrett Exp $
 *
 * AddressBook admin modifyCategories
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
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
function addressbook_admin_modifycategories() {

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
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));

            if (!xarVarFetch ('id', 'array::',$formData['id'], FALSE)) return;
            if (!xarVarFetch ('del', 'array::',$formData['del'], FALSE)) return;
            if (!xarVarFetch ('name', 'array::',$formData['name'], FALSE)) return;
            if (!xarVarFetch ('newname','str::30',$formData['newname'], FALSE)) return;

            if (!xarModAPIFunc(__ADDRESSBOOK__,'admin','updatecategories',$formData)) {
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));
            }
        }

        // get the list of categories
        $output['categories'] = xarModAPIFunc(__ADDRESSBOOK__,'util','getitems',array('tablename'=>'categories'));

        // Generate a one-time authorisation code for this operation
        $output['authid'] = xarSecGenAuthKey();

        // Submit button
        $output['btnCommitText'] = xarVarPrepHTMLDisplay(xarML(_AB_ADDRESSBOOKUPDATE));

    } else {
        return xarTplModule(__ADDRESSBOOK__,'user','noauth');
    }


    return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));

} // END modifycategories

?>