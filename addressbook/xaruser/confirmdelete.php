<?php
/**
 * File: $Id: confirmdelete.php,v 1.1 2003/07/02 07:31:18 garrett Exp $
 *
 * AddressBook user confirmDelete
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

//=========================================================================
//  Confirm deletion
//=========================================================================
function AddressBook_user_confirmdelete() {

    $data = array();

	// preserve menu settings
    $menuValues = xarModAPIFunc(__ADDRESSBOOK__,'user','getMenuValues');
    foreach ($menuValues as $key=>$value) {
        $data[$key] = $value;
    }

	$data['menuValues']=array('catview'   =>$data['catview'],
                    'menuprivate'=>$data['menuprivate'],
                    'all'       =>$data['all'],
                    'sortview'  =>$data['sortview'],
                    'page'      =>$data['page'],
                    'char'      =>$data['char'],
                    'total'     =>$data['total']);

	// Get the values
	$data = xarModAPIFunc(__ADDRESSBOOK__,'user','getsubmitvalues', array ('data'=>$data));

	// Get detailed values from database
	$details = xarModAPIFunc(__ADDRESSBOOK__,'user','getDetailValues',array('id'=>$data['id']));
    foreach ($details as $key=>$value) {
        $data[$key] = $value;
    }

    $data['authid'] = xarSecGenAuthKey();
    $data['id'] = $data['id'];
    $data['confirmDeleteTEXT'] = xarML(_AB_CONFIRMDELETE);
    $data['buttonDelete'] = xarML(_AB_DELETE);
    $data['buttonCancel'] = xarML(_AB_CANCEL);

/* gehDEBUG: to be tossed
	if (!empty($lname)) {
		if (!empty($fname)) {
			$data->Text(xarVarPrepHTMLDisplay($fname).' '.xarVarPrepHTMLDisplay($lname));
			$data->Linebreak(1);
			if (!empty($company)) {
				$data->Text(xarVarPrepHTMLDisplay($company));
				$data->Linebreak(1);
			}
		}
		else {
			$data->Text(xarVarPrepHTMLDisplay($lname));
			$data->Linebreak(1);
			if (!empty($company)) {
				$data->Text(xarVarPrepHTMLDisplay($company));
				$data->Linebreak(1);
			}
		}
	}
	else {
		$data->Text(xarVarPrepHTMLDisplay($company));
		$data->Linebreak(1);
	}
	$data->Text('</div>');
	$data->Linebreak(1);
	$data->Text(AddressBook_themetable('end'));

	// Go back or delete
	$data->FormStart(xarModURL(__ADDRESSBOOK__, 'user', 'doDelete',$menuValues));
	$data->FormHidden('authid', xarSecGenAuthKey());
	$data->FormHidden('id', $id);
	$data->Linebreak(1);
	$data->Text(AddressBook_themetable('start'));
	$data->Text('<div align="center"><br>');
	$data->Text('<input type="submit" value="'.xarVarPrepHTMLDisplay(_AB_DELETE).'">&nbsp;&nbsp;&nbsp;');
	$data->Text('<input type="button" value="'.xarVarPrepHTMLDisplay(_AB_CANCEL).'" onclick="javascript:history.go(-1);"');
	$data->Text('<br><br></div>');
	$data->Text(AddressBook_themetable('end'));
    $data->FormEnd();
*/
    if (xarExceptionMajor() != XAR_NO_EXCEPTION) {
        // Got an exception
        if ((xarExceptionMajor() == XAR_SYSTEM_EXCEPTION) && !_AB_DEBUG) {
            return; // throw back
        } else {
                        // We are going to handle this exception REGARDLESS of the type
            $data['abExceptions'] = xarModAPIFunc(__ADDRESSBOOK__,'user','handleException');
        }
    }

	return $data;
	
} // END confirmDelete

?>