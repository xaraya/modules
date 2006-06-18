<?php
/**
 * Delete a response
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
 * Standard function to Delete an item
 *
 * @param  $ 'scrid' the id of the item to be deleted
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function sitecontact_admin_delete($args)
{
    extract($args);

    if (!xarVarFetch('scrid',    'id', $scrid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm',  'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!empty($objectid)) {
        $exid = $objectid;
    }
    $item = xarModAPIFunc('sitecontact','user','get', array('scrid' => $scrid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('DeleteSiteContact', 1, 'ContactForm', "$item[scid]:All:All")) {
        return;
    }
    /* Check for confirmation. */
    if (empty($confirm)) {
        $data['scrid'] = $scrid;
        $data['scid'] = $item['scid'];
        $data['itemid'] = xarML('Response ID');
        $data['username'] = xarVarPrepForDisplay($item['username']);

        /* Generate a one-time authorisation code for this operation */
        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;

    if (!xarModAPIFunc('sitecontact','admin','delete', array('scrid' => $scrid))) {
        return; // throw back
    }
    if (!isset($scid)) $scid=xarModGetVar('sitecontact','defaultform');
    
    xarResponseRedirect(xarModURL('sitecontact', 'admin', 'view',array('scid'=>$scid)));

    /* Return */
    return true;
}
?>