<?php
/**
 * File: $Id: dodelete.php,v 1.1 2003/07/02 07:31:18 garrett Exp $
 *
 * AddressBook user doDelete
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

//FIXME: <garrett> why is this not an API?

//=========================================================================
//  Delete a record
//=========================================================================
function AddressBook_user_dodelete() {

    if (!xarVarFetch ('id','int::',$id, FALSE)) return FALSE;

    // save menu settings
    $menuValues = xarModAPIFunc(__ADDRESSBOOK__,'user','getMenuValues');
    foreach ($menuValues as $key=>$value) {
        $data[$key] = $value;
    }

    $data['menuValues']=array('catview'   =>$data['catview']
                    ,'menuprivate'=>$data['menuprivate']
                    ,'all'       =>$data['all']
                    ,'sortview'  =>$data['sortview']
                    ,'page'      =>$data['page']
                    ,'char'      =>$data['char']
                    );

	// Security check
	// Confirm authorisation code
	//if (!pnSecAuthAction(0, 'AddressBook::', '::', ACCESS_READ)) {
	if (!xarSecConfirmAuthKey()) {
	    $data = array();
        $data['infoMsg'] = xarML(_ADDRESSBOOK_NOAUTH);
        $data['backToListTEXT'] = xarML(_AB_GOBACK);
		return $data;
    }

	if(!xarModAPIFunc(__ADDRESSBOOK__,'user','deleterecord',array('id'=>$id))) {
        $data = array();
        $data['infoMsg'] = xarML(_AB_DELETENOSUCCESS);
        $data['backToListTEXT'] = xarML(_AB_GOBACK);
		return $data;
    }

	// This function generated no output
    xarResponseRedirect(xarModURL(__ADDRESSBOOK__, 'user', 'viewall',$data['menuValues']));

    if (xarExceptionMajor() != XAR_NO_EXCEPTION) {
        // Got an exception
        if ((xarExceptionMajor() == XAR_SYSTEM_EXCEPTION) && !_AB_DEBUG) {
            return; // throw back
        } else {
                        // We are going to handle this exception REGARDLESS of the type
            $data['abExceptions'] = xarModAPIFunc(__ADDRESSBOOK__,'user','handleException');
        }
    }

    // Return
    return $data;
    
} // END doDelete

?>