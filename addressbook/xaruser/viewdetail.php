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

?>