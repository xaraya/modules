<?php
/**
 * Sitecontact Update
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
        $customtext= xarModVars::get('sitecontact', 'customtext');
    }
    if (!isset ($customtitle)) {
        $customtitle=xarModVars::get('sitecontact', 'customtitle');
    }
    if (!isset($optiontext)) {
        $optiontext= xarModVars::get('sitecontact', 'optiontext');
    }
    if (!isset($webconfirmtext)) {
        $webconfirmtext= xarModVars::get('sitecontact', 'webconfirmtext');
    }
    if (!isset($notetouser) || trim($notetouser)=='') {
        $notetouser = xarModVars::get('sitecontact', 'notetouser');
    }
    if (!isset($usehtmlemail)) {
        $usehtmlemail = xarModVars::get('sitecontact', 'usehtmlemail');
    }
    if (!isset($allowcopy)) {
        $allowcopy = xarModVars::get('sitecontact', 'allowcopy');
    }

    if (!isset($scdefaultemail)) {
       $scdefaultemail = xarModVars::get('sitecontact', 'scdefaultemail');
       if (!isset($scdefaultemail) || (trim($scdefaultemail)=='')) {
            $scdefaultemail=xarModVars::get('mail','adminmail');
       }
    }

    if (!isset($scdefaultname) || (trim($scdefaultname))=='') {
        $scdefaultname = xarModVars::get('sitecontact', 'scdefaultname');
        if (!isset($scdefaultname)) {
            $scdefaultname=xarModVars::get('mail','adminname');
        }
    }

    if (!isset($scactive)) {
        $scactive = xarModVars::get('sitecontact', 'scactive');
    }

    if (!$savedata || !isset($savedata)) {
        $savedata = (int)xarModVars::get('sitecontact', 'savedata');
    }
     if (!isset($permissioncheck) || !$savedata) {
        $permissioncheck = (int)xarModVars::get('sitecontact', 'permissioncheck');
    }
    if (!isset($termslink)) {
        $termslink = xarModVars::get('sitecontact', 'termslink');
    }
    if (!isset($soptions)) {
        $soptions = xarModVars::get('sitecontact', 'soptions');
    }
    if (xarModVars::get('sitecontact','defaultform') == $scid) {
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
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
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
    xarModCallHooks('item', 'update', $scid, $item);

    return true;
}

?>
