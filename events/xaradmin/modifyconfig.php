<?php

/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function events_admin_modifyconfig()
{
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('events', 'admin', 'menu');

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if(!xarSecurityCheck('AdminEvents')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Here we set the global settings of the events module

    $data['eventsperpagelabel'] = xarVarPrepForDisplay(xarML('Events Per Page:'));
    $data['eventsperpagevalue'] = xarModGetVar('events', 'eventsperpage');
    $data['eventcurrencylabel'] = xarVarPrepForDisplay(xarML('Events Currency:'));
    $data['eventcurrencyoptions'] = array();
    $data['eventcurrencyoptions']['€']   = '€';
    $data['eventcurrencyoptions']['$']   = '$';
    $data['eventcurrencyoptions']['£']   = '£';
    $data['eventcurrencyoptions']['¥']   = '¥';
    $data['eventcurrencyoptions']['KR.'] = 'KR.';
    $data['eventcurrencyoptions']['SEK'] = 'SEK';
    $data['eventcurrencyvalue'] = xarModGetVar('events', 'currency');
    $data['ticketsperuserlabel'] = xarVarPrepForDisplay(xarML('Tickets Per User:'));
    $data['ticketsperuservalue'] = xarModGetVar('events', 'ticketsperuser');
    $data['imageuploadpathlabel'] = xarVarPrepForDisplay(xarML('Image Upload Path:'));
    $data['imageuploadpathvalue'] = xarModGetVar('events', 'imageuploadpath');
    $data['headerimagesizelabel'] = xarVarPrepForDisplay(xarML('Header Image Size:'));
    $data['headerimagesizevalue'] = xarModGetVar('events', 'headerimagesize');
    $data['bodyimagesizelabel'] = xarVarPrepForDisplay(xarML('Body Image Size:'));
    $data['bodyimagesizevalue'] = xarModGetVar('events', 'bodyimagesize');
    $data['headerimagedimensionslabel'] = xarVarPrepForDisplay(xarML('Header Image Dimensions:'));
    $data['headerimagewidthvalue'] = xarModGetVar('events', 'headerimagewidth');
    $data['headerimageheightvalue'] = xarModGetVar('events', 'headerimageheight');
    $data['bodyimagedimensionslabel'] = xarVarPrepForDisplay(xarML('Body Image Dimensions:'));
    $data['bodyimagewidthvalue'] = xarModGetVar('events', 'bodyimagewidth');
    $data['bodyimageheightvalue'] = xarModGetVar('events', 'bodyimageheight');
    $data['notificationemaillabel'] = xarVarPrepForDisplay(xarML('Notification e-mail:'));
    $data['notificationemailvalue'] = xarModGetVar('events', 'notificationemail');
    $data['sendadminemaillabel'] = xarVarPrepForDisplay(xarML('Send Admin e-mail:'));
    $data['sendadminemailchecked'] = xarModGetVar('events','sendadminemail') ? 'checked' : '';
    $data['senduseremaillabel'] = xarVarPrepForDisplay(xarML('Send User e-mail:'));
    $data['senduseremailchecked'] = xarModGetVar('events','senduseremail') ? 'checked' : '';
    $data['shorturlslabel'] = xarML('Enable short URLs:');
    $data['shorturlschecked'] = xarModGetVar('events','SupportShortURLs') ? 'checked' : '';

    // Here we set the required fields of the event

    $data['opeventnamelabel'] = xarVarPrepForDisplay(xarML('Event Name:'));
    $data['opeventnamechecked'] = xarModGetVar('events','opeventname') ? 'checked' : '';
    $data['opcompanynamelabel'] = xarVarPrepForDisplay(xarML('Company Name:'));
    $data['opcompanynamechecked'] = xarModGetVar('events','opcompanyname') ? 'checked' : '';
    $data['opspeakernamelabel'] = xarVarPrepForDisplay(xarML('Speaker Name:'));
    $data['opspeakernamechecked'] = xarModGetVar('events','opspeakername') ? 'checked' : '';
    $data['opeventstartdatelabel'] = xarVarPrepForDisplay(xarML('Event Start Date:'));
    $data['opeventstartdatechecked'] = xarModGetVar('events','opeventstartdate') ? 'checked' : '';
    $data['opeventenddatelabel'] = xarVarPrepForDisplay(xarML('Event End Date:'));
    $data['opeventenddatechecked'] = xarModGetVar('events','opeventenddate') ? 'checked' : '';
    $data['opeventstarttimelabel'] = xarVarPrepForDisplay(xarML('Event Start Time:'));
    $data['opeventstarttimechecked'] = xarModGetVar('events','opeventstarttime') ? 'checked' : '';
    $data['opeventendtimelabel'] = xarVarPrepForDisplay(xarML('Event End Time:'));
    $data['opeventendtimechecked'] = xarModGetVar('events','opeventendtime') ? 'checked' : '';
    $data['opeventregistrationtimelabel'] = xarVarPrepForDisplay(xarML('Event Registration Time:'));
    $data['opeventregistrationtimechecked'] = xarModGetVar('events','opeventregistrationtime') ? 'checked' : '';
    $data['opeventaddresslabel'] = xarVarPrepForDisplay(xarML('Event Address:'));
    $data['opeventaddresschecked'] = xarModGetVar('events','opeventaddress') ? 'checked' : '';
    $data['opeventsummarylabel'] = xarVarPrepForDisplay(xarML('Event Summary:'));
    $data['opeventsummarychecked'] = xarModGetVar('events','opeventsummary') ? 'checked' : '';
    $data['opeventheadertextlabel'] = xarVarPrepForDisplay(xarML('Event Header Text:'));
    $data['opeventheadertextchecked'] = xarModGetVar('events','opeventheadertext') ? 'checked' : '';
    $data['opeventbodytextlabel'] = xarVarPrepForDisplay(xarML('Event Body Text:'));
    $data['opeventbodytextchecked'] = xarModGetVar('events','opeventbodytext') ? 'checked' : '';
    $data['opeventprintticketslabel'] = xarVarPrepForDisplay(xarML('Event Print Tickets:'));
    $data['opeventprintticketschecked'] = xarModGetVar('events','opeventprinttickets') ? 'checked' : '';
    $data['opeventticketsavailablelabel'] = xarVarPrepForDisplay(xarML('Event Tickets Available:'));
    $data['opeventticketsavailablechecked'] = xarModGetVar('events','opeventticketsavailable') ? 'checked' : '';
    $data['opeventcostlabel'] = xarVarPrepForDisplay(xarML('Event Cost:'));
    $data['opeventcostchecked'] = xarModGetVar('events','opeventcost') ? 'checked' : '';
    $data['opeventtelephonelabel'] = xarVarPrepForDisplay(xarML('Event Telephone:'));
    $data['opeventtelephonechecked'] = xarModGetVar('events','opeventtelephone') ? 'checked' : '';
    $data['opeventheaderimagelabel'] = xarVarPrepForDisplay(xarML('Event Header Image:'));
    $data['opeventheaderimagechecked'] = xarModGetVar('events','opeventheaderimage') ? 'checked' : '';
    $data['opeventbodyimagelabel'] = xarVarPrepForDisplay(xarML('Event Body Image:'));
    $data['opeventbodyimagechecked'] = xarModGetVar('events','opeventbodyimage') ? 'checked' : '';

    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));


    $hooks = xarModCallHooks('module', 'modifyconfig', 'events',
                            array('module' => 'events'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    // Return the template variables defined in this function
    return $data;
}

?>