<?php
/**
 * AddressBook user viewDetail
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
function addressbook_user_viewdetail()
{

    $output = array();
    $output['abModInfo'] = xarModGetInfo(xarModGetIDFromName('addressbook'));

    /**
     * Retrieve data from submitted input / URL
     */
    $output = xarModAPIFunc('addressbook','user','getsubmitvalues', array('output' => $output));

    /**
     * Retrieve any config values needed to configure the page
     */
    $output['zipbeforecity'] = xarModGetVar('addressbook','zipbeforecity');

    // Get detailed values from database
    $details = xarModAPIFunc('addressbook','user','getdetailvalues',array('id'=>$output['id']));
    if ($details && is_array($details)) {
        foreach ($details as $key=>$value) {
            $output[$key] = $value;
        }
    } else { // did not get details for some reason
        return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));
    }

    // Get the labels
    $labels = xarModAPIFunc('addressbook','util','getitems',array('tablename'=>'labels'));

    // General information
    // headline
    $output['info'] = xarML('Category') . ': ' . xarML('Unfiled');
    if ($output['cat_id'] > 0) {
        $cats = xarModAPIFunc('addressbook','util','getitems',array('tablename'=>'categories'));
        foreach ($cats as $cat) {
            if ($output['cat_id'] == $cat['id']) {
                $output['info'] = xarML('Category') . ': ' . xarVarPrepHTMLDisplay($cat['name']);
            }
        }
    }

    if ($output['last_updt'] > 0) {
        $output['info'] .= ' | '.xarML('Last changed ').": "
                               .xarLocaleGetFormattedDate ('long',$output['last_updt']);
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
                    if(xarModAPIFunc('addressbook','util','is_email',array('email'=>$output[$the_contact]))) {
                        $contact['contact'] = '<a href="mailto:'.xarVarPrepHTMLDisplay($output[$the_contact]).'">'.xarVarPrepHTMLDisplay($output[$the_contact]).'</a>';
                    } elseif (xarModAPIFunc('addressbook','util','is_url',array('url'=>$output[$the_contact]))) {
                        $contact['contact'] = '<a href="'.xarVarPrepHTMLDisplay($output[$the_contact]).'" target="_blank">'.xarVarPrepHTMLDisplay($output[$the_contact]).'</a>';
                    }
                    else {
                        $contact['contact'] = xarVarPrepHTMLDisplay($output[$the_contact]);
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
    $custom_tab = xarModGetVar('addressbook','custom_tab');
    if ((!empty($custom_tab)) || ($custom_tab != '')) {

        $output['custom_tab'] = $custom_tab;
        $custUserData = xarModAPIFunc('addressbook','user','getcustfieldinfo',
                                        array('id'=>$output['id']
                                             ,'flag'=>_AB_CUST_ALLINFO));

    } // END if

    /**
     * Notes
     */
    if (!empty($output['note'])) {

        // headline
        $output['noteHeading'] = xarML('Note');

        $output['note'] = xarVarPrepHTMLDisplay(nl2br($output['note']));
    }

    /**
     * Navigation buttons
     */
    // Copy to clipboard if IE
    if (xarModAPIFunc('addressbook','util','checkforie')) {
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
        $output['copy2clipboard'] = xarML('Copy to clipboard');
    }

    $output['goBack'] = xarML('Back to list');

    return xarModAPIFunc('addressbook','util','handleexception',array('output'=>$output));

} // END viewdetail

?>
