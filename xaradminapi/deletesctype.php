<?php
/**
 * Site Contact
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
    
    //Hooks aren't doing their job?
    //Delete the DD object associated with this form -if it exists
    $moduleid= xarModGetIDFromName('sitecontact');    
    $objectinfo= xarModAPIFunc('dynamicdata','user','getobjectinfo',
                array('moduleid'=>$moduleid, 'itemtype'=>$scid));
    $objectid= $objectinfo['objectid'];      
    if (!empty($objectid)) {
                xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $objectid));
    }

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