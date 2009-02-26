<?php
/**
 * Sitecontact Update
 *
 * @package Xaraya
 * @copyright (C) 2004-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Xarigami SiteContact Module
 * @copyright (C) 2007,2008 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */
/**
 * Update sitecontact itemtype
 *
 * @param $args['scid'] form Itemtype ID
 * @returns bool
 * @return true on success, false on failure
 */
function sitecontact_adminapi_updatesctype($args)
{
    // Get arguments from argument array
    extract($args);

    $invalid = array();
    if (!isset($scid) || !is_numeric($scid)) {
        $invalid[] = 'Sitecontact Form ID';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'updatesctype','Sitecontact');
         xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // Form names names *must* be lower-case for now and one word
    $sctypename = strtolower($sctypename);
    $sctypename= str_replace(' ','_',$sctypename);

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

    if (!isset($scdefaultname) || (trim($scdefaultname))=='') {
        $scdefaultname = xarModGetVar('sitecontact', 'scdefaultname');
        if (!isset($scdefaultname)) {
            $scdefaultname=xarModGetVar('mail','adminname');
        }
    }

    if (!isset($scactive)) {
        $scactive = xarModGetVar('sitecontact', 'scactive');
    }

    if (!$savedata || !isset($savedata)) {
        $savedata = (int)xarModGetVar('sitecontact', 'savedata');
    }
     if (!isset($permissioncheck) || !$savedata) {
        $permissioncheck = (int)xarModGetVar('sitecontact', 'permissioncheck');
    }
    if (!isset($termslink)) {
        $termslink = xarModGetVar('sitecontact', 'termslink');
    }
    if (!isset($soptions)) {
        $soptions = xarModGetVar('sitecontact', 'soptions');
    }
    if (xarModGetVar('sitecontact','defaultform') == $scid) {
        $scactive = 1;
    }
    // Security check
    if (!xarSecurityCheck('EditSiteContact',1)) return;

    // Load user API to obtain item information function
    if (!xarModAPILoad('sitecontact', 'user')) return;

    // Get current itemtypes
    $sctype = xarModAPIFunc('sitecontact','user','getcontacttypes', array('scid'=>$scid));

    if (!is_array($sctype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Sitcontact Form ID', 'admin', 'updatesctype',
                    'Sitecontact');
         xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

     // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitecontactTable = $xartable['sitecontact'];

    // Update the publication type (don't allow updates on name)
    $query = "UPDATE $sitecontactTable
            SET xar_sctypename =?,
                xar_sctypedesc =?,
                xar_customtext =?,
                xar_customtitle =?,
                xar_optiontext =?,
                xar_webconfirmtext =?,
                xar_notetouser =?,
                xar_allowcopy =?,
                xar_usehtmlemail =?,
                xar_scdefaultemail =?,
                xar_scdefaultname =?,
                xar_scactive =?,
                xar_savedata =?,
                xar_permissioncheck =?,
                xar_termslink=?,
                xar_soptions=?
            WHERE xar_scid = ?";
    $bindvars = array($sctypename, $sctypedesc, $customtext, $customtitle, $optiontext,
             $webconfirmtext, $notetouser, $allowcopy, $usehtmlemail, $scdefaultemail, $scdefaultname, $scactive,
             (int)$savedata,(int)$permissioncheck,$termslink,$soptions, $scid);
    $result =& $dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $item['module'] = 'sitecontact';
    $item['itemid'] = $scid;
    $item['sctypename'] = $sctypename;
    $item['itemtype'] = $scid;
    xarModCallHooks('item', 'updateconfig', $scid, $item);

    return true;
}

?>