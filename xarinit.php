<?php
/**
   Mailbag for Xaraya
  
   @package Mailbag
   @copyright (C) 2004 Brian McGilligan.
   @license GPL <http://www.gnu.org/licenses/gpl.html>
   @link http://www.abrasiontechnology.com
   @author Brian McGilligan
*/

/**
* initialise the helpdesk module
*/
function mailbag_init()
{   
    // Get database information
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    
    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Create tables
    $mailbag  = $xartable['mailbag_errors'];

    $fields = array(
        'xar_msgid'      => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_subject'    => array('type'=>'varchar', 'size'=>100,'null'=>FALSE, 'default'=>''),
        'xar_from'       => array('type'=>'varchar', 'size'=>100,'null'=>FALSE, 'default'=>''),
        'xar_from_uid'   => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'default'=>'0'),
        'xar_to'         => array('type'=>'varchar', 'size'=>100,'null'=>FALSE, 'default'=>'0'),
        'xar_to_uid'     => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'default'=>'0'),
        'xar_msg_time'   => array('type'=>'varchar', 'size'=>20, 'null'=>FALSE, 'default'=>''),
        'xar_msg_text'   => array('type'=>'text',                'null'=>FALSE, 'default'=>''),
        'xar_header'     => array('type'=>'text',                'null'=>FALSE, 'default'=>''),
        'xar_errorcode'  => array('type'=>'integer', 'size'=>2,  'null'=>FALSE, 'default'=>'0'),
        'xar_error'      => array('type'=>'varchar', 'size'=>100,'null'=>FALSE, 'default'=>'')
    );
    
    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($mailbag,$fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!isset($result)) return;
    
    // Create tables
    $mailbag  = $xartable['mailbag_maillists'];

    $fields = array(
        'xar_lid'        => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_from_email' => array('type'=>'varchar', 'size'=>100,'null'=>FALSE, 'default'=>''),
        'xar_to_email'   => array('type'=>'varchar', 'size'=>100,'null'=>FALSE, 'default'=>''),
        'xar_in_subject' => array('type'=>'varchar', 'size'=>100,'null'=>FALSE, 'default'=>''),
        'xar_desc'       => array('type'=>'varchar', 'size'=>100,'null'=>FALSE, 'default'=>''),
        'xar_to_topic'   => array('type'=>'integer', 'size'=>3,  'null'=>FALSE, 'default'=>'0'),
        'xar_cid'        => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'default'=>'0'),
        'xar_admin_uid'  => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'default'=>'0'),
        'xar_no_comment' => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'default'=>'0'),
        'xar_no_homepage'=> array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'default'=>'0')
    );
    
    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($mailbag,$fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!isset($result)) return;
    
    // Create tables
    $mailbag  = $xartable['mailbag_sblacklist'];

    $fields = array(
        'xar_sbid'   => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_from'   => array('type'=>'varchar', 'size'=>100,'null'=>FALSE, 'default'=>'')
    );
    
    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($mailbag,$fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!isset($result)) return;
    
    // Create tables
    $mailbag  = $xartable['mailbag_rblacklist'];

    $fields = array(
        'xar_rbid'   => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_to'     => array('type'=>'varchar', 'size'=>100,'null'=>FALSE, 'default'=>'')
    );
    
    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($mailbag,$fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!isset($result)) return;

    // Create tables
    $mailbag  = $xartable['mailbag_ublacklist'];

    $fields = array(
        'xar_ubid'   => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'increment'=>TRUE,'primary_key'=>TRUE),
        'xar_uid'    => array('type'=>'integer', 'size'=>11, 'null'=>FALSE, 'default'=>'0')
    );
    
    // Create the Table - the function will return the SQL is successful or
    // raise an exception if it fails, in this case $query is empty
    $query = xarDBCreateTable($mailbag,$fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!isset($result)) return;
    
    
    // Set up module variables
    // Set up the initial values for a MailBag config variables.
    xarModSetVar('mailbag', 'bgswitch', 0);
    xarModSetVar('mailbag', 'exepassword', 'mailbag');
    xarModSetVar('mailbag', 'popserver', 'pop3.server.com');
    xarModSetVar('mailbag', 'popuser', 'account');
    xarModSetVar('mailbag', 'poppass', 'password');
    xarModSetVar('mailbag', 'emaildomain', 'yourdomain.com');
    xarModSetVar('mailbag', 'postmaster', 'postmaster@yourdomain.com');
    xarModSetVar('mailbag', 'allowhtml', 1);
    xarModSetVar('mailbag', 'maxrecip', '3');
    xarModSetVar('mailbag', 'maxsize', '3000');
    xarModSetVar('mailbag', 'faqaddr', 'faq');
    xarModSetVar('mailbag', 'unknowntolog', 0);
    xarModSetVar('mailbag', 'lastrunlog', "No run yet");
    xarModSetVar('mailbag', 'notifyuser', 0);
    xarModSetVar('mailbag', 'senderemail', 1);
    
    xarRegisterMask('viewmailbag',   'All','mailbag','mailbag','All', 'ACCESS_OVERVIEW');
    xarRegisterMask('readmailbag',   'All','mailbag','mailbag','All', 'ACCESS_READ');
    xarRegisterMask('submitmailbag', 'All','mailbag','mailbag','All', 'ACCESS_COMMENT');    
    xarRegisterMask('editmailbag',   'All','mailbag','mailbag','All', 'ACCESS_EDIT');
    xarRegisterMask('addmailbag',    'All','mailbag','mailbag','All', 'ACCESS_ADD');
    xarRegisterMask('deletemailbag', 'All','mailbag','mailbag','All', 'ACCESS_DELETE');
    xarRegisterMask('adminmailbag',  'All','mailbag','mailbag','All', 'ACCESS_ADMIN');

    
    /**
    * Ok, Now lets create all of our dd objects
    */
    $path = "modules/mailbag/xardata/";
    
    /*
    * The Errors Object
    */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'mailbag_errors.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('mailbag','errorsobjectid',$objectid);
    
    /*
    * The Senders Blacklist Object
    */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'sblacklist.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('mailbag','sblacklistobjectid',$objectid);
    
    /*
    * The Recipient Blacklist Object
    */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'rblacklist.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('mailbag','rblacklistobjectid',$objectid);
    
    /*
    * The Users Blacklist Object
    */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'ublacklist.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('mailbag','ublacklistobjectid',$objectid);
    
    /*
    * The Maillist Object
    */
    $objectid = xarModAPIFunc('dynamicdata','util','import',
                              array('file' => $path . 'maillist.xml'));
    if (empty($objectid)) return;
    // save the object id for later
    xarModSetVar('mailbag','maillistobjectid',$objectid);
    
    
    // Initialisation successful
    return true;
}

/**
* upgrade the mailbag module from an old version
*/
function mailbag_upgrade($oldversion)
{
    // Get database information
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    
    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Create tables
    $mailbagtable = $xartable['mailbag_errors'];
    
    switch($oldversion) {
        case '.3':
        case '.3.0':
        case '0.4.0':                      
    }
    // If all else fails, return true so the module no longer shows "Upgrade" in module administration
    return true;
} 

/**
* delete the mailbag module
*/
function mailbag_delete()
{
    //Load Table Maintenance API
    xarDBLoadTableMaintenanceAPI();

    // Get database information
    $dbconn   =& xarDBGetConn();
    $xartable =  xarDBGetTables();

    // Delete tables
    $query = xarDBDropTable($xartable['mailbag_errors']);
    $result =& $dbconn->Execute($query);
    $query = xarDBDropTable($xartable['mailbag_maillists']);
    $result =& $dbconn->Execute($query);
    $query = xarDBDropTable($xartable['mailbag_sblacklist']);
    $result =& $dbconn->Execute($query);
    $query = xarDBDropTable($xartable['mailbag_rblacklist']);
    $result =& $dbconn->Execute($query);
    $query = xarDBDropTable($xartable['mailbag_ublacklist']);
    $result =& $dbconn->Execute($query);

    $objectid = xarModGetVar('mailbag','errorsobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

    $objectid = xarModGetVar('mailbag','sblacklistobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

    $objectid = xarModGetVar('mailbag','rblacklistobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

    $objectid = xarModGetVar('mailbag','ublacklistobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

    $objectid = xarModGetVar('mailbag','maillistobjectid');
    if (!empty($objectid)) {
        xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

    xarModDelAllVars('mailbag');
    
    xarRemoveMasks('mailbag');
    xarRemoveInstances('mailbag');    
            
    return true;
} 
?>