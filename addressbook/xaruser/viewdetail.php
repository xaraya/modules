<?php
/**
 * File: $Id: viewdetail.php,v 1.4 2003/07/09 17:52:58 garrett Exp $
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
    $output['abModInfo'] = xarModGetInfo(xarModGetIDFromName(__ADDRESSBOOK__));

    /**
     * Retrieve data from submitted input / URL
     */
    $output = xarModAPIFunc(__ADDRESSBOOK__,'user','getsubmitvalues', array('output' => $output));

    /**
     * Retrieve any config values needed to configure the page
     */
    $output['zipbeforecity'] = pnModGetVar(__ADDRESSBOOK__,'zipbeforecity');

    // Get detailed values from database
    $details = xarModAPIFunc(__ADDRESSBOOK__,'user','getDetailValues',array('id'=>$output['id']));
    if ($details && is_array($details)) {
    	foreach ($details as $key=>$value) {
        	$output[$key] = $value;
    	}
    } else { // did not get details for some reason
    	return xarModAPIFunc(__ADDRESSBOOK__,'util','handleException',array('output'=>$output));
    }    	

    // Get the labels
    $labels = xarModAPIFunc(__ADDRESSBOOK__,'util','getItems',array('tablename'=>'labels'));

    // General information
    // headline
    $output['info'] = xarVarPrepHTMLDisplay(_AB_CATEGORY.': '._AB_UNFILED);
    if ($output['cat_id'] > 0) {
	    $cats = xarModAPIFunc(__ADDRESSBOOK__,'util','getItems',array('tablename'=>'categories'));
        foreach ($cats as $cat) {
            if ($output['cat_id'] == $cat['id']) {
                $output['info'] = xarVarPrepHTMLDisplay(_AB_CATEGORY.': '.$cat['name']);
            }
        }
    }

    if ($output['last_updt'] > 0) {
        $output['info'] .= ' | '.xarVarPrepHTMLDisplay(_AB_LASTCHANGED)
                               .xarModAPIFunc(__ADDRESSBOOK__,'util','ml_ftime',
                                                            array ('datefmt' =>_DATETIMEBRIEF
                                                                  ,'timestamp'=>$output['last_updt']));
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
        	// to build the contact array because none of the old labels['id'] will
        	// be found in the new label list.
            foreach ($labels as $lab) {
                if ($output[$the_label] == $lab['id']) {
                    $contact['label'] = xarVarPrepHTMLDisplay($lab['name']);
                    if(!xarModAPIFunc(__ADDRESSBOOK__,'util','is_email',array('email'=>$output[$the_contact]))) {
                        if(!xarModAPIFunc(__ADDRESSBOOK__,'util','is_url',array('url'=>$output[$the_contact]))) {
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
    if (xarModAPIFunc(__ADDRESSBOOK__,'util','checkForIE')) {
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