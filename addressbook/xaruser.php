<?php
/**
 * File: $Id$
 *
 * AddressBook utility functions
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

//FIXME: until we figure out module globals
include_once ('modules/addressbook/xarglobal.php');

//=========================================================================
//  the main function
//=========================================================================
function AddressBook_user_main() {

    $data = xarModFunc(__ADDRESSBOOK__,'user','viewall');

//function xarTplModule($modName, $modType, $funcName, $tplData = array(), $templateName = NULL)

//    return xarTplModule(__ADDRESSBOOK__,'user','viewall',$data);

    if (xarExceptionMajor() != XAR_NO_EXCEPTION) {
        // Got an exception
        if ((xarExceptionMajor() == XAR_SYSTEM_EXCEPTION) && !_AB_DEBUG) {
            return; // throw back
        } else {
            // We are going to handle this exception REGARDLESS of the type
            $data['abExceptions'] = xarModAPIFunc(__ADDRESSBOOK__,'user','handleException');
        }
    }

    return $data; //gehDEBUG
} // END main

//=========================================================================
//  Show all contacts
//=========================================================================
function AddressBook_user_viewall($args) {

    extract($args);

    $data['userIsLoggedIn'] = xarUserIsLoggedIn();
    $data['globalprotect'] = xarModGetVar(__ADDRESSBOOK__, 'globalprotect');
    $data['userCanViewModule'] = xarSecurityCheck('ViewAddressBook',0);

    /**
     * not sure how this differs from xarSecurityCheck above...
     */
    $data['userCanViewEntries'] = xarModAPIFunc(__ADDRESSBOOK__,'user','checkAccessLevel',array('option'=>'view'));

    /**
     * Get menu values from the input
     */
    $menuValues = xarModAPIFunc(__ADDRESSBOOK__,'user','getMenuValues');
    foreach ($menuValues as $key=>$value) {
        $data[$key] = $value;
    }

    /**
     * Print the main menu (could this be a block??)
     */
    $data = xarModAPIFunc(__ADDRESSBOOK__,'user','getMenu',array('data'=>$data));

	// Start Page

    $data = xarModAPIFunc(__ADDRESSBOOK__,'user','getAddressList',array('data'=>$data));

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

} // END viewall

//=========================================================================
//  Show detail page
//=========================================================================
function AddressBook_user_viewdetail() {

    $data = array();

    /**
     * Retrieve data from submitted input / URL
     */
    $data = xarModAPIFunc(__ADDRESSBOOK__,'user','getsubmitvalues', array('data' => $data));

    /**
     * Retrieve any config values needed to configure the page
     */
    $data['zipbeforecity'] = pnModGetVar(__ADDRESSBOOK__,'zipbeforecity');

    // Get detailed values from database
    $details = xarModAPIFunc(__ADDRESSBOOK__,'user','getDetailValues',array('id'=>$data['id']));
    foreach ($details as $key=>$value) {
        $data[$key] = $value;
    }

    // Get the labels
    $labels = xarModAPIFunc(__ADDRESSBOOK__,'user','getLabels');

    // General information
    // headline
    $cats = xarModAPIFunc(__ADDRESSBOOK__,'user','getFormCategories');
    $data['info'] = xarVarPrepHTMLDisplay(_AB_CATEGORY.': '._AB_UNFILED);
    if ($data['cat_id'] > 0) {
        foreach ($cats as $c) {
            if ($data['cat_id'] == $c['nr']) {
                $data['info'] = xarVarPrepHTMLDisplay(_AB_CATEGORY.': '.$c['name']);
            }
        }
    }

    if ($data['date'] > 0) {
        $data['info'] .= ' | '.xarVarPrepHTMLDisplay(_AB_LASTCHANGED)
                               .xarModAPIFunc(__ADDRESSBOOK__,'util','ml_ftime',
                                                            array ('datefmt' =>_DATETIMEBRIEF
                                                                  ,'timestamp'=>$data['date']));
    }

    // Format the Contat info for display
    $data['contacts'] = array();
    for ($i=1;$i<6;$i++) {
        $contact = array();
        $the_contact = 'contact_'.$i;
        $the_label = 'c_label_'.$i;
        if (!empty($data[$the_contact])) {
            foreach ($labels as $lab) {
                if ($data[$the_label] == $lab['nr']) {
                    $contact['label'] = xarVarPrepHTMLDisplay($lab['name']);
                    if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_email',array('email'=>$data[$the_contact]))) {
                        if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_url',array('url'=>$data[$the_contact]))) {
                            $contact['contact'] = xarVarPrepHTMLDisplay($data[$the_contact]);
                        }
                        else {
                            $contact['contact'] = '<a href="'.xarVarPrepHTMLDisplay($data[$the_contact]).'" target="_blank">'.xarVarPrepHTMLDisplay($data[$the_contact]).'</a>';
                        }
                    }
                    else {
                        $contact['contact'] = '<a href="mailto:'.xarVarPrepHTMLDisplay($data[$the_contact]).'">'.xarVarPrepHTMLDisplay($data[$the_contact]).'</a>';
                    }
                }
            }
            $data['contacts'][] = $contact;
        }
    } // END for

    /**
     * Display Image
     *
     * Nothing to do here / all handled by template now
     */

    /**
     * Custom information
     */
    $custom_tab = xarModGetVar(__ADDRESSBOOK__,'custom_tab');
    if ((!empty($custom_tab)) || ($custom_tab != '')) {

        $data['custom_tab'] = $custom_tab;
//        $custUserData = xarModAPIFunc(__ADDRESSBOOK__,'user','getCustUserData',array('id'=>$data['id']
//                                                                                       ,'flag'=>_AB_CUST_DATAONLY));


/* gehDEBUG
        $hasValues = false;
        foreach($cus_fields as $cus) {
            if ((!empty($cus['value'])) && ($cus['value'] != '')) {
                $hasValues = true;
                break;
            }
        }
        if ((!strstr($cus['type'],_AB_CUST_TEST_LB)) && (!strstr($cus['type'],_AB_CUST_TEST_HR))) {
            $hasValues = true;
        }
        if ($hasValues) {
*/
/* gehDEBUG - need to fix the formatting here

            foreach($custUserData as $userData) {
                if ($userData['type']=='date default NULL') {
                    $userData['userData'] = xarModAPIFunc(__ADDRESSBOOK__,'user','stamp2date',array('idate'=>$userData['userData']));
                } elseif ($userData['type']=='decimal(10,2) default NULL') {
                    $userData['userData'] = xarModAPIFunc(__ADDRESSBOOK__,'user','num4display',array('inum'=>$userData['userData']));
                } elseif ((strstr($userData['type'],_AB_CUST_TEST_LB)) || (strstr($userData['type'],_AB_CUST_TEST_LB))) {
                    if (strstr($userData['type'],_AB_CUST_TEST_LB)) {
                        $userData['userData'] = _AB_HTML_LINEBREAK;
                    } else {
                        $userData['userData'] = _AB_HTML_HORIZRULE;
                    }
                } elseif (!empty($userData['userData'])) {
                    $userData['type'] = xarVarPrepHTMLDisplay($userData['type']);
                    $userData['userData'] = xarVarPrepHTMLDisplay(nl2br($userData['userData']));
                } else {
                    $userData['type'] = xarVarPrepHTMLDisplay($userData['type']);
                    $userData['userData'] = '&nbsp;';
                }

                $data['custUserData'][] = $userData;

            } // END foreach
*/
//            echo "ID = ".$data['id']; print_r($data['custUserData']);die(); //gehDEBUG

//        }
    } // END if

    /**
     * Notes
     */
    if (!empty($data['note'])) {

        // headline
        $data['noteHeading'] = xarVarPrepHTMLDisplay(_AB_NOTETAB);

        $data['note'] = xarVarPrepHTMLDisplay(nl2br($data['note']));
    }

    /**
     * Navigation buttons
     */
    // Copy to clipboard if IE
    if (xarModAPIFunc(__ADDRESSBOOK__,'user','checkForIE')) {
        $clip='';
        if (!empty($data['company'])) {$clip.=$data['company'].'\n'; }
        if (!empty($data['lname'])) {
            if (!empty($data['fname'])) {$clip.=$data['fname'].' '.$data['lname'].'\n'; }
            else { $clip .= $data['lname'].'\n'; }
        }
        if (!empty($data['address_1'])) {$clip.=$data['address_1'].'\n'; }
        if (!empty($data['address_2'])) {$clip.=$data['address_2'].'\n'; }
        if ($data['zipbeforecity']) {
            if (!empty($data['zip'])) {$clip.=$data['zip'].' '; }
            if (!empty($data['city'])) {$clip.=$data['city'].'\n'; }
            if (!empty($data['state'])) {$clip.=$data['state'].'\n'; }
            if (!empty($data['country'])) {$clip.=$data['country'].'\n'; }
        }
        else {
            if (!empty($data['city'])) {$clip.=$data['city'].'\n'; }
            if (!empty($data['state'])) {$clip.=$data['state'].'\n'; }
            if (!empty($data['zip'])) {$clip.=$data['zip'].'\n'; }
            if (!empty($data['country'])) {$clip.=$data['country'].'\n'; }
        }
        $data['clip'] = $clip;
        $data['copy2clipboard'] = xarVarPrepHTMLDisplay(_AB_COPY);
    }

    $data['goBack'] = xarVarPrepHTMLDisplay(_AB_GOBACK);

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

} // END viewdetail

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
}

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
}

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

/*
    foreach($cus_fields as $cus) {
        $output->TableRowStart();
        $the_name = 'custom_'.$cus['nr'];
        switch ($cus['type']) {
            case 'varchar(60) default NULL':
                $cus['name'] = xarVarPrepHTMLDisplay($cus['name']);
                $cus['value'] = xarVarCleanFromInput($the_name);
                $cus['the_name'] = $the_name;
                break;
            case 'varchar(120) default NULL':
                $output->TableColStart(1,'left','top');
                $output->Text(pnVarPrepHTMLDisplay($cus['name']).':');
                $output->TableColEnd();
                $output->TableColStart(1,'left','top');
                $output->Text('<textarea name="'.$the_name.'" rows="2" cols="'.$textareawidth.'" onkeyup="TrackCount(this,120)" onkeypress="LimitText(this,120)" STYLE="overflow:hidden;">'.pnVarCleanFromInput($the_name).'</textarea>');
                break;
            case 'varchar(240) default NULL':
                $output->TableColStart(1,'left','top');
                $output->Text(pnVarPrepHTMLDisplay($cus['name']).':');
                $output->TableColEnd();
                $output->TableColStart(1,'left','top');
                $output->Text('<textarea name="'.$the_name.'" rows="4" cols="'.$textareawidth.'" onkeyup="TrackCount(this,240)" onkeypress="LimitText(this,240)" STYLE="overflow:hidden;">'.pnVarCleanFromInput($the_name).'</textarea>');
                break;
            case 'decimal(10,2) default NULL':
                $output->TableColStart(1,'left','middle');
                $output->Text(pnVarPrepHTMLDisplay($cus['name']).':');
                $output->TableColEnd();
                $output->TableColStart(1,'left','middle');
                $output->FormText($the_name,pnVarCleanFromInput($the_name),12,12);
                break;
            case 'int default NULL':
                $output->TableColStart(1,'left','middle');
                $output->Text(pnVarPrepHTMLDisplay($cus['name']).':');
                $output->TableColEnd();
                $output->TableColStart(1,'left','middle');
                $output->FormText($the_name,pnVarCleanFromInput($the_name),9,9);
                break;
            case 'date default NULL':
                $output->TableColStart(1,'left','middle');
                $output->Text(pnVarPrepHTMLDisplay($cus['name']).':');
                $output->TableColEnd();
                $output->TableColStart(1,'left','middle');
                $output->Text('<table border="0" cellpadding="0" cellspacing="0"><tr><td>');
                $output->FormText($the_name,pnVarCleanFromInput($the_name),10,10);
                if (pnModGetVar(__PNADDRESSBOOK__,'dateformat') == 0) {
                    $output->Text('</td><td>&nbsp;&nbsp;('.pnVarPrepHTMLDisplay(_pnAB_DATEFORMAT_1).')</td></tr></table>');
                }
                else {
                    $output->Text('</td><td>&nbsp;&nbsp;('.pnVarPrepHTMLDisplay(_pnAB_DATEFORMAT_2).')</td></tr></table>');
                }
                break;
            case 'tinyint default NULL':
                $output->TableColStart(2,'left','middle');
                $output->Text(pnVarPrepHTMLDisplay('<br>'));
                break;
            case 'smallint default NULL':
                $output->TableColStart(2,'left','middle');
                $output->Text(pnVarPrepHTMLDisplay('<hr>'));
                break;
            default:
                $output->TableColStart(1,'left','middle');
                $output->Text(pnVarPrepHTMLDisplay($cus['name']).':');
                $output->TableColEnd();
                $output->TableColStart(1,'left','middle');
                $output->FormText($the_name,pnVarCleanFromInput($the_name),60,60);
                break;
        }
    } // END foreach
*/
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