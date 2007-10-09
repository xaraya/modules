<?php
/**
 * Sitecontact form create
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

function sitecontact_adminapi_createsctype($args)
{
    // Get arguments from argument array
    extract($args);

    $invalid = array();
    if (!isset($sctypename) || !is_string($sctypename) || empty($sctypename)) {
        $invalid[] = 'sctypename';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'createitype','Integrator');
         xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
      $resultarray = array('sctypeid' => null,
                   'created' => 0);
    }

    // Form names names *must* be lower-case for now and one word
    $sctypename = strtolower($sctypename);
    $sctypename= str_replace(' ','_',$sctypename);
    //Does this exist already??
    $itemtest = xarModAPIFunc('sitecontact','user','getcontacttypes',array('sctypename'=>$sctypename));

    if (is_array($itemtest) && !empty($itemtest)) {
        //This already exists - return - fix the status message
        return false;
    }

    if (!isset ($customtext)) {
        $customtext= xarModGetVar('sitecontact', 'customtext');
    }
    if (!isset ($customtitle)) {
        $customtitle=xarModGetVar('sitecontact', 'customtitle');
    }
    if (!isset($optiontext)) {
        $optiontext= xarModGetVar('sitecontact', 'optiontext');
    }
    if (!isset($webconfirmtext)) {
        $webconfirmtext= xarModGetVar('sitecontact', 'webconfirmtext');
    }
    if (!isset($notetouser) || trim($notetouser)=='') {
        $notetouser = xarModGetVar('sitecontact', 'notetouser');
    }
    if (!isset($usehtmlemail)) {
        $usehtmlemail = xarModGetVar('sitecontact', 'usehtmlemail');
    }
    if (!isset($allowcopy)) {
        $allowcopy = xarModGetVar('sitecontact', 'allowcopy');
    }

    if (!isset($scdefaultemail)) {
       $scdefaultemail = xarModGetVar('sitecontact', 'scdefaultemail');
       if (!isset($scdefaultemail) || (trim($scdefaultemail)=='')) {
            $scdefaultemail=xarModGetVar('mail','adminmail');
       }
    }

    if (!isset($scdefaultname) || ($scdefaultname)=='') {
        $scdefaultname = xarModGetVar('sitecontact', 'scdefaultname');
        if (!isset($scdefaultname)) {
            $scdefaultname=xarModGetVar('mail','adminname');
        }
    }
    if (!isset($scactive)) {
        $scactive = xarModGetVar('sitecontact', 'scactive');
    }
    if (!isset($savedata)) {
        $savedata = xarModGetVar('sitecontact', 'savedata');
    }
     if (!isset($permissioncheck)) {
        $permissioncheck = xarModGetVar('sitecontact', 'permissioncheck');
    }
    if (!isset($termslink)) {
        $termslink = xarModGetVar('sitecontact', 'termslink');
    }
    if (!isset($soptions)) {
        $soptions = xarModGetVar('sitecontact', 'soptions');
    }

    // Security check
    if (!xarSecurityCheck('AddSiteContact')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitecontactTable = $xartable['sitecontact'];

    // Get next ID in table
    $nextId = $dbconn->GenId($sitecontactTable);

    // Insert the publication type
    $query = "INSERT INTO $sitecontactTable
             (xar_scid,
              xar_sctypename,
              xar_sctypedesc,
              xar_customtext,
              xar_customtitle,
              xar_optiontext,
              xar_webconfirmtext,
              xar_notetouser,
              xar_allowcopy,
              xar_usehtmlemail,
              xar_scdefaultemail,
              xar_scdefaultname,
              xar_scactive,
              xar_savedata,
              xar_permissioncheck,
              xar_termslink,
              xar_soptions)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $bindvars = array($nextId, (string)$sctypename, (string)$sctypedesc, (string)$customtext, $customtitle, (string)$optiontext,
             (string)$webconfirmtext, (string)$notetouser, (int)$allowcopy, (int)$usehtmlemail, (string)$scdefaultemail, (string)$scdefaultname, (int)$scactive,
             (int)$savedata,(int)$permissioncheck,$termslink,(string)$soptions);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Get ptid to return
    $sctypeid = $dbconn->PO_Insert_ID($sitecontactTable, 'xar_sctypeid');
    $args=array();
   // Let any hooks know that we have created a new user.
    $args['module'] = 'sitecontact';
    $args['itemtype'] = $sctypeid ;
    $args['itemid'] = $sctypeid ;
    // then call the create hooks
    $result = xarModCallHooks('item', 'create', $sctypeid, $args);

    $resultarray = array('sctypeid' => (int)$sctypeid,
                   'created' => 1);
    return $resultarray;
}

?>