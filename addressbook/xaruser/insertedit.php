<?php
/**
 * File: $Id: insertedit.php,v 1.1 2003/07/02 07:31:18 garrett Exp $
 *
 * AddressBook user insertEdit
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
//  Insert/Edit form
//=========================================================================
function AddressBook_user_insertedit() {


//gehDEBUG - lose for now
     //Security Check
//    if (!xarSecConfirmAuthKey()) {
//        xarExceptionSet(XAR_USER_EXCEPTION, _AB_ERR_ERROR,
//            new DefaultUserException("Invalid Auth Key")); return;
//    }


    $data['userIsLoggedIn'] = xarUserIsLoggedIn();
    $data['userCanViewModule'] = xarSecurityCheck('ViewAddressBook',0);

    $data['globalprotect'] = xarModGetVar(__ADDRESSBOOK__, 'globalprotect');
    $data['custom_tab'] = xarModGetVar(__ADDRESSBOOK__,'custom_tab');

    if (empty($data['custom_tab'])) {
        $data['addrow'] = 0;
    }  else {
        $data['addrow'] = 1;
    }
    $data['numcols'] = _AB_NUM_COLS + $data['addrow'];

    /**
     * not sure how this differs from xarSecurityCheck above...
     */
    $data['userCanEditEntries'] = xarModAPIFunc(__ADDRESSBOOK__,'user','checkAccessLevel',array('option'=>'edit'));

    $data['authid'] = xarSecGenAuthKey();

    /**
     * Get everything from the input
     */
    // Insert or Edit
    if (!xarVarFetch ('formcall','str::',$data['formcall'], 'insert')) return;

    /**
     * read in the menu values for preservation & display
     */
    $data['menuValues'] = xarModAPIFunc(__ADDRESSBOOK__,'user','getMenuValues');
    foreach ($data['menuValues'] as $key=>$value) {
        $data[$key] = $value;
    }

	// Get the form values
	$data = xarModAPIFunc(__ADDRESSBOOK__,'user','getsubmitvalues',array('data'=>$data));

    if ($data['addrow'] && !$data['formSubmitted']) {
        if ($data['formcall'] == 'edit') {
            $data['custUserData'] = xarModAPIFunc(__ADDRESSBOOK__,'user','getCustFieldInfo',array('id'=>$data['id']));
        }
        else {
            $data['custUserData'] = xarModAPIFunc(__ADDRESSBOOK__,'user','getCustFieldInfo');
        }
    }

	if ($data['id'] > 0 && !$data['formSubmitted']) {
		// Get detailed values from database
		$details = xarModAPIFunc(__ADDRESSBOOK__,'user','getDetailValues',array('id'=>$data['id']));
        foreach ($details as $key=>$value) {
            $data[$key] = $value;
        }
	}

    switch ($data['formcall']) {
        case 'edit':    $data['btnCommitID'] = "update"; $data['btnCommitTitle'] = "Update"; break;
        case 'insert':  $data['btnCommitID'] = "insert"; $data['btnCommitTitle'] = "Insert"; break;
    }

/**
 * Format data that is displayed across all sub-templates
 */
    $cats = xarModAPIFunc(__ADDRESSBOOK__,'user','getFormCategories');
    $data['cats'] = array();
    $data['cats'][] = array('id'=>'0',
                          'name'=>_AB_UNFILED);

    foreach($cats as $cat) {
        $data['cats'][] = array('id'=>$cat['nr'],
                              'name'=>$cat['name']);
    }

/**
 * Perform custom processing per sub-template
 */
    switch ($data['action']) {
        case _AB_TEMPLATE_NAME:
            $prfxs = xarModAPIFunc(__ADDRESSBOOK__,'user','getFormPrefixes');
            $data['prfxs'] = array();
            $data['prfxs'][] = array('id'=>'0',
                                     'name'=>_AB_NOPREFIX);

            foreach($prfxs as $prfx) {
                $data['prfxs'][] = array('id'=>$prfx['nr'],
                                         'name'=>$prfx['name']);
            }

            /**
             * Company auto fill dropdown
             */
            $data['companies'] = xarModAPIFunc(__ADDRESSBOOK__,'user','getCompanies');

            break;

        case _AB_TEMPLATE_ADDR:
                if (!xarVarFetch('autoCompanyFill','int::',$autoCompanyFill,FALSE)) return;
                if (!xarVarFetch('comp_lookup',    'int::',$comp_lookup,FALSE)) return;
                if ($autoCompanyFill) {
                    if (!xarVarFetch('companyId','int::',$companyId,FALSE)) return;
                    $comp_id = xarVarCleanFromInput('comp_lookup');
                    $compaddress = xarModAPIFunc(__ADDRESSBOOK__,'user','getCompanyAddress',array('id'=>$comp_id));

                    foreach($compaddress as $fieldName=>$value) {
                        $data[$fieldName] = $value;
                    }
                }

            break;

        case _AB_TEMPLATE_CONTACT:
            $labels = xarModAPIFunc(__ADDRESSBOOK__,'user','getLabels');
            $data['labels'] = array();
            foreach($labels as $label) {
                $data['labels'][] = array('id'=>$label['nr'],
                                         'name'=>$label['name']);
            }
            break;

        case _AB_TEMPLATE_CUST:
            $data['dateformat_1'] = _AB_DATEFORMAT_1;
            $data['dateformat_2'] = _AB_DATEFORMAT_2;
            $data['textareawidth'] = xarModGetVar(__ADDRESSBOOK__,'textareawidth');
            break;

        case _AB_TEMPLATE_NOTE:
            break;

    } // END swtich

    /**
     * Preserve menu vars from page to page
     */
	$data['menuValues'] = array('catview'   =>$data['catview'],
                    'menuprivate'=>$data['menuprivate'],
                    'all'       =>$data['all'],
                    'sortview'  =>$data['sortview'],
                    'page'      =>$data['page'],
                    'char'      =>$data['char'],
                    'total'     =>$data['total']);


	// Cancelled??
	if (xarVarCleanFromInput('cancel')) {
		xarResponseRedirect(xarModURL(__ADDRESSBOOK__, 'user', 'viewall',$data['menuValues']));
		return true;
	}

	// Update button clicked?
	if (xarVarCleanFromInput('update')) {

//gehDEBUG
         //Security Check
//        if (!xarSecConfirmAuthKey()) {
//            xarExceptionSet(XAR_USER_EXCEPTION, _AB_ERR_ERROR,
//                new DefaultUserException("Invalid Auth Key")); return $data;
//        }

		$check1=xarModAPIFunc(__ADDRESSBOOK__,'user','checksubmitvalues',$data);
		if ($check1) {
			$data['msg'] = "update: ".xarVarPrepHTMLDisplay($check1);
		}
		else {

			$check2=xarModAPIFunc(__ADDRESSBOOK__,'user','updaterecord',$data);
			if (!$check2) {
                $data['msg'] = "update: ".xarVarPrepHTMLDisplay(xarML(_AB_UPDATE_ERROR));
//				return $data;
			}
			else {
				$data['menuValues'] = xarModAPIFunc(__ADDRESSBOOK__,'user','getMenuValues');
				xarResponseRedirect(xarModURL(__ADDRESSBOOK__, 'user', 'viewall',array ('data'=>$data['menuValues'])));
				return true;
			}

		}
	}

	// Insert button clicked?
	if (xarVarCleanFromInput('insert')) {
		$check1=xarModAPIFunc(__ADDRESSBOOK__,'user','checksubmitvalues',$data);
		if ($check1) {
			$data['msg'] = "insert: ".xarVarPrepHTMLDisplay($check1);
		}
		else {
			$check2=xarModAPIFunc(__ADDRESSBOOK__,'user','insertrecord',$data);
			if (!$check2) {
				 $data['msg'] = xarVarPrepHTMLDisplay(_AB_INSERT_ERROR);
				 return $data;
			}
			else {
                $data['insertStatus'] = TRUE;
                $data['insertSuccess'] = xarML(_AB_INSERT_AB_SUCCESS);
                $data['newAddrLinkTEXT'] = xarML(_AB_MENU_ADD);
                $data['backToListTEXT'] = xarML(_AB_GOBACK);

                return $data;
                // Some error handling being done here that should be
                // incorporated back into the module
                // gehDEBUG
                /*
				$data['menuValues']=array('formcall'=>'insert','authid'=>xarSecGenAuthKey(),'catview'=>$data['catview'],'menuprivate'=>$data['menuprivate'],'all'=>$data['all'],'sortview'=>$data['sortview'],'page'=>$data['page'],'char'=>$data['char'],'total'=>$data['total']);
				$data->Text(AddressBook_themetable('start'));
				$data->Linebreak(1);
				$data->Text('<div align="center">');
				$data->Text(xarVarPrepHTMLDisplay('<b>'._AB_INSERT_AB_SUCCESS.'</b>'));
				$addTXT	= xarVarPrepHTMLDisplay(_AB_MENU_ADD);
				$addURL = xarModURL(__ADDRESSBOOK__,'user','insertedit',$data['menuValues']);
				$backTXT = xarVarPrepHTMLDisplay('['._AB_GOBACK.']');
				$backURL = xarModURL(__ADDRESSBOOK__,'user','viewall',$data['menuValues']);
				$data->Linebreak(2);
				if(xarModAPIFunc(__ADDRESSBOOK__,'user','checkAccessLevel',array('option'=>'create'))) {
					$data->URL($addURL,$addTXT);
					$data->Text('&nbsp;&nbsp;&nbsp;');
				}
				$data->URL($backURL,$backTXT);
				$data->Text('</div>');
				$data->Linebreak(1);
				$data->Text(AddressBook_themetable('end'));
				return $data->GetOutput();
                */
			}
		}
	}


    // Get user id
    if (xarUserIsLoggedIn()) {
        $data['user_id'] = xarUserGetVar('uid');
    }
    // END custom field handling

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

} // END insertedit

?>