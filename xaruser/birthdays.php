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
function dossier_user_birthdays($args)
{
    extract($args);
    
    if (!xarVarFetch('startdate', 'str', $startdate, $startdate, XARVAR_NOT_REQUIRED)) return;

    $data = xarModAPIFunc('dossier','user','menu');

    if (!xarSecurityCheck('PublicDossierAccess')) {
        return;
    }

    $contactlist = xarModAPIFunc('dossier',
                          'user',
                          'getallbirthdays',
                          array('startdate' => $startdate,
                                'numitems' => xarModGetVar('dossier', 'itemsperpage')));//TODO: numitems

    if (!isset($contactlist) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $displaytitle = xarModGetVar('dossier', 'displaytitle');
    $data['displaytitle'] = $displaytitle ? $displaytitle : xarML("DOSSIER - Active Public Projects");
    $data['contactlist'] = $contactlist;
    $data['pager'] = '';
    return $data;
}

?>
