<?php
/**
 * Site Contact
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
 * Delete sitecontact form definition
 *
 * @param $args['scid'] ID of the itemtype
 * @returns bool
 * @return true on success, false on failure
 */
function sitecontact_adminapi_deletesctype($args)
{
    // Get arguments from argument array
    extract($args);

     if (!isset($scid) || !is_numeric($scid) || $scid < 1) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Item type ID', 'admin', 'deletesctype',
                    'Sitecontact');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

    // Security check - we require ADMIN rights here
    if (!xarSecurityCheck('DeleteSiteContact')) return;

    // Load user API to obtain item information function
    if (!xarModAPILoad('sitecontact', 'user')) return;

    // Get current publication types
    $formtype = xarModAPIFunc('sitecontact','user','getcontacttypes', array('scid'=>$scid));
    if (!isset($formtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'Form type ID', 'admin', 'deletesctype',
                    'Sitecontact');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }


    //delete hooks
    $item['module'] = 'sitecontact';
    $item['itemid'] = $scid;
    $item['itemtype']=$scid;
    xarModCallHooks('item', 'delete', $scid, $item);

    //Delete the DD object associated with this form -if it exists
    $moduleid= xarModGetIDFromName('sitecontact');
    $forminfo = xarModAPIFunc('sitecontact','user','getcontacttypes', array('scid'=> $scid));
    $forminfo=$forminfo[0];
    $info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('name'=> $forminfo['sctypename']));
    $thisobject = xarModAPIFunc('dynamicdata','user','getobject', array('objectid' => $info['objectid']));

    $objectid= $info['objectid'];

    if (!empty($objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }
    xarModVars::delete('sitecontact', $forminfo['sctypename'].'_objectid');
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitecontactTable = $xartable['sitecontact'];

    // Delete the scform type
    $query = "DELETE FROM $sitecontactTable
            WHERE xar_scid = ?";
    $result =& $dbconn->Execute($query,array($scid));
    if (!$result) return false;


    return true;
}

?>