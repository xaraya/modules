<?php
/**
 * File: $Id: viewdetail.php,v 1.1 2003/07/02 07:31:18 garrett Exp $
 *
 * AddressBook user viewDetail
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
 * Display details
 */
function AddressBook_user_viewdetail() {

    $output = array();

    /**
     * Retrieve data from submitted input / URL
     */
    $output = xarModAPIFunc(__ADDRESSBOOK__,'user','getsubmitvalues', array('data' => $output));

    /**
     * Retrieve any config values needed to configure the page
     */
    $output['zipbeforecity'] = pnModGetVar(__ADDRESSBOOK__,'zipbeforecity');

    // Get detailed values from database
    $details = xarModAPIFunc(__ADDRESSBOOK__,'user','getDetailValues',array('id'=>$output['id']));
    foreach ($details as $key=>$value) {
        $output[$key] = $value;
    }

    // Get the labels
    $labels = xarModAPIFunc(__ADDRESSBOOK__,'user','getLabels');

    // General information
    // headline
    $cats = xarModAPIFunc(__ADDRESSBOOK__,'user','getFormCategories');
    $output['info'] = xarVarPrepHTMLDisplay(_AB_CATEGORY.': '._AB_UNFILED);
    if ($output['cat_id'] > 0) {
        foreach ($cats as $c) {
            if ($output['cat_id'] == $c['nr']) {
                $output['info'] = xarVarPrepHTMLDisplay(_AB_CATEGORY.': '.$c['name']);
            }
        }
    }

    if ($output['date'] > 0) {
        $output['info'] .= ' | '.xarVarPrepHTMLDisplay(_AB_LASTCHANGED)
                               .xarModAPIFunc(__ADDRESSBOOK__,'util','ml_ftime',
                                                            array ('datefmt' =>_DATETIMEBRIEF
                                                                  ,'timestamp'=>$output['date']));
    }

    // Format the Contat info for display
    $output['contacts'] = array();
    for ($i=1;$i<6;$i++) {
        $contact = array();
        $the_contact = 'contact_'.$i;
        $the_label = 'c_label_'.$i;
        if (!empty($output[$the_contact])) {
        	//FIXME:<garrett> if there is a record with a set of contact labels
        	// and ALL those labels were deleted & new ones added, this will fail to
        	// to build the contact array because none of the old labels['nr'] will
        	// be found in the new label list.
            foreach ($labels as $lab) {
                if ($output[$the_label] == $lab['nr']) {
                    $contact['label'] = xarVarPrepHTMLDisplay($lab['name']);
                    if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_email',array('email'=>$output[$the_contact]))) {
                        if(!xarModAPIFunc(__ADDRESSBOOK__,'user','is_url',array('url'=>$output[$the_contact]))) {
                            $contact['contact'] = xarVarPrepHTMLDisplay($output[$the_contact]);
                        }
                        else {
                            $contact['contact'] = '<a href="'.xarVarPrepHTMLDisplay($output[$the_contact]).'" target="_blank">'.xarVarPrepHTMLDisplay($output[$the_contact]).'</a>';
                        }
                    }
                    else {
                        $contact['contact'] = '<a href="mailto:'.xarVarPrepHTMLDisplay($output[$the_contact]).'">'.xarVarPrepHTMLDisplay($output[$the_contact]).'</a>';
                    }
                }
            }
            $output['contacts'][] = $contact;
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

        $output['custom_tab'] = $custom_tab;
        $custUserData = xarModAPIFunc(__ADDRESSBOOK__,'user','getCustFieldInfo',
        								array('id'=>$output['id']
                                             ,'flag'=>_AB_CUST_ALLINFO));

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

                $output['custUserData'][] = $userData;

            } // END foreach
*/

//        }
    } // END if

    /**
     * Notes
     */
    if (!empty($output['note'])) {

        // headline
        $output['noteHeading'] = xarVarPrepHTMLDisplay(_AB_NOTETAB);

        $output['note'] = xarVarPrepHTMLDisplay(nl2br($output['note']));
    }

    /**
     * Navigation buttons
     */
    // Copy to clipboard if IE
    if (xarModAPIFunc(__ADDRESSBOOK__,'user','checkForIE')) {
        $clip='';
        if (!empty($output['company'])) {$clip.=$output['company'].'\n'; }
        if (!empty($output['lname'])) {
            if (!empty($output['fname'])) {$clip.=$output['fname'].' '.$output['lname'].'\n'; }
            else { $clip .= $output['lname'].'\n'; }
        }
        if (!empty($output['address_1'])) {$clip.=$output['address_1'].'\n'; }
        if (!empty($output['address_2'])) {$clip.=$output['address_2'].'\n'; }
        if ($output['zipbeforecity']) {
            if (!empty($output['zip'])) {$clip.=$output['zip'].' '; }
            if (!empty($output['city'])) {$clip.=$output['city'].'\n'; }
            if (!empty($output['state'])) {$clip.=$output['state'].'\n'; }
            if (!empty($output['country'])) {$clip.=$output['country'].'\n'; }
        }
        else {
            if (!empty($output['city'])) {$clip.=$output['city'].'\n'; }
            if (!empty($output['state'])) {$clip.=$output['state'].'\n'; }
            if (!empty($output['zip'])) {$clip.=$output['zip'].'\n'; }
            if (!empty($output['country'])) {$clip.=$output['country'].'\n'; }
        }
        $output['clip'] = $clip;
        $output['copy2clipboard'] = xarVarPrepHTMLDisplay(_AB_COPY);
    }

    $output['goBack'] = xarVarPrepHTMLDisplay(_AB_GOBACK);
 
	return xarModAPIFunc(__ADDRESSBOOK__,'util','handleException',array('output'=>$output));

} // END viewdetail

?>
