<?php
/**
 * AddressBook user insertEdit
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

//=========================================================================
//  Insert/Edit form
//=========================================================================
function addressbook_user_insertedit()
{

    $output = array();

    /**
     * Security check first
     */
    if (xarSecurityCheck('AddAddressBook',0)   ||
        xarSecurityCheck('EditAddressBook',0)) {

        // Get the form values
        $output = xarModAPIFunc('addressbook','user','getsubmitvalues',array('output'=>$output));

        /**
         * read in the menu values for preservation & display
         */
        $output['menuValues'] = xarModAPIFunc('addressbook','user','getmenuvalues');
        foreach ($output['menuValues'] as $key=>$value) {
            $output[$key] = $value;
        }

        /*
         * Check if we've come in via a submit, commit everything and cary on
         */
        if (!xarVarFetch ('cancel','str::',$cancel, FALSE))
            return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
        if (!xarVarFetch ('update','str::',$update, FALSE))
            return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
        if (!xarVarFetch ('insert','str::',$insert, FALSE))
            return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

        // Insert or Edit
        if (!xarVarFetch ('formcall','str::',$output['formcall'], 'insert', XARVAR_NOT_REQUIRED))
            return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));


        if (!empty($cancel)) {
            xarResponseRedirect(xarModURL('addressbook', 'user', 'viewall',$output['menuValues']));
            return true;
        } elseif (!empty($update)) {
            /**
             * Check for an update
             */

         //Security Check
            if (!xarSecConfirmAuthKey()) {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }

            if (xarModAPIFunc('addressbook','user','checksubmitvalues',$output)) {
                if (xarModAPIFunc('addressbook','user','updaterecord',$output)) {
                    $output['menuValues'] = xarModAPIFunc('addressbook','user','getmenuvalues');
                    xarResponseRedirect(xarModURL('addressbook', 'user', 'viewall',$output['menuValues']));
                    return true;
                }
            } else {
                $output['action'] = _AB_TEMPLATE_NAME;
            }
        } elseif (!empty($insert)) {
            /**
             * Was the insert button clicked
             */

             //Security Check
            if (!xarSecConfirmAuthKey()) {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }

            if (xarModAPIFunc('addressbook','user','checksubmitvalues',$output)) {
                if (xarModAPIFunc('addressbook','user','insertrecord',$output)) {
                    $output['insertStatus'] = TRUE;
                    $output['insertSuccess'] = xarML('Address Book Entry saved!');
                    $output['newAddrLinkTEXT'] = xarML('Add new address');
                    $output['backToListTEXT'] = xarML('Back to list');

                    return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

                    // Some error handling being done here that should be
                    // incorporated back into the module
                    // gehDEBUG
                    /*
                    $output['menuValues']=array('formcall'=>'insert','authid'=>xarSecGenAuthKey(),'catview'=>$output['catview'],'menuprivate'=>$output['menuprivate'],'all'=>$output['all'],'sortview'=>$output['sortview'],'page'=>$output['page'],'char'=>$output['char'],'total'=>$output['total']);
                    $output->Text(addressbook_themetable('start'));
                    $output->Linebreak(1);
                    $output->Text('<div align="center">');
                    $output->Text(xarVarPrepHTMLDisplay('<b>'.xarML('Address Book Entry saved!').'</b>'));
                    $addTXT = xarML('Add new address');
                    $addURL = xarModURL('addressbook','user','insertedit',$output['menuValues']);
                    $backTXT = xarVarPrepHTMLDisplay('['.xarML('Back to list').']');
                    $backURL = xarModURL('addressbook','user','viewall',$output['menuValues']);
                    $output->Linebreak(2);
                    if(xarModAPIFunc('addressbook','user','checkaccesslevel',array('option'=>'create'))) {
                        $output->URL($addURL,$addTXT);
                        $output->Text('&nbsp;&nbsp;&nbsp;');
                    }
                    $output->URL($backURL,$backTXT);
                    $output->Text('</div>');
                    $output->Linebreak(1);
                    $output->Text(addressbook_themetable('end'));
                    return $output->GetOutput();
                    */
                }
            } else {
                $output['action'] = _AB_TEMPLATE_NAME; //return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }

        } // END submit checks

        $output['globalprotect'] = xarModGetVar('addressbook', 'globalprotect');
        $output['custom_tab'] = xarModGetVar('addressbook','custom_tab');

        /**
         * Configure for custom field tab
         */
        if (empty($output['custom_tab'])) {
            $output['addrow'] = 0;
        }  else {
            $output['addrow'] = 1;
        }
        $output['numcols'] = _AB_NUM_COLS + $output['addrow'];

        /**
         * not sure how this differs from xarSecurityCheck above...
         */
    //    $output['userCanEditEntries'] = xarModAPIFunc('addressbook','user','checkaccesslevel',array('option'=>'edit'));
        $output['authid'] = xarSecGenAuthKey();

        /**
         * Get everything from the input
         */
        if ($output['addrow'] && !$output['formSubmitted']) {
            if ($output['formcall'] == 'edit') {
                $output['custUserData'] = xarModAPIFunc('addressbook','user','getcustfieldinfo',array('id'=>$output['id']));
            }
            else {
                $output['custUserData'] = xarModAPIFunc('addressbook','user','getcustfieldinfo');
            }
        }

        if ($output['id'] > 0 && !$output['formSubmitted']) {
            // Get detailed values from database
            $details = xarModAPIFunc('addressbook','user','getdetailvalues',array('id'=>$output['id']));
            foreach ($details as $key=>$value) {
                $output[$key] = $value;
            }
        }

        switch ($output['formcall']) {
            case 'edit':    $output['btnCommitID'] = "update"; $output['btnCommitTitle'] = "Update"; break;
            case 'insert':  $output['btnCommitID'] = "insert"; $output['btnCommitTitle'] = "Insert"; break;
        }

    /**
     * Format data that is displayed across all sub-templates
     */
        $output['cats'] = xarModAPIFunc('addressbook','util','getitems',array('tablename'=>'categories'));

    /**
     * Perform custom processing per sub-template
     */
        switch ($output['action']) {
            case _AB_TEMPLATE_NAME:
                $output['prfxs'] = xarModAPIFunc('addressbook','util','getitems',array('tablename'=>'prefixes'));

                /**
                 * Handle images
                 */
                if (xarModGetVar('addressbook','use_img') && xarSecurityCheck('ReadAddressBook',0)) {
                    $modInfo = xarModGetInfo(xarModGetIDFromName('addressbook'));
                    $handle = @opendir("modules/".$modInfo['directory']."/xarimages");
                    $output['imgFiles'][] = array('id'=>'','name'=>xarML('No Image'));
                    while ($file = @readdir ($handle)) {
                        if (eregi("^\.{1,2}$",$file)) {
                            continue;
                        }
                        else {
                            $output['imgFiles'][] = array('id'=>$file, 'name'=>$file);
                        }
                    }
                    @closedir($handle);
                }

                /**
                 * Company auto fill dropdown
                 */
                $output['companies'] = xarModAPIFunc('addressbook','user','getcompanies');

                break;

            case _AB_TEMPLATE_ADDR:
                if (!xarVarFetch('autoCompanyFill','int::',$autoCompanyFill,FALSE)) return;
                if (!xarVarFetch('comp_lookup',    'int::',$comp_lookup,FALSE)) return;
                if ($autoCompanyFill) {
                    if (!xarVarFetch('companyId','int::',$companyId,FALSE)) return;
                    if (!xarVarFetch ('comp_lookup','str::',$comp_id, FALSE)) return;
                    $compaddress = xarModAPIFunc('addressbook','user','getcompanyaddress',array('id'=>$comp_id));

                    foreach($compaddress as $fieldName=>$value) {
                        $output[$fieldName] = $value;
                    }
                }

                break;

            case _AB_TEMPLATE_CONTACT:
                $output['labels'] = xarModAPIFunc('addressbook','util','getitems',array('tablename'=>'labels'));
                break;

            case _AB_TEMPLATE_CUST:
                $output['dateformat_1'] = _AB_DATEFORMAT_1;
                $output['dateformat_2'] = _AB_DATEFORMAT_2;
                $output['textareawidth'] = xarModGetVar('addressbook','textareawidth');
                break;

            case _AB_TEMPLATE_NOTE:
                break;

        } // END swtich

        /**
         * Preserve menu vars from page to page
         */
        $output['menuValues'] = array('catview'   =>$output['catview'],
                                      'menuprivate'=>$output['menuprivate'],
                                      'all'       =>$output['all'],
                                      'sortview'  =>$output['sortview'],
                                      'page'      =>$output['page'],
                                      'char'      =>$output['char'],
                                      'total'     =>$output['total']);

        // Get user id
        if (xarUserIsLoggedIn()) {
            $output['user_id'] = xarUserGetVar('uid');
        }
        // END custom field handling

    } // END SecurityCheck

    return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

} // END insertedit

?>