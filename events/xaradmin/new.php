<?php

/**
 * add new item
 * This is a standard function that is called whenever an administrator
 * wishes to create a new module item
 */
function events_admin_new($args)
{
    // Get parameters from whatever input we need.
    //
    // We are recieving the form values as the poor man's form validation.
    // If the values that are required in the update function are not there,
    // then we are redirecting back to the main form again for processing.
    list($name,
         $number) = xarVarCleanFromInput('name',
                                         'number');

    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('events', 'admin', 'menu');

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if(!xarSecurityCheck('AddEvents')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Specify some labels for display
    $data['eventnamelabel'] = xarVarPrepForDisplay(xarML('Event Name:'));
    $data['companynamelabel'] = xarVarPrepForDisplay(xarML('Company Name:'));
    $data['speakernamelabel'] = xarVarPrepForDisplay(xarML('Speaker Name:'));
    $data['eventstartdatelabel'] = xarVarPrepForDisplay(xarML('Event Start Date:'));
    $data['eventstartdatedays'] = array();
    $data['eventstartdatedays'][''] = xarML('Choose Day');

    for($i=1; $i < 32; $i++){
    if($i < 10){
    $i = "0$i";
    }
    $data['eventstartdatedays'][$i] = xarML($i);
    }
    $data['eventstartdatemonths'] = array();
    $data['eventstartdatemonths']['']     = xarML('Choose Month');
    $data['eventstartdatemonths']['01']   = xarML('January');
    $data['eventstartdatemonths']['02']   = xarML('February');
    $data['eventstartdatemonths']['03']   = xarML('March');
    $data['eventstartdatemonths']['04']   = xarML('April');
    $data['eventstartdatemonths']['05']   = xarML('May');
    $data['eventstartdatemonths']['06']   = xarML('June');
    $data['eventstartdatemonths']['07']   = xarML('July');
    $data['eventstartdatemonths']['08']   = xarML('August');
    $data['eventstartdatemonths']['09']   = xarML('September');
    $data['eventstartdatemonths']['10']   = xarML('October');
    $data['eventstartdatemonths']['11']   = xarML('November');
    $data['eventstartdatemonths']['12']   = xarML('December');


    $data['eventenddatelabel'] = xarVarPrepForDisplay(xarML('Event End Date:'));
    $data['eventenddatedays'] = array();
    $data['eventenddatedays'][''] = xarML('Choose Day');

    for($i=1; $i < 32; $i++){
    if($i < 10){
    $i = "0$i";
    }
    $data['eventenddatedays'][$i] = xarML($i);
    }
    $data['eventenddatemonths'] = array();
    $data['eventenddatemonths']['']     = xarML('Choose Month');
    $data['eventenddatemonths']['01']   = xarML('January');
    $data['eventenddatemonths']['02']   = xarML('February');
    $data['eventenddatemonths']['03']   = xarML('March');
    $data['eventenddatemonths']['04']   = xarML('April');
    $data['eventenddatemonths']['05']   = xarML('May');
    $data['eventenddatemonths']['06']   = xarML('June');
    $data['eventenddatemonths']['07']   = xarML('July');
    $data['eventenddatemonths']['08']   = xarML('August');
    $data['eventenddatemonths']['09']   = xarML('September');
    $data['eventenddatemonths']['10']   = xarML('October');
    $data['eventenddatemonths']['11']   = xarML('November');
    $data['eventenddatemonths']['12']   = xarML('December');


    $data['eventstarttimelabel'] = xarVarPrepForDisplay(xarML('Event Start Time:'));
    $data['eventstarttimehour'] = array();
    $data['eventstarttimehour'][''] = xarML('Hour');

    for($i=0; $i < 24; $i++){
    if($i < 10){
    $i = "0$i";
    }
    $data['eventstarttimehour'][$i] = xarML($i);
    }

    $data['eventstarttimeminute'] = array();
    $data['eventstarttimeminute'][''] = xarML('Minute');

    for($i=0; $i < 24; $i++){
    if($i < 10){
    $i = "0$i";
    }
    $data['eventstarttimeminute'][$i] = xarML($i);
    }

    $data['eventendtimelabel'] = xarVarPrepForDisplay(xarML('Event End Time:'));
    $data['eventendtimehour'] = array();
    $data['eventendtimehour'][''] = xarML('Hour');

    for($i=0; $i < 24; $i++){
    if($i < 10){
    $i = "0$i";
    }
    $data['eventendtimehour'][$i] = xarML($i);
    }

    $data['eventendtimeminute'] = array();
    $data['eventendtimeminute'][''] = xarML('Minute');

    for($i=0; $i < 24; $i++){
    if($i < 10){
    $i = "0$i";
    }
    $data['eventendtimeminute'][$i] = xarML($i);
    }

    $data['eventregistrationtimelabel'] = xarVarPrepForDisplay(xarML('Event Registration Time:'));
    $data['eventregistrationtimehour'] = array();
    $data['eventregistrationtimehour'][''] = xarML('Hour');

    for($i=0; $i < 24; $i++){
    if($i < 10){
    $i = "0$i";
    }
    $data['eventregistrationtimehour'][$i] = xarML($i);
    }

    $data['eventregistrationtimeminute'] = array();
    $data['eventregistrationtimeminute'][''] = xarML('Minute');

    for($i=0; $i < 24; $i++){
    if($i < 10){
    $i = "0$i";
    }
    $data['eventregistrationtimeminute'][$i] = xarML($i);
    }
    $data['eventaddresslabel'] = xarVarPrepForDisplay(xarML('Event Address:'));
    $data['eventsummarylabel'] = xarVarPrepForDisplay(xarML('Event Summary:'));
    $data['eventheadertextlabel'] = xarVarPrepForDisplay(xarML('Event Header Text:'));
    $data['eventbodytextlabel'] = xarVarPrepForDisplay(xarML('Event Body Text:'));
    $data['eventprintticketslabel'] = xarVarPrepForDisplay(xarML('Event Print Tickets:'));
    $data['eventticketsavailablelabel'] = xarVarPrepForDisplay(xarML('# Of Tickets Available:'));
    $data['eventcostlabel'] = xarVarPrepForDisplay(xarML('Event Cost:'));
    $data['eventcostvalue'] = xarModGetVar('events', 'currency');
    $data['eventtelephonelabel'] = xarVarPrepForDisplay(xarML('Event Telephone:'));
    $data['eventheaderimagelabel'] = xarVarPrepForDisplay(xarML('Event Header Image:'));
    $data['eventbodyimagelabel'] = xarVarPrepForDisplay(xarML('Event Body Image:'));

    $data['addbutton'] = xarVarPrepForDisplay(xarML('Add Event'));

    $item = array();
    $item['module'] = 'events';
    $hooks = xarModCallHooks('item','new','',$item);

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    // For E_ALL purposes, we need to check to make sure the vars are set.
    // If they are not set, then we need to set them empty to surpress errors

    if (empty($eventname)){
        $data['eventname'] = '';
    } else {
        $data['eventname'] = $eventname;
    }

    if (empty($companyname)){
        $data['companyname'] = '';
    } else {
        $data['companyname'] = $companyname;
    }

    if (empty($speakername)){
        $data['speakername'] = '';
    } else {
        $data['speakername'] = $speakername;
    }


    if (empty($eventstartdateyear)){
        $data['eventstartdateyear'] = '';
    } else {
        $data['eventstartdateyear'] = $eventstartdateyear;
    }

    if (empty($eventenddateyear)){
        $data['eventenddateyear'] = '';
    } else {
        $data['eventenddateyear'] = $eventenddateyear;
    }

    if (empty($eventaddress)){
        $data['eventaddress'] = '';
    } else {
        $data['eventaddress'] = $eventaddress;
    }

    if (empty($eventsummary)){
        $data['eventsummary'] = '';
    } else {
        $data['eventsummary'] = $eventsummary;
    }

    if (empty($eventheadertext)){
        $data['eventheadertext'] = '';
    } else {
        $data['eventheadertext'] = $eventheadertext;
    }

    if (empty($eventbodytext)){
        $data['eventbodytext'] = '';
    } else {
        $data['eventbodytext'] = $eventbodytext;
    }

    if (empty($eventticketsavailable)){
        $data['eventticketsavailable'] = '';
    } else {
        $data['eventticketsavailable'] = $eventticketsavailable;
    }

    if (empty($eventcost)){
        $data['eventcost'] = '';
    } else {
        $data['eventcost'] = $eventcost;
    }

    if (empty($eventtelephone)){
        $data['eventtelephone'] = '';
    } else {
        $data['eventtelephone'] = $eventtelephone;
    }

    // If we like, we can also display the status message if one is set.
    $data['statusmsg'] = xarSessionGetVar('statusmsg');

    // Return the template variables defined in this function
    return $data;
}

?>