<?php
/**
 * AddressBook admin functions
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
 * Display form used to update the configuration settings
 * Handle the data submission
 *
 * @param GET / POST passed from modifyconfig form
 * @return array xarTemplate data
 */
function addressbook_admin_modifyconfig()
{
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
            /**
             * Data integrity / Security check
             */
            if (!xarSecConfirmAuthKey())
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

            // Security Settins
            if (!xarVarFetch ('guestmode_1','checkbox',$formData['guestmode_1'], 0)) return;
            if (!xarVarFetch ('guestmode_2','checkbox',$formData['guestmode_2'], 0)) return;
            if (!xarVarFetch ('guestmode_3','checkbox',$formData['guestmode_3'], 0)) return;

            if (!xarVarFetch ('usermode_1','checkbox',$formData['usermode_1'], 0)) return;
            if (!xarVarFetch ('usermode_2','checkbox',$formData['usermode_2'], 0)) return;
            if (!xarVarFetch ('usermode_3','checkbox',$formData['usermode_3'], 0)) return;

            // Other Settings
            if (!xarVarFetch ('abtitle','str::60',$formData['abtitle'], '', XARVAR_NOT_REQUIRED)) return;

            if (!xarVarFetch ('sortdata_1','str:1:',$formData['sortdata_1'])) return;
            if (!xarVarFetch ('sortdata_2','str:1:',$formData['sortdata_2'])) return;
            if (!xarVarFetch ('sortdata_3','str:1:',$formData['sortdata_3'])) return;
            if (!xarVarFetch ('sortdata_4','str:1:',$formData['sortdata_4'])) return;
            if (!xarVarFetch ('name_order','str:1:',$formData['name_order'], 0)) return;
            if (!xarVarFetch ('special_chars_1','str:1:24',$formData['special_chars_1'], '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch ('special_chars_2','str:1:24',$formData['special_chars_2'], '', XARVAR_NOT_REQUIRED)) return;

            if (!xarVarFetch ('globalprotect','checkbox',$formData['globalprotect'], 0)) return;
            if (!xarVarFetch ('use_prefix',   'checkbox',$formData['use_prefix'],    0)) return;
            if (!xarVarFetch ('display_prefix','checkbox',$formData['display_prefix'],0)) return;
            if (!xarVarFetch ('use_img',      'checkbox',$formData['use_img'],       0)) return;
            if (!xarVarFetch ('menu_off',     'str:1:',  $formData['menu_off'],      0)) return;
            if (!xarVarFetch ('menu_semi',    'checkbox',$formData['menu_semi'],     0)) return;
            if (!xarVarFetch ('zipbeforecity','checkbox',$formData['zipbeforecity'], 0)) return;
            if (!xarVarFetch ('itemsperpage','int:1:100',$formData['itemsperpage'],  30)) return;
            if (!xarVarFetch ('hidecopyright','checkbox',$formData['hidecopyright'], 0)) return;

            // Custom Labels
            if (!xarVarFetch ('custom_tab',  'str::60',      $formData['custom_tab'], '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch ('textareawidth','int:4:100',   $formData['textareawidth'], 60)) return;
            if (!xarVarFetch ('dateformat',  'str:1:',       $formData['dateformat'],    0)) return;
            if (!xarVarFetch ('numformat',   'str:1:',       $formData['numformat'],     '9,999.99')) return;

            // Admin messages
            if (!xarVarFetch ('rptErrAdminFlag','checkbox',$formData['rptErrAdminFlag'], 1)) return;
            if (!xarVarFetch ('rptErrAdminEmail','str:1:128',$formData['rptErrAdminEmail'], FALSE)) return;
            if (!xarVarFetch ('rptErrDevFlag','checkbox',$formData['rptErrDevFlag'], 1)) return;

            if (!xarModAPIFunc('addressbook','admin','updateconfig',$formData)) {
                return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
            }
        }
        // Thanks to Jason Judge <admin@academe.co.uk> for suggesting the
        // use of floor() & mod!!
        // Set values that will be displayed in the template
        $guestMode = xarModGetVar('addressbook', 'guestmode');
        $output['guestmode_1'] = $guestMode % 2;
        $output['guestmode_2'] = floor($guestMode / 2) % 2;
        $output['guestmode_3'] = floor($guestMode / 4) % 2;

        $userMode = xarModGetVar('addressbook', 'usermode');
        $output['usermode_1'] = $userMode % 2;
        $output['usermode_2'] = floor($userMode / 2) % 2;
        $output['usermode_3'] = floor($userMode / 4) % 2;

        // User Title for Address Book
        $output['abtitle'] = xarModGetVar('addressbook', 'abtitle');

        /**
         * Build Sort Order Options
         */
        // Get the default Sort Order
        $output['defSortCols'] = explode(',',xarModGetVar('addressbook', 'sortorder_1'));
        // Get Alternate Sort Order
        $output['altSortCols'] = explode(',',xarModGetVar('addressbook', 'sortorder_2'));

        // build the basic sort options
        $sortOptions = xarModAPIFunc('addressbook','util','getsortoptions');

        // Inclue custom fields in sorts & ordering
        $custom_tab = xarModGetVar('addressbook','custom_tab');
        if ((!empty($custom_tab)) && ($custom_tab != '')) {
            $custFieldLabels = xarModAPIFunc('addressbook','admin','getcustomfields');
            foreach($custFieldLabels as $custFieldLabel) {
                $sortOptions[] = array('id'=>$custFieldLabel['colName'], 'name'=>xarVarPrepHTMLDisplay($custFieldLabel['custLabel']));
            }
        }

        // Load the sort combo boxes
        $output['sortdata_1'] = $sortOptions;
        $output['sortdata_2'] = $sortOptions;
        $output['sortdata_3'] = $sortOptions;
        $output['sortdata_4'] = $sortOptions;

        //////////// End build sortOptions /////////////////////

        // Name display in list view & sort order
        $temp1 = xarML('Last name').', '.xarML('First name');
        $temp2 = xarML('First name').' '.xarML('Last name');
        $output['name_order'][] = array('id'=>0, 'name'=>$temp1);
        $output['name_order'][] = array('id'=>1, 'name'=>$temp2);
        $output['name_order_selected'] = xarModGetVar('addressbook', 'name_order');

        // Additional Settings
        $output['special_chars_1']  = xarModGetVar('addressbook', 'special_chars_1');
        $output['special_chars_2']  = xarModGetVar('addressbook', 'special_chars_2');

        $output['globalprotect']    = xarModGetVar('addressbook', 'globalprotect');
        $output['use_prefix']       = xarModGetVar('addressbook', 'use_prefix');
        $output['display_prefix']   = xarModGetVar('addressbook', 'display_prefix');
        $output['use_img']          = xarModGetVar('addressbook', 'use_img');

        // Disable / enable menu options
        $output['menu_off'][] = array('id'=>0, 'name'=>xarML('Enabled for all'));
        $output['menu_off'][] = array('id'=>1, 'name'=>xarML('Disabled for all'));
        $output['menu_off'][] = array('id'=>2, 'name'=>xarML('Disabled only for guests'));
        $output['menu_off_selected'] = (int) xarModGetVar('addressbook', 'menu_off');

        $output['menu_semi']        = xarModGetVar('addressbook', 'menu_semi');
        $output['zipbeforecity']    = xarModGetVar('addressbook', 'zipbeforecity');
        $output['itemsperpage']     = xarModGetVar('addressbook', 'itemsperpage');
        $output['hidecopyright']    = xarModGetVar('addressbook', 'hidecopyright');
        $output['custom_tab']       = xarModGetVar('addressbook', 'custom_tab');
        $output['textareawidth']    = xarModGetVar('addressbook', 'textareawidth');

        $output['dateformat'][] = array('id'=>0, 'name'=>xarVarPrepForDisplay(_AB_DATEFORMAT_1));
        $output['dateformat'][] = array('id'=>1, 'name'=>xarVarPrepForDisplay(_AB_DATEFORMAT_2));
        $output['dateformat_selected'] = xarModGetVar('addressbook', 'dateformat');

        $output['numformat'][] = array('id'=>'9,999.99', 'name'=>'9,999.99');
        $output['numformat'][] = array('id'=>'9.999,99', 'name'=>'9.999,99');
        $output['numformat_selected'] = xarModGetVar('addressbook', 'numformat');

        // Admin Message config
        $output['rptErrAdminFlag']    = xarModGetVar('addressbook', 'rptErrAdminFlag');
        $output['rptErrAdminEmail']   = xarModGetVar('addressbook', 'rptErrAdminEmail');
        $output['rptErrDevFlag']      = xarModGetVar('addressbook', 'rptErrDevFlag');

        // Generate a one-time authorisation code for this operation
        $output['authid'] = xarSecGenAuthKey();

        // Submit button
        $output['btnCommitText'] = xarML('Commit Changes');

    } else {
        return xarTplModule('addressbook','user','noauth');
    }

    return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

} // END modifyconfig

?>