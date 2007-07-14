<?php
/**
 * Create a response record
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Create a new response record
 * Usage : $scrid = xarModAPIFunc('sitecontact', 'admin', 'create', $response);
 *
 * Create a new response record for a user and specific form - only the scid is required
 * as we must have a way to allow for users that disagree to saving data in the db but we still allow to fill in
 * these are treated as 'missing values' rather than no response at all, for statistical reasons if needed
 *
 * @param $args['username'] name of the respondent
 * @param $args['useremail'] email address of the respondent
 * @param $args['requesttext'] email subject
 * @param $args['company'] company name
 * @param $args['message'] message text
 * @param $args['scid'] the specific site contact form id (required)
 * @param $args['useripaddress'] ip address of respondent
 * @param $args['userreferer'] refererurl of respondent
 * @param $args['sendcopy'] requested a copy
 * @param $args['permission'] agree to saving
 * @returns $args['bccrecipients'] bccrecipient list
 * @returns $args['ccrecipients'] ccrecipient list
 * @return articles item ID on success, false on failure
 */
function sitecontact_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check (all the rest is optional, and set to defaults below)
    if (empty($scid) || !is_int($scid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'title', 'admin', 'create', 'Sitecontact');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // There was a point when we could have scid as optional and default to default form it
    // however better to pass it in
    if (empty($scid) || !is_numeric($scid)) {
        //we won't use this atm
    }

     // Security check

     if(!xarSecurityCheck('SubmitSiteContact', 0, 'ContactForm', "$scid:All:All")) return; // we don't want to error display here and distrupt

    // Default publication date is now
    if (!isset($responsetime) || empty($responsetime)) {
        $responsetime = time();
    }
    
    /* get the Dynamic Object defined for this module (and itemtype, if relevant) */
    $object = xarModAPIFunc('dynamicdata','user','getobject',
                             array('module' => 'sitecontact',
                                   'itemtype' => $scid));
    if (!isset($object)) return;  /* throw back */
    $objectid=$object->objectid;      

    /*we just want a copy of data - don't need to save it in a table yet */
    if (isset($object) && !empty($object->objectid)) {
        /* check the input values for this object and do ....what here? */
        $isvalid = $object->checkInput();         
        // $dditems =& $object->getProperties();
        $dditems = $object->properties;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitecontactResponseTable = $xartable['sitecontact_response'];

    // Get next ID in table
    if (empty($scrid) || !is_numeric($scrid) || $scrid == 0) {
        $nextId = $dbconn->GenId($sitecontactResponseTable);
    } else {
        $nextId = $scrid;
    }

    // Add item
    $query = "INSERT INTO $sitecontactResponseTable (
              xar_scrid,
              xar_scid,
              xar_username,
              xar_useremail,
              xar_requesttext,
              xar_company,
              xar_usermessage,
              xar_useripaddress,
              xar_userreferer,
              xar_sendcopy,
              xar_permission,
              xar_bccrecipients,
              xar_ccrecipients,
              xar_responsetime)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $bindvars = array($nextId,
                      (int)     $scid,
                      (string)  $username,
                      (string)  $useremail,
                      (string)  $requesttext,
                      (string)  $company,
                                $usermessage,
                      (string)  $useripaddress,
                      (string)  $userreferer,
                      (int)     $sendcopy,
                      (int)     $permission,
                      (string)  $bccrecipients,
                      (string)  $ccrecipients,
                      (int)  $responsetime);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Get scrid to return
    if (empty($scrid) || !is_numeric($scrid) || $scrid == 0) {
        $scrid = $dbconn->PO_Insert_ID($sitecontactResponseTable, 'xar_scrid');
    }

    // Call create hooks for dynamic data but only if permission given
    if (($permissioncheck && $permission) || (!$permissioncheck)) {
       $args['scrid'] = $scrid;

       $args['module'] = 'sitecontact';
       $args['itemtype'] = $scid;
       $args['itemid'] = $scrid;
       xarModCallHooks('item', 'create', $scrid, $args);
    }
    return $scrid;
}

?>