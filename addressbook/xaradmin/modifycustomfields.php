<?php
/**
 * File: $Id: modifycustomfields.php,v 1.6 2003/12/22 07:11:41 garrett Exp $
 *
 * AddressBook admin functions
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
 * Display form used to update the custom field settings
 * Handle the data submission
 *
 * @param GET / POST passed from modifycustomfields form
 * @return xarTemplate data
 */
function addressbook_admin_modifycustomfields() {

    $output = array(); // template contents go here
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
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));

            if (!xarVarFetch ('id', 'array::',$formData['id'], FALSE))  {
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('del', 'array::',$formData['del'], FALSE))  {
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('custLabel', 'array::',$formData['custLabel'], FALSE))  {
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('custType','array::',$formData['custType'],FALSE)) {
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('incID','int::',$formData['incID'],FALSE))  {
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('decID','int::',$formData['decID'],FALSE))  {
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('newname','str::30',$formData['newname'],FALSE))  {
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));
            }
            if (!xarVarFetch ('newtype','str::30',$formData['newtype'],FALSE))  {
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));
            }

            /**
             * Perform the update
             */
            if (!xarModAPIFunc(__ADDRESSBOOK__,'admin','updatecustomfields',$formData)) {
                return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));
            }
        }
//FIXME:<garrett> would rather use a userapi here
        $output['custfields'] = xarModAPIFunc(__ADDRESSBOOK__,'admin','getcustomfields');
        if(!is_array($output['custfields'])) {
            return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));
        }

        //gehINFO - this should be in a table & configurable
        $output['datatypes'][] = array('id'=>'varchar(60) default NULL',    'name'=>' Text, 60 chars, 1 line');
        $output['datatypes'][] = array('id'=>'varchar(120) default NULL',   'name'=>'Text, 120 chars, 2 lines');
        $output['datatypes'][] = array('id'=>'varchar(240) default NULL',   'name'=>'Text, 240 chars, 4 lines');
        $output['datatypes'][] = array('id'=>'int default NULL',            'name'=>'Integer numbers');
        $output['datatypes'][] = array('id'=>'decimal(10,2) default NULL',  'name'=>'Decimal numbers');
        $output['datatypes'][] = array('id'=>'int(1) default NULL',         'name'=>'Checkbox');
        $output['datatypes'][] = array('id'=>'date default NULL',           'name'=>'Date');
        $output['datatypes'][] = array('id'=>'tinyint default NULL',        'name'=>'Blank line');
        $output['datatypes'][] = array('id'=>'smallint default NULL',       'name'=>'Horizontal rule');

        // Generate a one-time authorisation code for this operation
        $output['authid'] = xarSecGenAuthKey();

        // Submit button
        $output['btnCommitText'] = xarVarPrepHTMLDisplay(xarML(_AB_ADDRESSBOOKUPDATE));

    } else {
        return xarTplModule(__ADDRESSBOOK__,'user','noauth');
    }

    return xarModAPIFunc(__ADDRESSBOOK__,'util','handleexception',array('output'=>$output));

} // END modifycustomfields

?>