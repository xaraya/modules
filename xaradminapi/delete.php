<?php
/**
 * Delete
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
 * Delete a response
 *
 * Standard function to delete a module item
 *
 * @param  $args ['scrid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function sitecontact_adminapi_delete($args)
{
    extract($args);
    if (!isset($scrid) || !is_numeric($scrid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'Response ID', 'admin', 'delete', 'Sitecontact');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $item = xarModAPIFunc('sitecontact','user','get', array('scrid' => $scrid));
    $scid=$item['scid'];
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('DeleteSitecontact',1)) {
        return;
    }
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $sitecontactResponseTable = $xartable['sitecontact_response'];

    $query = "DELETE FROM $sitecontactResponseTable WHERE xar_scrid = ?";

    /* The bind variable $exid is directly put in as a parameter. */
    $result = &$dbconn->Execute($query,array($scrid));

    if (!$result) return;
    $item['module'] = 'sitecontact';
    $item['itemid'] = $scrid;
    xarModCallHooks('item', 'delete', $scrid, $item);

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>