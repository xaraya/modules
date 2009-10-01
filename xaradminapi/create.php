<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/* @author the Example module development team
 * @param  $args ['totalgained'] total kb gained in optimization
 * @returns int
 * @return sitetools item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function sitetools_adminapi_create($args)
{
extract($args);
    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set an appropriate error
    // message and return
    $invalid = array();
    if (!isset($totalgain)) {
        $$totalgain = 0;
    }
   $totalgain=round($totalgain,3);

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'SiteTools');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Security check - important to do this as early on as possible
    if (!xarSecurityCheck('AdminSiteTools')) {
        return;
    }
    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $sitetoolstable = $xartable['sitetools'];

    // Get next ID in table
    $nextId = $dbconn->GenId($sitetoolstable);
    // Add item

    $query = "INSERT INTO $sitetoolstable (
              xar_stid,
              xar_stgained)
              VALUES (?,?)";
    $bindvars = array($nextId, $totalgain);
    $result = &$dbconn->Execute($query,$bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Get the ID of the item that we inserted.
    $stid = $dbconn->PO_Insert_ID($sitetoolstable, 'xar_stid');
    // Let any hooks know that we have created a new item.

    $item = $args;
    $item['module'] = 'sitetools';
    $item['itemid'] = $stid;
    xarModCallHooks('item', 'create', $stid, $item);
    // Return the id of the newly created item to the calling process
    return $stid;
}

?>