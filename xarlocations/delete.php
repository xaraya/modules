<?php
/**
 * Dossier Module - A Contact and Customer Service Management Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author Chad Kraeft
 */
function dossier_locations_delete($args)
{
    extract($args);
    
    if (!xarVarFetch('locationid', 'id', $locationid)) return;
    if (!xarVarFetch('contactid', 'isset', $contactid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('close', 'isset', $close, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'isset', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarModAPILoad('dossier', 'user')) return;
    
    $timeframeinfo = xarModAPIFunc('dossier',
                         'locations',
                         'getcontact',
                         array('contactid' => $contactid,
                            'locationid' => $locationid));
    
    if (!isset($timeframeinfo) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    if(empty($timeframeinfo['contactid'])) {
        $msg = xarML('Not associated with a contact to delete from #(1) item #(2)',
                    'dossier', xarVarPrepForDisplay($locationid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_ITEM',
                       new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('PublicDossierAccess', 1, 'Contact', "All:All:All:All")) {
        $msg = xarML('Not authorized to delete #(1) item #(2)',
                    'dossier', xarVarPrepForDisplay($locationid));
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException($msg));
        return;
    }

    if (empty($confirm)) {

        $contactinfo = xarModAPIFunc('dossier',
                              'user',
                              'get',
                              array('contactid' => $contactid));
                              
        xarModLoad('dossier','admin');
        $data = xarModAPIFunc('dossier','admin','menu');

        $data['locationid'] = $locationid;
        $data['contactid'] = $contactid;
        $data['contactinfo'] = $contactinfo;
        $data['item'] = $timeframeinfo;

        $data['authid'] = xarSecGenAuthKey();

        return $data;
    }
    if (!xarSecConfirmAuthKey()) return;
    
    if($close) {
        if (!xarModAPIFunc('dossier',
                         'locations',
                         'deletedata',
                         array('locationid' => $locationid,
                                'contactid' => $contactid))) {
            return;
        }
    } else {
        if (!xarModAPIFunc('dossier',
                         'locations',
                         'delete',
                         array('locationid' => $locationid))) {
            return;
        }
    }
    
    xarSessionSetVar('statusmsg', xarML('Location Deleted'));

    xarResponseRedirect(xarModURL('dossier', 'admin', 'display', array('contactid' => $timeframeinfo['contactid'], 'mode' => "locations")));

    return true;
}

?>
