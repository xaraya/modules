<?php

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function events_admin_updateconfig()
{
    // Get parameters from whatever input we need.  All arguments to this
    // function should be obtained from xarVarCleanFromInput(), getting them
    // from other places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    list($eventsperpage,
         $eventcurrency,
         $ticketsperuser,
         $imageuploadpath,
         $headerimagesize,
         $bodyimagesize,
         $headerimagewidth,
         $headerimageheight,
         $bodyimagewidth,
         $bodyimageheight,
         $notificationemail,
         $sendadminemail,
         $senduseremail,
         $shorturls,
         $opeventname,
         $opcompanyname,
         $opspeakername,
         $opeventstartdate,
         $opeventenddate,
         $opeventstarttime,
         $opeventendtime,
         $opeventregistrationtime,
         $opeventaddress,
         $opeventsummary,
         $opeventheadertext,
         $opeventbodytext,
         $opeventprinttickets,
         $opeventticketsavailable,
         $opeventcost,
         $opeventtelephone,
         $opeventheaderimage,
         $opeventbodyimage) = xarVarCleanFromInput('eventsperpage',
                                                   'eventcurrency',
                                                   'ticketsperuser',
                                                   'imageuploadpath',
                                                   'headerimagesize',
                                                   'bodyimagesize',
                                                   'headerimagewidth',
                                                   'headerimageheight',
                                                   'bodyimagewidth',
                                                   'bodyimageheight',
                                                   'notificationemail',
                                                   'sendadminemail',
                                                   'senduseremail',
                                                   'shorturls',
                                                   'opeventname',
                                                   'opcompanyname',
                                                   'opspeakername',
                                                   'opeventstartdate',
                                                   'opeventenddate',
                                                   'opeventstarttime',
                                                   'opeventendtime',
                                                   'opeventregistrationtime',
                                                   'opeventaddress',
                                                   'opeventsummary',
                                                   'opeventheadertext',
                                                   'opeventbodytext',
                                                   'opeventprinttickets',
                                                   'opeventticketsavailable',
                                                   'opeventcost',
                                                   'opeventtelephone',
                                                   'opeventheaderimage',
                                                   'opeventbodyimage');

    // Confirm authorisation code.  This checks that the form had a valid
    // authorisation code attached to it.  If it did not then the function will
    // proceed no further as it is possible that this is an attempt at sending
    // in false data to the system
    if (!xarSecConfirmAuthKey()) return;

    // Update module variables.  Note that depending on the HTML structure used
    // to obtain the information from the user it is possible that the values
    // might be unset, so it is important to check them all and assign them
    // default values if required
    if (!isset($eventsperpage) || !is_numeric($eventsperpage)) {
        $eventsperpage = 10;
    }
    xarModSetVar('events', 'eventsperpage', $eventsperpage);

    if ($eventcurrency == '') {
        $eventcurrency = '€';
    }
    xarModSetVar('events', 'currency', $eventcurrency);

    if (!isset($ticketsperuser) || !is_numeric($ticketsperuser)) {
        $ticketsperuser = 5;
    }
    xarModSetVar('events', 'ticketsperuser', $ticketsperuser);

    if (!isset($imageuploadpath)) {
        $imageuploadpath = 'modules/events/eventimages';
    }
    xarModSetVar('events', 'imageuploadpath', $imageuploadpath);

    if (!isset($headerimagesize) || !is_numeric($headerimagesize)) {
        $headerimagesize = 512000;
    }
    xarModSetVar('events', 'headerimagesize', $headerimagesize);

    if (!isset($bodyimagesize) || !is_numeric($bodyimagesize)) {
        $bodyimagesize = 512000;
    }
    xarModSetVar('events', 'bodyimagesize', $bodyimagesize);

    if (!isset($headerimagewidth) || !is_numeric($headerimagewidth)) {
        $headerimagewidth = 640;
    }
    xarModSetVar('events', 'headerimagewidth', $headerimagewidth);

    if (!isset($headerimageheight) || !is_numeric($headerimageheight)) {
        $headerimageheight = 200;
    }
    xarModSetVar('events', 'headerimageheight', $headerimageheight);

    if (!isset($bodyimagewidth) || !is_numeric($bodyimagewidth)) {
        $bodyimagewidth = 640;
    }
    xarModSetVar('events', 'bodyimagewidth', $bodyimagewidth);

    if (!isset($bodyimageheight) || !is_numeric($bodyimageheight)) {
        $bodyimageheight = 200;
    }
    xarModSetVar('events', 'bodyimageheight', $bodyimageheight);

    if ($notificationemail == '') {
        $notificationemail = xarModGetVar('mail', 'adminmail');
    }
    xarModSetVar('events', 'notificationemail', $notificationemail);

    if (!isset($sendadminemail)) {
        $sendadminemail = 0;
    }
    xarModSetVar('events', 'sendadminemail', $sendadminemail);

    if (!isset($senduseremail)) {
        $senduseremail = 0;
    }
    xarModSetVar('events', 'senduseremail', $senduseremail);

    if (!isset($shorturls)) {
        $shorturls = 0;
    }
    xarModSetVar('events', 'SupportShortURLs', $shorturls);

    // Here we define the required fields
    
    if (!isset($opeventname)) {
        $opeventname = 0;
    }
    xarModSetVar('events', 'opeventname', $opeventname);

    if (!isset($opcompanyname)) {
        $opcompanyname = 0;
    }
    xarModSetVar('events', 'opcompanyname', $opcompanyname);

    if (!isset($opspeakername)) {
        $opspeakername = 0;
    }
    xarModSetVar('events', 'opspeakername', $opspeakername);

    if (!isset($opeventstartdate)) {
        $opeventstartdate = 0;
    }
    xarModSetVar('events', 'opeventstartdate', $opeventstartdate);

    if (!isset($opeventenddate)) {
        $opeventenddate = 0;
    }
    xarModSetVar('events', 'opeventenddate', $opeventenddate);

    if (!isset($opeventstarttime)) {
        $opeventstarttime = 0;
    }
    xarModSetVar('events', 'opeventstarttime', $opeventstarttime);

    if (!isset($opeventendtime)) {
        $opeventendtime = 0;
    }
    xarModSetVar('events', 'opeventendtime', $opeventendtime);

    if (!isset($opeventregistrationtime)) {
        $opeventregistrationtime = 0;
    }
    xarModSetVar('events', 'opeventregistrationtime', $opeventregistrationtime);

    if (!isset($opeventsummary)) {
        $opeventsummary = 0;
    }
    xarModSetVar('events', 'opeventsummary', $opeventsummary);

    if (!isset($opeventheadertext)) {
        $opeventheadertext = 0;
    }
    xarModSetVar('events', 'opeventheadertext', $opeventheadertext);

    if (!isset($opeventbodytext)) {
        $opeventbodytext = 0;
    }
    xarModSetVar('events', 'opeventbodytext', $opeventbodytext);

    if (!isset($opeventprinttickets)) {
        $opeventprinttickets = 0;
    }
    xarModSetVar('events', 'opeventprinttickets', $opeventprinttickets);

    if (!isset($opeventticketsavailable)) {
        $opeventticketsavailable = 0;
    }
    xarModSetVar('events', 'opeventticketsavailable', $opeventticketsavailable);

    if (!isset($opeventcost)) {
        $opeventcost = 0;
    }
    xarModSetVar('events', 'opeventcost', $opeventcost);

    if (!isset($opeventtelephone)) {
        $opeventtelephone = 0;
    }
    xarModSetVar('events', 'opeventtelephone', $opeventtelephone);

    if (!isset($opeventheaderimage)) {
        $opeventheaderimage = 0;
    }
    xarModSetVar('events', 'opeventheaderimage', $opeventheaderimage);

    if (!isset($opeventbodyimage)) {
        $opeventbodyimage = 0;
    }
    xarModSetVar('events', 'opeventbodyimage', $opeventbodyimage);


    xarModCallHooks('module','updateconfig','events',
                   array('module' => 'events'));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('events', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>